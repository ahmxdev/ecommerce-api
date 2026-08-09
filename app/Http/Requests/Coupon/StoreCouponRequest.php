<?php

namespace App\Http\Requests\Coupon;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
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
            'code' => [
                'required',
                'string',
                'max:20',
                'unique:coupons,code',
            ],
            'discount_percentage' => [
                'required',
                'integer',
                'between:1,100',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
            'expires_at' => [
                'required',
                'date',
                'after:now',
            ],
        ];
    }
}
