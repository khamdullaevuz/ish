<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class EmployeeFilter extends BaseQueryFilter
{
    public function company_id($value): Builder
    {
        if($value) {
            $this->builder->where('company_id', $value);
        }

        return $this->builder;
    }
}
