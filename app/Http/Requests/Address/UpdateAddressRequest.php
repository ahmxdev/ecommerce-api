<?php

namespace App\Http\Requests\Address;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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
            'district' => ['bail', 'required', 'string', 'max:255'],
            'street' => ['bail', 'required', 'string', 'max:255'],
            'building' => ['bail', 'required', 'string', 'max:255'],
            'floor' => ['bail', 'nullable', 'string', 'max:255'],
            'apartment' => ['bail', 'nullable', 'string', 'max:255'],
            'landmark' => ['bail', 'nullable', 'string', 'max:255'],
        ];
    }
}
