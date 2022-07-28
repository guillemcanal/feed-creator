<?php

namespace Gcanal\FeedCreator;

use LogicException;
use Symfony\Component\DomCrawler\Crawler;

class FeedCreator
{
    public function __construct(
        private readonly Provider    $provider,
        private readonly ?Filesystem $filesystem = new LocalFilesystem(),
    )
    {
    }

    /**
     * @param string $url
     * @return Feed
     *
     * @throws LogicException When a value cannot be extracted
     */
    public function getFeed(string $url): Feed
    {
        $crawler = new Crawler($this->filesystem->getContents($url));
        $baseURL = static::getBaseURL($url);

        return new Feed(
            title: $this->provider->feedTitle
                ->extractFrom($crawler)
                ->orElseThrow(new LogicException('Unable to extract feed\'s title')),
            link: $url,
            entries: array_map(
                fn(Crawler $item) => new Entry(
                    title: $this->provider->title
                        ->extractFrom($item)
                        ->orElseThrow(new LogicException('Unable to extract the entry\'s title')),
                    link: $this->provider->link
                        ->extractFrom($item)
                        ->map(static::toAbsoluteURL($baseURL))
                        ->orElseThrow(new LogicException('Unable to extract the entry\'s link')),
                    description: $this->provider->description
                        ->extractFrom($item),
                    creationDate: $this->provider->date
                        ->extractFrom($item)
                        ->orElseThrow(new LogicException('Unable to extract the entry\'s creationDate')),
                ),
                $this->provider->items
                    ->extractFrom($crawler)
                    ->orElseThrow(new LogicException('Unable to extract the feed\'s items'))
            ),
        );
    }

    private static function getBaseURL(string $url): string
    {
        $parts = parse_url($url);
        if (!isset($parts['scheme'])) {
            throw new \LogicException('Unable to extract URL scheme from ' . $url);
        }
        if (!isset($parts['host'])) {
            throw new \LogicException('Unable to extract URL host from ' . $url);
        }

        return sprintf('%s://%s', $parts['scheme'], $parts['host']);
    }

    private static function toAbsoluteURL(string $baseURL): callable
    {
        return static function(string $url) use ($baseURL) {

            if (str_starts_with($url, '/')) {
                return $baseURL . $url;
            }

            return $url;
        };
    }
}
