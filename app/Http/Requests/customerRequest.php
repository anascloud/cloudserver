<?php
// app/Http/Requests/customerRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class customerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id'); // works for update route {id}

        return [
            'first_name'          => 'required|string|max:255',
            'last_name'           => 'nullable|string|max:255',
            'email'               => [
                'required', 'email', 'max:255',
                Rule::unique('customer', 'email')->ignore($id),
            ],
            'phone'               => 'required|string|max:50',
            'company'             => 'nullable|string|max:255',
            'street'              => 'nullable|string|max:255',
            'house'               => 'nullable|string|max:255',
            'zip'                 => 'nullable|string|max:20',
            'city'                => 'nullable|string|max:255',
            'state'               => 'nullable|string|max:255',
            'country'             => 'nullable|integer|exists:countries,id',
            'account_no'          => 'nullable|string|max:100',
            'bank_name'           => 'nullable|string|max:255',
            'currency_id'         => 'nullable|integer|exists:currencies,id',
            'payment_terms'       => 'nullable|integer',
            'chart_of_account_id' => 'nullable|integer|exists:chart_of_accounts,id',
            'residence_permit_no' => 'nullable|string|max:100',
            'photo'               => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
            'cover_photo'         => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:8192',
        ];
    }
}
