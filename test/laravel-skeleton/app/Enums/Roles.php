<?php

namespace App\Enums;

use App\Models\User;

/**
 * @property ADMIN - Can do anything
 * @property USER - Standard read-only
 * @property USERCLASS - Value that needs string escaping
 */
enum Roles: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case USERCLASS = User::class;

    public static function fromValue(string $value): self
    {
        return match ($value) {
            'admin' => self::ADMIN,
            'user' => self::USER,
            default => throw new \Exception('Invalid value for Roles enum'),
        };
    }
}
