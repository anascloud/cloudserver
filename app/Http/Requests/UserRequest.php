<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'firstName' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email' . ($this->method() === 'PUT' ? ',' . $this->route('id') : ''),
            'password' => 'required|string|min:6|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'integer|exists:roles,id',
        ];
    }
}
