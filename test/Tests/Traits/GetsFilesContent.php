<?php

namespace Tests\Traits;

use Illuminate\Support\Str;

trait GetsFilesContent
{
    public function normalizeLineEndings(string $string): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return Str::of($string)
                ->replace("\r\n", "\n")
                ->replace("\n", PHP_EOL)
                ->toString();
        }

        return $string;
    }

    public function getFileContents(string $path, bool $addEOL = false): string
    {
        $contents = (string) file_get_contents($path);

        return $this->normalizeLineEndings($addEOL ? $contents . PHP_EOL : $contents);
    }
}
