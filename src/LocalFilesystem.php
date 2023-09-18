<?php

declare(strict_types=1);

namespace Gcanal\FeedCreator;

final class LocalFilesystem implements Filesystem
{
    public function getContents(string $filename): string
    {
        return @file_get_contents($filename) ?: throw new \RuntimeException('Unable to retreive ' . $filename);
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
