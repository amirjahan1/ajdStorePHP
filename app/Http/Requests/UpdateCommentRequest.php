<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && in_array(auth()->user()->role, ['admin', 'superAdmin']);
    }

    public function rules()
    {
        return [
            'is_approved' => 'required|boolean',
        ];
    }
}