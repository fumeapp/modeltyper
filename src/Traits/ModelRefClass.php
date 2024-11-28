<?php

namespace FumeApp\ModelTyper\Traits;

use ReflectionClass;

trait ModelRefClass
{
    /**
     * Get the reflection interface.
     *
     * @param  array{"class": class-string<\Illuminate\Database\Eloquent\Model>, database: string, table: string, policy: class-string|null, attributes: \Illuminate\Support\Collection, relations: \Illuminate\Support\Collection, events: \Illuminate\Support\Collection, observers: \Illuminate\Support\Collection, collection: class-string<\Illuminate\Database\Eloquent\Collection<\Illuminate\Database\Eloquent\Model>>, builder: class-string<\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>>}  $info
     * @return \ReflectionClass<\Illuminate\Database\Eloquent\Model>
     */
    public function getRefInterface(array $info): ReflectionClass
    {
        return new ReflectionClass($info['class']);
    }
}
