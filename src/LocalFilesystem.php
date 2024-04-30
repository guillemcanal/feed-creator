<?php

declare(strict_types=1);

namespace Gcanal\FeedCreator;

final class LocalFilesystem implements Filesystem
{
    public function getContents(string $filename): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $filename);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible;)');
        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
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
