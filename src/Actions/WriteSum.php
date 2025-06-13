<?php

namespace FumeApp\ModelTyper\Actions;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class WriteSum
{
    public function __invoke(array $sum, string $indent = '', bool $jsonOutput = false, bool $optionalSums = false): string|array
    {
        $relation = $sum['relation'];
        $column = $sum['column'];

        $columnsCase = Config::get('modeltyper.case.columns', 'snake');
        $optionalSums = $optionalSums ? '?' : '';

        $sumName = match ($columnsCase) {
            'camel' => Str::camel("{$relation}_sum_{$column}"),
            'pascal' => Str::studly("{$relation}_sum_{$column}"),
            'kebab' => Str::kebab("{$relation}_sum_{$column}"),
            default => "{$relation}_sum_{$column}",
        };

        if ($jsonOutput) {
            return [
                'type' => 'number | null',
                'name' => "{$sumName}{$optionalSums}",
            ];
        }

        return "{$indent}  {$sumName}{$optionalSums}: number | null;" . PHP_EOL;
    }
}
