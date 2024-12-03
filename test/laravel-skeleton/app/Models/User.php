<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_traditional',
        'role_new',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
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
