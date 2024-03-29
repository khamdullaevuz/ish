<?php

namespace App\Dtos;

class CompanyDto extends BaseDto
{
    public function __construct(
        public string $name,
        public string $owner_name,
        public string $address,
        public string $email,
        public string $website,
        public string $phone
    )
    {
    }
}
