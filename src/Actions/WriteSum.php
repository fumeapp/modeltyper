<?php

namespace FumeApp\ModelTyper\Actions;

use Illuminate\Support\Facades\Config;

class WriteSum
{
    public function __invoke(array $sum, string $indent = '', bool $jsonOutput = false, bool $optionalSums = false): string|array
    {
        $relation = $sum['relation'];
        $column = $sum['column'];

        $case = Config::get('modeltyper.case.columns', 'snake');
        $optional = $optionalSums ? '?' : '';

        $sumName = app(MatchCase::class)($case, "{$relation} sum {$column}");

        if ($jsonOutput) {
            return [
                'type' => 'number | null',
                'name' => "{$sumName}{$optional}",
            ];
        }

        return "{$indent}  {$sumName}{$optional}: number | null;" . PHP_EOL;
    }
}
