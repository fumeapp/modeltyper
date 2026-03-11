<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Overrides\ModelInspector;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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
     * @param  class-string<Model>  $model
     * @return array{"class": class-string<Model>, database: string, table: string, policy: class-string|null, attributes: Collection, relations: Collection, events: Collection, observers: Collection, collection: class-string<\Illuminate\Database\Eloquent\Collection<Model>>, builder: class-string<Builder<Model>>}|null
     */
    public function __invoke(string $model): ?array
    {
        try {
            return app(ModelInspector::class)->inspect($model);
        } catch (BindingResolutionException $th) {
            return null;
        }
    }
}
