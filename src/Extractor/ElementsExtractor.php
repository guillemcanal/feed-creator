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
        try {
            $items = $crawler->filter($this->selector)->each(fn(Crawler $crawler) => $crawler);
        } catch (\Throwable $e) {
            throw new \LogicException(
                sprintf('Unable to extract %s from %s', $this->selector, $crawler->html()),
                $e->getCode(),
                $e,
            );
        }
        
        if (!count($items)) {
            return Optional::empty();
        }

        return Optional::of($items);
    }
}
