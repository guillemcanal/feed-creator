<?php

namespace Gcanal\FeedCreator;

use LogicException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\UriResolver;

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
    public function getFeed(string $pageURL): Feed
    {
        $crawler = new Crawler($this->filesystem->getContents($pageURL));
        $resolveURL = fn(string $url): string => UriResolver::resolve($url, $pageURL);

        return new Feed(
            title: $this->provider->feedTitle
                ->extractFrom($crawler)
                ->orElseThrow(new LogicException('Unable to extract feed\'s title')),
            link: $pageURL,
            entries: array_map(
                fn(Crawler $item) => new Entry(
                    title: $this->provider->title
                        ->extractFrom($item)
                        ->orElseThrow(new LogicException('Unable to extract the entry\'s title')),
                    link: $this->provider->link
                        ->extractFrom($item)
                        ->map($resolveURL)
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
}
