<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'product_id' => 'required|uuid|exists:products,id',
            'parent_id' => 'nullable|uuid|exists:comments,id',
            'body' => 'required|string',
            'rate' => 'nullable|integer|min:1|max:5',
        ];
    }
}