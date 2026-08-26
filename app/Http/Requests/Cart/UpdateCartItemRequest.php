<?php

namespace App\Http\Requests\Cart;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCartItemRequest extends FormRequest
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
            'quantity' => ['bail', 'required', 'integer', 'min:1'],
        ];
    }


    protected function after(): array
    {
        return [
            function (Validator $validator) {
                $cartItem = $this->route('cartItem');
                $product = $cartItem->product;
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
