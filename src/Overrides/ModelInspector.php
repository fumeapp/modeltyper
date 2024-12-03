<?php

namespace FumeApp\ModelTyper\Overrides;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\ModelInspector as EloquentModelInspector;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class ModelInspector extends EloquentModelInspector
{
    /**
     * Create a new model inspector instance.
     */
    public function __construct(?Application $app = null)
    {
        $this->relationMethods = collect(Arr::flatten(Config::get('modeltyper.custom_relationships', [])))
            ->map(fn (string $method): string => Str::trim($method))
            ->merge($this->relationMethods)
            ->toArray();

        parent::__construct($app ?? app());
    }
}
