<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user() && auth()->user()->role === 'superAdmin';
    }

    public function rules()
    {
        $categoryId = $this->route('category')?->id;
        return [
            'name' => 'sometimes|string|max:255',
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($categoryId)],
            'parent_id' => 'nullable|uuid|exists:categories,id',
        ];
    }
}