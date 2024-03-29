<?php

namespace App\Http\Requests\Employee;

use App\Dtos\EmployeeDto;
use App\Enums\UserRoles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return user()->company_id !== null && user()->role === UserRoles::USER;
    }

    public function rules(): array
    {
        return [
            'passport' => [
                'required',
                'phone_number',
                Rule::unique('employees')
                    ->where('company_id', user()->company_id)
                    ->ignore($this->employee),
            ],
            'surname' => ['required'],
            'name' => ['required'],
            'patronymic' => ['required'],
            'position' => ['required'],
            'phone' => [
                'required',
                Rule::unique('employees')
                    ->where('company_id', user()->company_id)
                    ->ignore($this->employee),
            ],
            'address' => ['required'],
        ];
    }

    public function toDto(): EmployeeDto
    {
        return new EmployeeDto(
            passport: $this->validated('passport'),
            surname: $this->validated('surname'),
            name: $this->validated('name'),
            patronymic: $this->validated('patronymic'),
            position: $this->validated('position'),
            phone: $this->validated('phone'),
            address: $this->validated('address'),
        );
    }
}
