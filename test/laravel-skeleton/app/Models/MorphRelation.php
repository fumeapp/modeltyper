<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MorphRelation extends Model
{
    public function model(): MorphTo|User|Complex
    {
        return $this->morphTo();
    }
}
