<?php

namespace App\Http\Requests\Users;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateUserRequest
 *
 * @package App\Http\Requests
 */
class UpdateUserRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'min:2',
            ],
            'last_name' => [
                'required',
                'string',
                'min:2'
            ],
            'middle_name' => [
                'string',
                'min:2',
                'nullable',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique(User::getTableName())->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })->ignore(auth()->user()->id),
            ],
            'phone' => [
                'required', 'min:12', 'max:20'
            ],
            'description' => [
                'string',
                'nullable',
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => ('validation.user.name.required'),
            'name.string' => ('validation.user.name.string'),
            'name.min' => ('validation.user.name.min'),

            'last_name.required' => ('validation.user.surname.required'),
            'last_name.string' => ('validation.user.surname.string'),
            'last_name.min' => ('validation.user.surname.min'),

            'middle_name.string' => ('validation.user.middle-name.string'),
            'middle_name.min' => ('validation.user.middle-name.min'),

            'email.required' => ('validation.user.email.required'),
            'email.email' => ('validation.user.email.email'),

            'phone.min' => ('validation.user.phone.min'),
            'phone.max' => ('validation.user.phone.max'),
            'phone.required' => ('validation.user.phone.required'),

            'description.string' => ('validation.user.description.string'),
            'description.nullable' => ('validation.user.description.nullable'),
        ];
    }
}
