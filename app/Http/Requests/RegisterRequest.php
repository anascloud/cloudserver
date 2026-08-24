<?php

namespace App\Http\Requests;


class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    // RegisterRequest.php
    public function rules()
    {
        return [
            'firstName' => 'required|string|max:255',
            'mobileNo' => 'required|numeric|digits_between:10,15',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed', // The "confirmed" rule automatically checks the password_confirmation field
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
     * @return array
     * Custom validation message
     */
    public function messages()
    {
        return [
            'firstName.required' => 'Please give your name',
            'firstName.max' => 'Please give your name between 50 characters',
            'email.required' => 'Please give your email',
            'email.unique' => 'User already exists by this email, please try with another email.',
            'password.required' => 'Please give your password',
        ];
    }
}
