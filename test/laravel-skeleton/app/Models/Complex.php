<?php

namespace App\Models;

use App\Casts\UpperCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Complex extends Model
{
    protected $table = 'complex_model_table';

    protected $casts = [
        'json' => 'json',
        'jsonb' => 'json',
        'year' => 'int',
        'casted_uppercase_string' => UpperCast::class,
        'immutableDateTime' => 'immutable_date',
        'immutableDate' => 'immutable_datetime',
        'immutableCustomDateTime' => 'immutable_custom_datetime',
    ];

    public function complexRelationships(): HasMany
    {
        return $this->hasMany(ComplexRelationship::class);
    }
}
