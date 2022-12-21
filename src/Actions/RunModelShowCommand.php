<?php

namespace FumeApp\ModelTyper\Actions;

use Illuminate\Support\Facades\Artisan;

class RunModelShowCommand
{
    /**
     * Run internal Laravel model:show command.
     *
     * @see https://github.com/laravel/framework/blob/9.x/src/Illuminate/Foundation/Console/ShowModelCommand.php
     *
     * @param  string  $model
     * @return array
     */
    public function __invoke(string $model): array
    {
        Artisan::call("model:show {$model} --json");

        return json_decode(Artisan::output(), true);
    }
}
