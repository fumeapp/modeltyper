<?php

use Lurker\Event\FilesystemEvent;
use Robo\Tasks;

class RoboFile extends Tasks
{
    public function watch(string $patterns, string $commands)
    {
        $files = array_reduce(
            explode(',', $patterns),
            fn ($array, $pattern) => [...$array, ...glob(trim($pattern))],
            []
        );

        $this->taskWatch()->monitor($files, function (FilesystemEvent $event) use ($commands) {
            $this->taskExec($commands)->run();
        })->run();
    }
}
