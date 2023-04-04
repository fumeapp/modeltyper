<?php

namespace App\Enums;

/**
 * @property ADMIN - Can do anything
 * @property USER - Standard read-only
 */
enum Roles: string
{
    case ADMIN = 'admin';
    case USER = 'user';

    public static function fromValue(string $value): self
    {
        return match ($value) {
            'admin' => self::ADMIN,
            'user' => self::USER,
            default => throw new \Exception('Invalid value for Roles enum'),
        };
    }
}
