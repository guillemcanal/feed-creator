<?php

namespace Gcanal\FeedCreator;

class LocalFilesystem implements Filesystem
{
    public function getContents(string $filename): string
    {
        return file_get_contents($filename);
    }

    public function putContents(string $filename, string $data): void
    {
        file_put_contents($filename, $data);
    }

    public function exists(string $filename): bool
    {
        return file_exists($filename);
    }
}
