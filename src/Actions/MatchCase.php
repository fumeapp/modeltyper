<?php

namespace FumeApp\ModelTyper\Actions;

use Illuminate\Support\Str;

class MatchCase
{
    /**
     * Map the return type to a typescript type.
     */
    public function __invoke(string $case, string $value): string
    {
        return match ($case) {
            'snake' => Str::snake($value),
            'camel' => Str::camel($value),
            'pascal' => Str::studly($value),
            default => $value,
        };
    }
}
