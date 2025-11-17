<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
             'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'shipping_address' => 'required|string',
            'order_address' => 'required|string',
            'order_email' => 'required|email',
            'order_status' => 'sometimes|in:pending,processing,shipped,delivered,cancelled',
            'order_details' => 'required|array|min:1',
            'order_details.*.product_id' => 'required|exists:products,id',
            'order_details.*.price' => 'required|numeric|min:0',
            'order_details.*.sku' => 'required|string|max:100',
            'order_details.*.quantity' => 'required|integer|min:1',
        ];
    }
    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer is required.',
            'customer_id.exists' => 'Selected customer does not exist.',
            'amount.required' => 'Order amount is required.',
            'amount.min' => 'Order amount cannot be negative.',
            'shipping_address.required' => 'Shipping address is required.',
            'order_address.required' => 'Order address is required.',
            'order_email.required' => 'Order email is required.',
            'order_email.email' => 'Order email must be a valid email address.',
            'order_details.required' => 'Order details are required.',
            'order_details.min' => 'At least one order item is required.',
            'order_details.*.product_id.required' => 'Product is required for each item.',
            'order_details.*.product_id.exists' => 'One or more selected products do not exist.',
            'order_details.*.price.required' => 'Price is required for each item.',
            'order_details.*.price.min' => 'Price cannot be negative.',
            'order_details.*.quantity.required' => 'Quantity is required for each item.',
            'order_details.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}
