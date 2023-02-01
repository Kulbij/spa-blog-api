<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Response;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Users\UserResource;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Requests\Users\RegistrationUserRequest;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\Api
 */
class UserController extends BaseController
{
    public $loginAfterSignUp = true;

    /**
     * @var UserService
     */
    private UserService $userService;

    /**
     * UserController constructor.
     *
     * @param  UserService  $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateUserRequest  $request
     *
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        return $this->sendResponse($this->userService->update($request->validated()));
    }

    /**
     * @param  RegistrationUserRequest  $request
     *
     * @return JsonResponse
     */
    public function register(RegistrationUserRequest $request): JsonResponse
    {
        if (!$user = $this->userService->create($request->validated())) {
            return $this->sendError(
                ('user.server.error'),
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $token = auth()->login($user);

        return $this->getAuthData($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function userProfile(): JsonResponse
    {
        return $this->sendResponse(
            new UserResource(auth()->user())
        );
    }

    /**
     * Get the token array structure.
     *
     * @param  string  $token
     *
     * @return JsonResponse
     */
    protected function getAuthData(string $token): JsonResponse
    {
        return $this->sendResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ]);
    }
}
