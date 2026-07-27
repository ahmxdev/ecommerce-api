<?php

namespace App\Http\Requests\Category;

use App\Models\Category;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCategoryRequest extends FormRequest
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
            'name' => ['bail', 'required', 'max:255', Rule::unique('categories', 'name')->ignore($this->category)],
            'parent_id' => ['bail', 'nullable', Rule::exists('categories', 'id')]
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (is_null($this->parent_id)) {
                    return;
                }

                $category = $this->route('category');
                $parent = Category::find($this->parent_id);
                while ($parent) {
                    if ($parent->id == $category->id) {
                        $validator->errors()->add(
                            'parent_id',
                            'A category cannot be a child of its children'
                        );
                        break;
                    } else {
                        $parent = $parent->parent;
                    }
                }
            },
        ];
    }
}
