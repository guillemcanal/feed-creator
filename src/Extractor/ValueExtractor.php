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
        
        try {
            $node = $crawler->filter($this->selector);
            $value = $node->text();
            if ($this->attr) {
                $value = $node->attr($this->attr);
            }
        } catch (\Throwable $e) {
            throw new \LogicException(
                sprintf('Unable to extract %s from %s', $this->selector, $crawler->html()),
                $e->getCode(),
                $e,
            );
        }
        
        return Optional::of($value);
    }
}
