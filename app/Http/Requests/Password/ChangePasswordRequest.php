<?php

namespace App\Http\Requests\Password;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ChangePasswordRequest
 *
 * @package App\Http\Requests
 */
class ChangePasswordRequest extends FormRequest
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
            'old_password' => [
                'required',
                'min:' . config('const.user.password.length'),
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, auth()->user()->password)) {
                        $fail(translate('validation.restore.password.old.match'));
                    }
                },
            ],
            'new_password' => [
                'required',
                'min:' . config('const.user.password.length'),
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'old_password.required' => translate('validation.restore.password.old.required'),
            'old_password.min' => translate('validation.restore.password.old.min'),
            'new_password.required' => translate('validation.restore.password.new.required'),
            'new_password.min' => translate('validation.restore.password.new.min'),
        ];
    }
}
