<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['bail', 'required', 'numeric', 'decimal:0,2', 'min:0'],
            'slug' => ['bail', 'required', 'string', Rule::unique('products', 'slug')],
            'stock' => ['bail', 'required', 'integer', 'min:0'],
            'brand_id' => ['bail', 'required', Rule::exists('brands', 'id')],
            'categories' => ['bail', 'required', 'array', 'min:1'],
            'categories.*' => ['bail', 'integer', 'distinct', Rule::exists('categories', 'id')],
            /* 'IMAGES' => [....] */
        ];
    }
}
