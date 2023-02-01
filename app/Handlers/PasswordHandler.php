<?php

namespace App\Handlers;

use Exception;
use App\Models\User;
use App\Services\LogService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Services\NotificationService;
use App\Mail\Factories\JobMailFactory;
use Illuminate\Validation\ValidationException;

/**
 * Class PasswordHandler
 *
 * @package App\Handlers
 */
class PasswordHandler
{
    public const TYPE_RESET = 'reset';
    public const TYPE_CREATE = 'create';

    public const DATABASE_CREATE = 'password_create';
    public const DATABASE_RESET = 'password_resets';

    /**
     * @var int
     */
    private int $tokenSetExpiredTime;

    /**
     * @var int
     */
    private int $tokenRestoreExpiredTime;

    /**
     * @var NotificationService
     */
    private NotificationService $notificationService;

    /**
     * PasswordHandler constructor.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->tokenSetExpiredTime = config('const.user.password.set.token.expired');
        $this->tokenRestoreExpiredTime = config('const.user.password.restore.token.expired');

        $this->notificationService = $notificationService;
    }

    /**
     * @param  string  $email
     * @param  string  $type
     *
     * @return string
     */
    public function createToken(string $email, string $type = self::TYPE_RESET): string
    {
        $table = $this->getDbName($type);

        $isToken = DB::table($table)->where([
            ['email', $email],
            [
                'created_at',
                '>',
                Carbon::now()
                    ->subHours(
                        self::TYPE_CREATE === $table ?
                            $this->tokenSetExpiredTime :
                            $this->tokenRestoreExpiredTime
                    ),
            ],
        ])->first();

        if ($isToken) {
            return $isToken->token;
        }

        $token = sha1($email . Carbon::now()->format('Y-m-d-h-i-s'));

        DB::table($table)->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        return $token;
    }

    /**
     * @param  string  $email
     */
    public function sendRestoreLink(string $email): void
    {
        try {
            $this->sendPasswordLinkByType($email);
        } catch (\Exception $exception) {
            LogService::exception($exception->getMessage(), $exception, 'E_PASSWORD_SEND_LINK_RESTORE');
        }
    }

    /**
     * @param  string  $email
     */
    public function sendCreateLink(string $email): void
    {
        try {
            $this->sendPasswordLinkByType(
                $email,
                self::TYPE_CREATE,
                NotificationService::EVENT_PASSWORD_CREATE
            );
        } catch (\Exception $exception) {
            LogService::exception($exception->getMessage(), $exception, 'E_PASSWORD_SEND_LINK_CREATE');
        }
    }

    /**
     * @param  array  $data
     *
     * @return bool
     * @throws ValidationException
     */
    public function restore(array $data): bool
    {
        return $this->setPassword($data['token'], $data['password']);
    }

    /**
     * @param  array  $data
     *
     * @return bool
     * @throws ValidationException
     */
    public function set(array $data): bool
    {
        return $this->setPassword($data['token'], $data['password'], self::TYPE_CREATE);
    }

    /**
     * @param  array  $data
     *
     * @return bool
     */
    public function change(array $data): bool
    {
        try {
            auth()->user()->password = Hash::make($data['new_password']);

            return auth()->user()->save();
        } catch (Exception $exception) {
            LogService::exception($exception->getMessage(), $exception, 'E_EMPLOYEE_PASSWORD_CHANGE', [
                'employeeId' => auth()->user()->id,
            ]);

            return false;
        }
    }

    /**
     * @param  string  $token
     * @param  string  $password
     * @param string $type
     *
     * @return bool
     * @throws ValidationException
     */
    private function setPassword(string $token, string $password, string $type = self::TYPE_RESET): bool
    {
        $table = $this->getDbName($type);

        $resetDataQuery = DB::table($table)->where('token', '=', $token);

        if ($resetDataQuery->count() > 0) {
            $resetData = $resetDataQuery->first();

            if (!$this->isTokenExpired($resetData, $type)) {
                throw ValidationException::withMessages(['token' => translate('token.expired')]);
            }

            $user = User::where('email', '=', $resetData->email)->firstOrFail();

            $user->update([
                'password' => Hash::make($password),
            ]);

            $resetDataQuery->delete();

            return true;
        }

        return false;
    }

    /**
     * @param  string  $type
     *
     * @return string
     */
    private function getDbName(string $type = self::TYPE_RESET): string
    {
        if (self::TYPE_CREATE === $type) {
            return self::DATABASE_CREATE;
        }

        return self::DATABASE_RESET;
    }

    /**
     * @param  \stdClass  $token
     * @param  string  $type
     *
     * @return bool
     */
    private function isTokenExpired(\stdClass $token, string $type = self::TYPE_RESET): bool
    {
        $expiredIn = Carbon::parse($token->created_at)
            ->addHours(self::TYPE_CREATE === $type ? $this->tokenSetExpiredTime : $this->tokenRestoreExpiredTime)
            ->timestamp;

        return Carbon::now()->timestamp <= $expiredIn;
    }

    public function clearTokens(): void
    {
        foreach ([self::DATABASE_RESET, self::DATABASE_CREATE] as $table) {
            $now = Carbon::now()
                ->subHours(
                    self::DATABASE_CREATE === $table ?
                        $this->tokenSetExpiredTime :
                        $this->tokenRestoreExpiredTime
                );

            DB::table($table)->where('created_at', '<', $now)->delete();
        }
    }

    /**
     * @param  string  $token
     * @param string $type
     *
     * @return string
     */
    private function createPasswordLink(string $token, string $type = self::TYPE_RESET): string
    {
        $link = config('const.frontend.url', null);

        if (null === $link) {
            $message = 'Link to frontend is not specified';
            LogService::exception($message, __CLASS__ .':'.__FUNCTION__, 'E_EMPLOYEE_PASSWORD_CREATE_LINK');

            throw new \InvalidArgumentException($message);
        }

        if (self::TYPE_CREATE === $type) {
            return $link . config('const.user.password.set.link') . "/{$token}";
        }

        return $link . config('const.user.password.restore.link') . "/{$token}";
    }

    /**
     * @param $email
     * @param string $type
     * @param string $event
     */
    private function sendPasswordLinkByType(
        $email,
        string $type = self::TYPE_RESET,
        string $event = NotificationService::EVENT_PASSWORD_RESET
    ): void {
        $token = $this->createToken($email, $type);

        $this->notificationService->notify(new JobMailFactory(), $event, [
            'link' => $this->createPasswordLink($token, $type),
            'emails' => [$email],
            'token' => $token,
        ]);
    }
}
