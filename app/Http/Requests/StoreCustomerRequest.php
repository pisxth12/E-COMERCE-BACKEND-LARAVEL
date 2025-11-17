<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:customers',
            'password' => 'required|min:6',
            'full_name' => 'required|string|max:255',
            'billing_address' => 'required|string',
            'default_shipping_address' => 'required|string',
            'country' => 'required|string|max:100',
            'phone' => 'required|string|max:20',

        ];
    }
    public function messages(): array
    {
        return [
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'full_name.required' => 'Full name is required.',
            'billing_address.required' => 'Billing address is required.',
            'default_shipping_address.required' => 'Shipping address is required.',
            'country.required' => 'Country is required.',
            'phone.required' => 'Phone number is required.',
        ];
    }
}
