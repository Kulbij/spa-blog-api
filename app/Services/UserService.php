<?php

namespace App\Services;

use App\Models\User;
use App\Models\Settings;
use App\DataFinders\CompanyFinder;
use Illuminate\Support\Facades\Hash;
use App\Mail\Factories\JobMailFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\Users\UserResource;

/**
 * Class UserService
 *
 * @package App\Services
 */
class UserService
{
    /**
     * @var CompanyFinder
     */
    private CompanyFinder $finder;

    /**
     * CompanyHandler constructor.
     *
     * @param NotificationService $notificationService
     * @param CompanyFinder $finder
     */
    public function __construct()
    {
        
    }

    /**
     * @param string $id
     * @param array $data
     *
     * @return UserResource
     * @throws ValidationException
     */
    public function update(array $data): UserResource
    {
        $user = auth()->user();

        $user->fill($data);

        try {
            $user->save();
        } catch (\Exception $exception) {
            LogService::exception($exception->getMessage(), $exception, 'E_COMPANY_UPDATE');
        }

        LogService::info('User update', __CLASS__ . ':' . __FUNCTION__, 'I_USER_UPDATE', [
            'userid' => $user->id,
        ]);

        return new UserResource($user);
    }

    /**
     * @param $data
     *
     * @return null|User
     */
    public function create($data): ?User
    {
        try {
            if (!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }

            return User::create($data);
        } catch (\Exception $exception) {
            LogService::exception($exception->getMessage(), $exception, 'E_COMPANY_REGISTER');

            return null;
        }
    }
}
