<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:50',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
