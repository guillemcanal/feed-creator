<?php

namespace Gcanal\FeedCreator\Extractor;

use Gcanal\FeedCreator\Optional;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @template T
 * @implements Extractor<Crawler[]>
 */
class ElementsExtractor implements Extractor
{
    public function __construct(
        public readonly string $selector,
    ) {}

    public function extractFrom(Crawler $crawler): Optional
    {
        $items = $crawler->filter($this->selector)->each(fn(Crawler $crawler) => $crawler);
        if (!count($items)) {
            return Optional::empty();
        }

        return Optional::of($items);
    }
}
