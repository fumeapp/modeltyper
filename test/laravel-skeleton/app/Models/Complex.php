<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complex extends Model
{
    protected $table = 'complex_model_table';

    protected $casts = [
        'json' => 'json',
        'jsonb' => 'json',
        'year' => 'int',
    ];
}
