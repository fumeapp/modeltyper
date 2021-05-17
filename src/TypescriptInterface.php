<?php

namespace App\ModelTyper;

class TypescriptInterface
{
    public function __construct(
        public string $name,
        public array $columns,
        public array $mutators,
        public array $relations,
    ) {
    }
}
