<?php

namespace App\Http\Requests\Password;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RestorePasswordRequest
 *
 * @package App\Http\Requests
 */
class RestorePasswordRequest extends FormRequest
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
                Rule::exists(User::getTableName(), 'email')->whereNull('deleted_at'),
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'email.required' => translate('validation.auth.email.required'),
            'email.email' => translate('validation.auth.email.email'),
            'email.exists' => translate('validation.auth.email.exists'),
        ];
    }
}
