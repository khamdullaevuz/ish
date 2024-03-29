<?php

namespace App\Models;

use App\Traits\IsCompanyTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes, IsCompanyTrait;

    protected $fillable = [
        'passport',
        'surname',
        'name',
        'patronymic',
        'position',
        'phone',
        'address',
        'company_id'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
