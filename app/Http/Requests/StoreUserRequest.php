<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'sku' => 'required|string|unique:products,sku,' . $this->product->id,
        'category' => 'required|string|max:255',
        'brand' => 'required|string|max:255', // ← This is required!
        'status' => 'sometimes|in:active,inactive,out_of_stock',
        'specifications' => 'sometimes|array',

        ];
    }
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'This email is already taken',
            'phone.required' => 'Phone number is required',
            'department.required' => 'Department is required',
            'role.required' => 'Role is required',
            'status.required' => 'Status is required',
        ];
    }
}
