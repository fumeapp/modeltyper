<?php

namespace FumeApp\ModelTyper\Actions;

use Exception;
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
     *
     * @throws Exception
     */
    public function __invoke(string $model): array
    {
        $exitCode = Artisan::call("model:show {$model} --json");

        if ($exitCode !== 0) {
            dump('You may need to install the doctrine/dbal package to use this command.');
            dump('composer require doctrine/dbal');

            throw new Exception(Artisan::output());
        }

        return json_decode(Artisan::output(), true);
    }
}
