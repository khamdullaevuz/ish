<?php

namespace App\Dtos;

class UserDto extends BaseDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public int $company_id,
    )
    {
    }
}
