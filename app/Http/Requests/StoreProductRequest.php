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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' =>  ['required', 'string', 'max:255'],
            'price' =>  ['required', 'integer'],
            'code' =>  ['nullable', 'string', 'max:255'],
            'unit' =>  ['required', 'integer', 'exists:units,id'],
            'brand' =>  ['nullable', 'integer', 'exists:brands,id'],
            'min_stock' =>  ['nullable', 'integer'],
            'notes' =>  ['nullable', 'string'],
            'image' =>  ['nullable', 'image', 'max:4096'],
            'status' =>  ['required', 'boolean'],
            'categories' =>  ['nullable', 'array'],
            'categories.*' =>  ['nullable', 'integer', 'exists:categories,id'],
        ];
    }
}
