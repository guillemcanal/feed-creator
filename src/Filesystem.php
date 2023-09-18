<?php

declare(strict_types=1);

namespace Gcanal\FeedCreator;

interface Filesystem
{
    public function getContents(string $filename): string;

    public function putContents(string $filename, string $data): void;

    public function exists(string $filename): bool;
}
