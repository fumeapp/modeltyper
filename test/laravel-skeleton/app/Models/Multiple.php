<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// SHOULD be detected
class Multiple extends Model {}

// Should NOT be detected
class OtherClass {}
