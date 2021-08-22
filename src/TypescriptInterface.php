<?php

namespace FumeApp\ModelTyper;

class TypescriptInterface
{
    public function __construct(
        public string $name,
        public array $columns,
        public array $mutators,
        public array $relations,
        public array $interfaces,
    ) {
    }
}
