<?php

namespace App\Http\Requests\Post;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdatePostRequest
 *
 * @package App\Http\Requests
 */
class UpdatePostRequest extends FormRequest
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
            'description' => [
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
            'name.required' => ('validation.post.name.required'),
            'name.string' => ('validation.post.name.string'),
            'name.min' => ('validation.post.name.min'),

            'description.required' => ('validation.post.description.required'),
            'description.string' => ('validation.post.description.string'),
            'description.min' => ('validation.post.description.min'),
        ];
    }
}
