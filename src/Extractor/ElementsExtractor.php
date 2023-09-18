<?php

namespace Gcanal\FeedCreator\Extractor;

use Gcanal\FeedCreator\Optional;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @implements Extractor<non-empty-array<Crawler>>
 */
class ElementsExtractor implements Extractor
{
    public function __construct(
        public readonly string $selector,
    ) {}

    public function extractFrom(Crawler $crawler): Optional
    {
        try {
            /** @var Crawler[] $items */
            $items = $crawler->filter($this->selector)->each(fn(Crawler $crawler): Crawler => $crawler);
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
