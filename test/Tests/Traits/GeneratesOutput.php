<?php

namespace Tests\Traits;

trait GeneratesOutput
{
    public function getOutputPath(string $appends = ''): string
    {
        $path = ROOT_PATH . '/test/output';

        if ($appends) {
            $path .= str_starts_with($appends, '/') ? $appends : "/$appends";
        }

        return $path;
    }

    public function deleteOutput()
    {
        $this->removeDirectoryContents($this->getOutputPath(), ['.keep']);
    }

    private function removeDirectory(string $dirPath)
    {
        $this->removeDirectoryContents($dirPath);
        rmdir($dirPath);
    }

    private function removeDirectoryContents(string $dirPath, array $ignore = [])
    {
        foreach (array_diff(scandir($dirPath), ['.', '..', ...$ignore]) as $filename) {
            $filepath = "$dirPath/$filename";

            if (is_file($filepath)) {
                unlink($filepath);
            }

            if (is_dir($filepath)) {
                $this->removeDirectory($filepath);
            }
        }
    }
}
