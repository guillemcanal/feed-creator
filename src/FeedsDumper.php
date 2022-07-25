<?php

namespace Gcanal\FeedCreator;

use Laminas\Feed\Reader\Reader;

class FeedsDumper
{
    public function __construct(
        private readonly Config      $config,
        private ?string $feedsDirectory = null,
        private ?Filesystem $filesystem = null,
    )
    {
        $this->feedsDirectory ??= dirname(__DIR__) . '/public';
        $this->filesystem ??= new LocalFilesystem();
    }

    public function dump(): void
    {
        foreach ($this->config->urls as $url) {
            $creator = new FeedCreator($this->config->getProviderFrom($url), $this->filesystem);
            $feed = $creator->getFeed($url);
            $feedFilename = $this->getFeedFilename($feed);
            if ($this->feedShouldBeDumped($feedFilename, $feed)) {
                $this->filesystem->putContents($feedFilename, $feed->toAtom());
                printf("generated feed: %s\n", $feedFilename);
            } else {
                printf("feed already up to date: %s\n", $feedFilename);
            }
        }
    }

    private function getFeedFilename(Feed $feed): string
    {
        return $this->feedsDirectory . '/' . Transliterator::slugify($feed->title) . '.atom';
    }

    private function feedShouldBeDumped(string $feedFilename, Feed $feed): bool
    {
        if (!$this->filesystem->exists($feedFilename)) {
            return true;
        }

        return Reader::importFile($feedFilename)->getDateModified() != $feed->dateModified;
    }
}