<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $casts = [
        'role' => Roles::class,
    ];

    /**
     * Get the user's role using Laravel's traditional accessor.
     */
    public function getRoleTraditionalAttribute(): string
    {
        return Roles::fromValue($this->attributes['role'])->value;
    }

    /**
     * Get the user's role using Laravel's new accessor.
     */
    protected function roleNew(): Attribute
    {
        return Attribute::make(
            get: fn (string $value): string => Roles::fromValue($value)->value,
        );
    }

    /**
     * Test Enum as a casted attribute return type
     */
    protected function roleEnum(): Attribute
    {
        return Attribute::make(
            get: fn (): Roles => Roles::ADMIN,
        );
    }

    /**
     * Get the user's role using Laravel's traditional accessor.
     */
    public function getRoleEnumTraditionalAttribute(): Roles
    {
        return Roles::ADMIN;
    }
}
