<?php

namespace App\Http\Requests\Password;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateRestorePasswordRequest
 *
 * @package App\Http\Requests
 */
class UpdateRestorePasswordRequest extends FormRequest
{
    /**
     * Determine if the employee is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'token' => [
                'required',
                Rule::exists('password_resets', 'token'),
            ],
            'password' => [
                'required',
                'min:' . config('const.user.password.length'),
            ],
            'password_confirmation' => [
                'required',
                'min:' . config('const.user.password.length'),
                'same:password',
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'token.required' => translate('validation.restore.token.required'),
            'token.exists' => translate('validation.restore.token.exists'),

            'password.required' => translate('validation.restore.password.required'),
            'password.min' => translate('validation.restore.password.min'),

            'password_confirmation.required' => translate('validation.restore.password-confirmation.required'),
            'password_confirmation.min' => translate('validation.restore.password-confirmation.min'),
            'password_confirmation.same' => translate('validation.restore.password.same'),
        ];
    }
}
