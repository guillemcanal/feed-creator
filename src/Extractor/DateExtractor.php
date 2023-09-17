<?php

namespace Gcanal\FeedCreator\Extractor;

use Gcanal\FeedCreator\Optional;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @template T
 * @implements Extractor<\DateTimeImmutable>
 */
class DateExtractor implements Extractor
{
    public function __construct(
        public readonly ?string $selector = null,
        public readonly ?string $attr = null,
        public readonly ?string $dateFormat = null,
    ) {}

    public function extractFrom(Crawler $crawler): Optional
    {
        if (!$this->selector) {
            return Optional::empty();
        }

        return (new ValueExtractor($this->selector, $this->attr))
            ->extractFrom($crawler)
            ->map(fn(string $value) => $this->toDate($value));
    }

    private function toDate(string $value): \DateTimeImmutable
    {
        return $this->dateFormat 
            ? \DateTimeImmutable::createFromFormat($this->dateFormat, $value)
            : new \DateTimeImmutable($value); 
    }
}
