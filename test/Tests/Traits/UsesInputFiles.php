<?php

namespace Tests\Traits;

trait UsesInputFiles
{
    public function getInputPath(string $appends = ''): string
    {
        $path = ROOT_PATH . '/test/input';

        if ($appends) {
            $path .= str_starts_with($appends, '/') ? $appends : "/$appends";
        }

        return $path;
    }

    public function getInputFileContents(string $path): string
    {
        return file_get_contents($this->getInputPath($path));
    }

    public function getExpectedContent(string $path)
    {
        return $this->getInputFileContents("expectations/$path");
    }
}
