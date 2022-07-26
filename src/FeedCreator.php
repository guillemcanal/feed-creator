<?php

namespace Gcanal\FeedCreator;

use Symfony\Component\DomCrawler\Crawler;

class FeedCreator
{
    public function __construct(
        private readonly Provider    $provider,
        private readonly ?Filesystem $filesystem = new LocalFilesystem(),
    )
    {
    }

    public function getFeed(string $url): Feed
    {
        $crawler = new Crawler($this->filesystem->getContents($url));

        return new Feed(
            title: $this->provider->feedTitle
                ->extractFrom($crawler)
                ->orElseThrow(new \LogicException('Unable to extract feed\'s title')),
            link: $url,
            entries: array_map(
                fn(Crawler $item) => new Entry(
                    title: $this->provider->title
                        ->extractFrom($item)
                        ->orElseThrow(new \LogicException('Unable to extract the entry\'s title')),
                    link: $this->provider->link
                        ->extractFrom($item)
                        ->orElseThrow(new \LogicException('Unable to extract the entry\'s link')),
                    description: $this->provider->description
                        ->extractFrom($item),
                    creationDate: $this->provider->date
                        ->extractFrom($item)
                        ->orElseThrow(new \LogicException('Unable to extract the entry\'s creationDate')),
                ),
                $this->provider->items
                    ->extractFrom($crawler)
                    ->orElseThrow(new \LogicException('Unable to extract the feed\'s items'))
            ),
        );
    }
}
