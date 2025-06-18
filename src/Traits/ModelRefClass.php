<?php

namespace FumeApp\ModelTyper\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use ReflectionClass;

trait ModelRefClass
{
    /**
     * Get the reflection interface.
     *
     * @param  array{"class": class-string<Model>, database: string, table: string, policy: class-string|null, attributes: Collection, relations: Collection, events: Collection, observers: Collection, collection: class-string<\Illuminate\Database\Eloquent\Collection<Model>>, builder: class-string<Builder<Model>>}  $info
     * @return ReflectionClass<Model>
     */
    public function getRefInterface(array $info): ReflectionClass
    {
        return new ReflectionClass($info['class']);
    }
}
