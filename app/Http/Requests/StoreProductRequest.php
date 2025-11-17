<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'sku' => 'required|unique:products|max:100',
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'descriptions' => 'nullable|string',
            'thumbnail' => 'nullable|string|max:500',
            'image' => 'nullable|string|max:500',
            'stock' => 'required|integer|min:0',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'option_ids' => 'nullable|array',
            'option_ids.*' => 'exists:options,id',
        ];
    }

    public function messages(): array
    {
        return [
            'sku.required' => 'SKU is required.',
            'sku.unique' => 'This SKU already exists.',
            'name.required' => 'Product name is required.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price cannot be negative.',
            'stock.required' => 'Stock quantity is required.',
            'stock.integer' => 'Stock must be a whole number.',
            'stock.min' => 'Stock cannot be negative.',
            'category_ids.*.exists' => 'One or more selected categories do not exist.',
            'option_ids.*.exists' => 'One or more selected options do not exist.',
        ];
    }
}
