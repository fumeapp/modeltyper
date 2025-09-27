<?php

namespace FumeApp\ModelTyper\Actions;

use Illuminate\Support\Facades\Config;

class WriteAvg
{
    public function __invoke(array $avg, string $indent = '', bool $jsonOutput = false, bool $optionalAverages = false): string|array
    {
        $relation = $avg['relation'];
        $column = $avg['column'];

        $case = Config::get('modeltyper.case.columns', 'snake');
        $optional = $optionalAverages ? '?' : '';

        $avgName = app(MatchCase::class)($case, "{$relation} avg {$column}");

        if ($jsonOutput) {
            return [
                'type' => 'number | null',
                'name' => "{$avgName}{$optional}",
            ];
        }

        return "{$indent}  {$avgName}{$optional}: number | null;" . PHP_EOL;
    }
}
