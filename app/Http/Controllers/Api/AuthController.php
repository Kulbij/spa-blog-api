<?php


namespace App\Http\Controllers\Api;

use App\Services\LogService;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Resources\Employees\EmployeeResource;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Requests\Auth\AuthRequest;

use Illuminate\Support\Facades\Hash;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers\Api
 */
class AuthController extends BaseController
{
    public $loginAfterSignUp = true;
    
    /**
     * @param  AuthRequest  $request
     *
     * @return JsonResponse
     */
    public function login(AuthRequest $request): JsonResponse
    {
        try {
            if (!$token = JWTAuth::attempt($request->validated())) {
                return $this->sendError(('validation.unauthorised'), [], Response::HTTP_UNAUTHORIZED);
            }
        } catch (JWTException $exception) {
            LogService::exception($exception->getMessage(), $exception, 'E_AUTH_LOGIN');

            return $this->sendError($exception->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->getAuthData($token);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);

            return $this->sendResponse([
                'message' => ('auth.logout.success')
            ]);
        } catch (JWTException $exception) {
            LogService::exception($exception->getMessage(), $exception, 'E_AUTH_LOGOUT');

            return $this->sendError($exception->getMessage(), []);
        }
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->getAuthData(auth()->refresh());
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

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function userProfile(): JsonResponse
    {
        return $this->sendResponse(new EmployeeResource(auth()->user()));
    }
}
