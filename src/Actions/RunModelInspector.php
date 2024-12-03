<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Overrides\ModelInspector;
use Illuminate\Contracts\Foundation\Application;

class RunModelInspector
{
    public function __construct(protected ?Application $app = null)
    {
        $this->app = $app ?? app();
    }

    /**
     * Run internal Laravel ModelInspector class.
     *
     * @see https://github.com/laravel/framework/blob/11.x/src/Illuminate\Database\Eloquent\ModelInspector.php
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @return array{"class": class-string<\Illuminate\Database\Eloquent\Model>, database: string, table: string, policy: class-string|null, attributes: \Illuminate\Support\Collection, relations: \Illuminate\Support\Collection, events: \Illuminate\Support\Collection, observers: \Illuminate\Support\Collection, collection: class-string<\Illuminate\Database\Eloquent\Collection<\Illuminate\Database\Eloquent\Model>>, builder: class-string<\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>>}|null
     */
    public function __invoke(string $model): ?array
    {
        try {
            return app(ModelInspector::class)->inspect($model);
        } catch (\Illuminate\Contracts\Container\BindingResolutionException $th) {
            return null;
        }
    }
}
