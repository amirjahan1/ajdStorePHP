<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize()
    {
       
        // فقط سوپرادمین اجازه دارد
        return auth()->user() && auth()->user()->role === 'superAdmin';
    }

    public function rules()
    {
    
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'parent_id' => 'nullable|uuid|exists:categories,id',
        ];
    }
}