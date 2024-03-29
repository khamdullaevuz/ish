<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static Model|Builder|static company()
 * @method static Model|Builder|static orCompany()
 * @property string $is_company_key
 */
trait IsCompanyTrait
{
    public function isCompany(): bool
    {
        return $this->{$this->getIsCompanyKey()} === $this->getIsCompanyValue();
    }

    private function getIsCompanyKey(): string
    {
        return property_exists($this, 'is_company_key') ? $this->is_company_key : 'company_id';
    }

    private function getIsCompanyValue()
    {
        return user_company()?->id;
    }

    public function scopeCompany(Builder $query): Builder
    {
        return $this->getIsCompanyValue() === null ? $query : $query->where($this->getIsCompanyKey(), $this->getIsCompanyValue());
    }

    public function scopeOrCompany(Builder $query): Builder
    {
        return $this->getIsCompanyValue() === null ? $query : $query->orWhere($this->getIsCompanyKey(), $this->getIsCompanyValue());
    }
}
