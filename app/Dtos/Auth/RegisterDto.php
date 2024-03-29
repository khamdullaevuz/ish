<?php

namespace App\Dtos\Auth;

use App\Dtos\BaseDto;

class RegisterDto extends BaseDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    )
    {
    }
}
