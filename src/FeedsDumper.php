<?php

declare(strict_types=1);

namespace Gcanal\FeedCreator;

use Gcanal\FeedCreator\Opml as Opml;

final class FeedsDumper
{
    private Filesystem $filesystem;

    public function __construct(
        private readonly Config $config,
        private readonly string $feedsDirectory,
        private readonly string $baseURL,
        ?Filesystem $filesystem = null,
    ) {
        if (!file_exists($feedsDirectory) || !is_dir($feedsDirectory)) {
            throw new \InvalidArgumentException($feedsDirectory . " doesn't exist.");
        }

        $this->filesystem = $filesystem ?? new LocalFilesystem();
    }

    public function dump(): void
    {
        /** @var Feed[] $feeds */
        $feeds = [];
        foreach ($this->config->urls as $pageURL) {
            $creator = new FeedCreator($this->config->getProviderFrom($pageURL), $this->filesystem);
            $feeds[] = $feed = $creator->getFeed($pageURL, $this->baseURL);
            $this->filesystem->putContents($this->getFeedFilename($feed), $feed->toAtom());
        }

        $this->createOpml($feeds);
    }

    /** @param Feed[] $feeds */
    private function createOpml(array $feeds): void
    {
        $opml = new Opml\Feed(
            'Feed subcriptions',
            ...array_map(
                fn (Feed $feed): Opml\Outline => new Opml\Outline(
                    title: $feed->title,
                    xmlUrl: $feed->url,
                    htmlUrl: $feed->link,
                    scanDelay: Optional::of(90)
                ),
                $feeds,
            )
        );

        $this->filesystem->putContents(
            $this->feedsDirectory . '/index.xml',
            $opml->toXML(),
        );
    }

    private function getFeedFilename(Feed $feed): string
    {
        $path = parse_url($feed->url, PHP_URL_PATH);
        if (!is_string($path)) {
            throw new \RuntimeException('Unable to extract feed path from ' . $feed->url);
        }

        return $this->feedsDirectory.'/'.pathinfo($path, PATHINFO_BASENAME);
    }
}
