<?php

namespace FumeApp\ModelTyper;

class TypescriptInterface
{
    /**
     * @param  array<string, mixed>  $columns
     * @param  array<string, mixed>  $mutators
     * @param  array<string, mixed>  $relations
     * @param  array<string, mixed>  $interfaces
     */
    public function __construct(
        public string $name,
        public array $columns,
        public array $mutators,
        public array $relations,
        public array $interfaces,
    ) {}
}
