<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class BaseQueryFilter
{
    public function __construct(protected Builder $builder, protected Request $request)
    {
    }

    public function apply(): Builder
    {
        foreach ($this->request->all() as $name => $value) {
            if (method_exists($this, $name)) {
                $this->$name($value);
            }
        }

        return $this->builder;
    }
}
