<?php

declare(strict_types=1);

namespace Gcanal\FeedCreator\Extractor;

use Gcanal\FeedCreator\Optional;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @implements Extractor<string>
 */
final class ValueExtractor implements Extractor
{
    public function __construct(
        public readonly ?string $selector,
        public readonly ?string $attr = null,
    ) {
    }

    public function extractFrom(Crawler $crawler): Optional
    {
        if ($this->selector === null) {
            return Optional::empty();
        }

        try {
            $node = $crawler->filter($this->selector);
            $value = $node->text();
            if ($this->attr !== null) {
                $value = $node->attr($this->attr) ?? '';
            }

            return Optional::of($value);
        } catch (\Throwable $throwable) {
            throw new \LogicException(
                sprintf('Unable to extract %s from %s', $this->selector, $crawler->html()),
                $throwable->getCode(),
                $throwable,
            );
        }
    }
}
