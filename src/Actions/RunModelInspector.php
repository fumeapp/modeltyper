<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Overrides\ModelInspector;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
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
     * @see https://github.com/laravel/framework/blob/13.x/src/Illuminate\Database\Eloquent\ModelInspector.php
     *
     * @param  class-string<Model>  $model
     * @return array{"class": class-string<Model>, database: string, table: string, policy: class-string|null, attributes: Collection, relations: Collection, events: Collection, observers: Collection, collection: class-string<\Illuminate\Database\Eloquent\Collection<Model>>, builder: class-string<Builder<Model>>, resource: JsonResource|null}|null
     */
    public function __invoke(string $model): ?array
    {
        try {
            $result = app(ModelInspector::class)->inspect($model);

            return [
                'class' => $result['class'],
                'database' => $result['database'],
                'table' => $result['table'],
                'policy' => $result['policy'],
                'attributes' => $result['attributes'],
                'relations' => $result['relations'],
                'events' => $result['events'],
                'observers' => $result['observers'],
                'collection' => $result['collection'],
                'builder' => $result['builder'],
                'resource' => $result['resource'] ?? null,
            ];
        } catch (BindingResolutionException $th) {
            return null;
        }
    }
}
