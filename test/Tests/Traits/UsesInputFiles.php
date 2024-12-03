<?php

namespace Tests\Traits;

use function Orchestra\Testbench\package_path;

trait UsesInputFiles
{
    use GetsFilesContent;

    public function getInputPath(?string $appends = null): string
    {
        $path = package_path('test/input');

        if ($appends) {
            $path .= str_starts_with($appends, '/') ? $appends : "/$appends";
        }

        return $path;
    }

    public function getInputFileContents(string $path, bool $addEOL = false): string
    {
        return $this->getFileContents($this->getInputPath($path), $addEOL);
    }

    public function getExpectedContent(string $path, bool $addEOL = false): string
    {
        return $this->getInputFileContents("expectations/$path", $addEOL);
    }
}
