<?php

namespace App\Http\Requests\Cart;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreCartItemRequest extends FormRequest
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

    protected function prepareForValidation(): void
    {
        // default quantity value is 1
        $this->merge([
            'quantity' => $this->input('quantity', 1),
        ]);
    }
    public function rules(): array
    {
        return [
            'product_id' => ['bail', 'required', 'integer', Rule::exists('products', 'id')],
            'quantity' => ['bail', 'required', 'integer', 'min:1'],
        ];
    }


    protected function after(): array
    {
        return [
            function (Validator $validator) {
                $product = Product::find($this->product_id);
                $stock = $product->stock;
                if ($this->quantity > $stock) {
                    $validator->errors()->add(
                        'quantity',
                        'this quantity is not valid'
                    );
                }
            },
        ];
    }
}
