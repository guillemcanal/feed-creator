<?php

declare(strict_types=1);

namespace Gcanal\FeedCreator\Extractor;

use Gcanal\FeedCreator\Optional;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @implements Extractor<non-empty-array<Crawler>>
 */
final class ElementsExtractor implements Extractor
{
    public function __construct(
        public readonly string $selector,
    ) {
    }

    public function extractFrom(Crawler $crawler): Optional
    {
        try {
            /** @var Crawler[] $items */
            $items = $crawler->filter($this->selector)->each(static fn (Crawler $crawler): Crawler => $crawler);
        } catch (\Throwable $throwable) {
            throw new \LogicException(
                sprintf('Unable to extract %s from %s', $this->selector, $crawler->html()),
                $throwable->getCode(),
                $throwable,
            );
        }

        if ($items === []) {
            return Optional::empty();
        }

        return Optional::of($items);
    }
}
