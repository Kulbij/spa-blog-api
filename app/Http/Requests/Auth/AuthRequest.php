<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AuthRequest
 *
 * @package App\Http\Requests
 */
class AuthRequest extends FormRequest
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
            'email' => [
                'required',
                'email',
            ],
            'password' => [
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
            'email.required' => ('validation.auth.email.required'),
            'email.email' => ('validation.auth.email.email'),
            'email.exists' => ('validation.auth.email.exists'),

            'password.required' => ('validation.auth.password.required'),
            'password.min' => ('validation.auth.password.min'),
        ];
    }
}
