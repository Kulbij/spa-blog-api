<?php

namespace App\Http\Requests\Category;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateCategoryRequest
 *
 * @package App\Http\Requests
 */
class UpdateCategoryRequest extends FormRequest
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
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => ('validation.category.name.required'),
            'name.string' => ('validation.category.name.string'),
            'name.min' => ('validation.category.name.min'),
        ];
    }
}
