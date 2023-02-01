<?php

namespace App\Http\Requests\Password;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateSetPasswordRequest
 *
 * @package App\Http\Requests
 */
class UpdateSetPasswordRequest extends FormRequest
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
                Rule::exists('password_create', 'token'),
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
            'token.required' => translate('validation.set.token.required'),
            'token.exists' => translate('validation.set.token.exists'),

            'password.required' => translate('validation.set.password.required'),
            'password.min' => translate('validation.set.password.min'),
            'password.confirmed' => translate('validation.set.password.confirmed'),

            'password_confirmation.required' => translate('validation.restore.password-confirmation.required'),
            'password_confirmation.min' => translate('validation.restore.password-confirmation.min'),
            'password_confirmation.same' => translate('validation.restore.password.same'),
        ];
    }
}
