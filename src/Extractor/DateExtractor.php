<?php

declare(strict_types=1);

namespace Gcanal\FeedCreator\Extractor;

use Gcanal\FeedCreator\Optional;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @implements Extractor<\DateTimeImmutable>
 */
final class DateExtractor implements Extractor
{
    public function __construct(
        public readonly ?string $selector = null,
        public readonly ?string $attr = null,
        public readonly ?string $dateFormat = null,
    ) {
    }

    public function extractFrom(Crawler $crawler): Optional
    {
        if ($this->selector === null) {
            return Optional::empty();
        }

        return (new ValueExtractor($this->selector, $this->attr))
            ->extractFrom($crawler)
            ->map(fn (string $value): \DateTimeImmutable => $this->toDate($value));
    }

    private function toDate(string $value): \DateTimeImmutable
    {
        return $this->dateFormat !== null
            ? (
                \DateTimeImmutable::createFromFormat($this->dateFormat, $value)
                ?: throw new \RuntimeException(
                    sprintf(
                        'Cannot parse "%s" with format "%s"',
                        $value,
                        $this->dateFormat,
                    )
                )
            )
            : new \DateTimeImmutable($value);
    }
}
