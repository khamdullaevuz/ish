<?php

namespace App\Http\Requests\Company;

use App\Dtos\CompanyDto;
use App\Enums\UserRoles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return user()->company_id === null && user()->role === UserRoles::ADMIN;
    }

    public function rules(): array
    {
        return [
            'name' => ['required'],
            'owner_name' => ['required'],
            'address' => ['required'],
            'email' => ['required', 'email', 'max:254'],
            'website' => ['required'],
            'phone' => [
                'required',
                'phone_number',
                Rule::unique('companies'),
            ],
        ];
    }

    public function toDto(): CompanyDto
    {
        return new CompanyDto(
            name: $this->validated('name'),
            owner_name: $this->validated('owner_name'),
            address: $this->validated('address'),
            email: $this->validated('email'),
            website: $this->validated('website'),
            phone: $this->validated('phone'),
        );
    }
}
