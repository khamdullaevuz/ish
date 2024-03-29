<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

abstract class CustomRule implements Rule
{
    protected string $alias = '';

    public function __toString()
    {
        return $this->alias;
    }
}
