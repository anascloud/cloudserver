<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255|unique:roles,name', // Ensure the role name is unique
            'permissions' => 'nullable|max:1000', // Optional description with a maximum length
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Please provide a role name.',
            'name.max' => 'Role name must not exceed 255 characters.',
            'name.unique' => 'This role name already exists. Please choose a different name.',
            'description.max' => 'Role description must not exceed 1000 characters.',
        ];
    }
}