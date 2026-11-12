<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HiddenRelation extends Model
{
    protected $hidden = [
        'hiddenRelation',
    ];

    public function hiddenRelation(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function visibleRelation(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
