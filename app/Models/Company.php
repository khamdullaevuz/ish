<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'owner_name',
        'address',
        'email',
        'website',
        'phone',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
