<?php

namespace Gcanal\FeedCreator\Extractor;

use Gcanal\FeedCreator\Optional;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @template T
 * @implements Extractor<string>
 */
class ValueExtractor implements Extractor
{
    public function __construct(
        public readonly ?string $selector,
        public readonly ?string $attr = null,
    ) {}

    public function extractFrom(Crawler $crawler): Optional
    {
        if (!$this->selector) {
            return Optional::empty();
        }

        $node = $crawler->filter($this->selector);
        $value = $node->text();
        if ($this->attr) {
            $value = $node->attr($this->attr);
        }

        return Optional::of($value);
    }
}
