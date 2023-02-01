<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\ResetPasswordService;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Password\ChangePasswordRequest;
use App\Http\Requests\Password\RestorePasswordRequest;
use App\Http\Requests\Password\UpdateSetPasswordRequest;
use App\Http\Requests\Password\UpdateRestorePasswordRequest;

/**
 * Class ResetPasswordController
 *
 * @package App\Http\Controllers\Api
 */
class ResetPasswordController extends BaseController
{
    /**
     * @var ResetPasswordService
     */
    private ResetPasswordService $resetPasswordService;

    /**
     * ResetPasswordService constructor.
     *
     * @param  ResetPasswordService  $resetPasswordService
     */
    public function __construct(ResetPasswordService $resetPasswordService)
    {
        $this->resetPasswordService = $resetPasswordService;
    }

    /**
     * @param  RestorePasswordRequest  $request
     *
     * @return JsonResponse
     */
    public function forgot(RestorePasswordRequest $request): JsonResponse
    {
        $this->resetPasswordService->sendRestoreLink($request->email);

        return $this->sendResponse([
            'message' => translate('password.link.send'),
        ]);
    }

    /**
     * @param  UpdateRestorePasswordRequest  $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function restore(UpdateRestorePasswordRequest $request): JsonResponse
    {
        if ($this->resetPasswordService->restore($request->validated())) {
            return $this->sendResponse([
                'message' => translate('password.restore.success'),
            ]);
        }

        return $this->sendError(translate('password.restore.error'), [], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param  UpdateSetPasswordRequest  $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function set(UpdateSetPasswordRequest $request): JsonResponse
    {
        if ($this->resetPasswordService->set($request->validated())) {
            return $this->sendResponse([
                'message' => translate('password.set.success'),
            ]);
        }

        return $this->sendError(translate('password.set.error'), [], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param  ChangePasswordRequest  $request
     *
     * @return JsonResponse
     */
    public function change(ChangePasswordRequest $request): JsonResponse
    {
        if ($this->resetPasswordService->change($request->validated())) {
            return $this->sendResponse([
                'message' => translate('password.change.success')
            ]);
        }

        return $this->sendError(
            translate('password.change.error'),
            [],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
