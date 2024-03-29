<?php

namespace App\Dtos;

class EmployeeDto extends BaseDto
{
    public function __construct(
        public string $passport,
        public string $surname,
        public string $name,
        public string $patronymic,
        public string $position,
        public string $phone,
        public string $address,
    )
    {
    }
}
