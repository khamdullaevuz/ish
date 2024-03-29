<?php

namespace App\Enums;

use App\Traits\EvolvedEnumsTrait;

enum UserRoles: string
{
    use EvolvedEnumsTrait;
    case ADMIN = 'admin';
    case USER = 'user';
}
