<?php

namespace App\Http\Requests\Address;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
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
            'country' => ['bail', 'required', 'string', 'max:255'],
            'state' => ['bail', 'required', 'string', 'max:255'],
            'city' => ['bail', 'required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'street' => ['bail', 'required', 'string', 'max:255'],
            'building' => ['required', 'string', 'max:255'],
            'floor' => ['nullable', 'string', 'max:255'],
            'apartment' => ['nullable', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:255'],
        ];
    }
}
