<?php

namespace App\Http\Requests\User;

use App\Dtos\UserDto;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required'],
            'email' => ['required', 'email', 'max:254'],
            'password' => ['required'],
            'company_id' => 'required|exists:companies,id',
        ];
    }

    public function toDto(): UserDto
    {
        return new UserDto(
            name: $this->validated('name'),
            email: $this->validated('email'),
            password: $this->validated('password'),
            company_id: $this->validated('company_id'),
        );
    }
}
