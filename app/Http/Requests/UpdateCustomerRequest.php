<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
        $customerId = $this->route('customer');
        return [
             'email' => 'sometimes|required|email|unique:customers,email,' . $customerId,
            'password' => 'sometimes|min:6',
            'full_name' => 'sometimes|required|string|max:255',
            'billing_address' => 'sometimes|required|string',
            'default_shipping_address' => 'sometimes|required|string',
            'country' => 'sometimes|required|string|max:100',
            'phone' => 'sometimes|required|string|max:20',
            
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered.',
            'password.min' => 'Password must be at least 6 characters.',
        ];
    } 
}
