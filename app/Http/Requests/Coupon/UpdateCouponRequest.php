<?php

namespace App\Http\Requests\Coupon;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
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
                'bail',
                'required',
                'string',
                'max:20',
                'unique:coupons,code,' . $this->coupon->id,
            ],

            'discount_percentage' => [
                'bail',
                'required',
                'integer',
                'between:1,100',
            ],

            'is_active' => [
                'bail',
                'required',
                'boolean',
            ],

            'expires_at' => [
                'bail',
                'required',
                'date',
                'after:now',
            ],
        ];
    }
}
