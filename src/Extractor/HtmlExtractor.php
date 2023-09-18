<?php

namespace Gcanal\FeedCreator\Extractor;

use Gcanal\FeedCreator\Optional;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @implements Extractor<string>
 */
class HtmlExtractor implements Extractor
{
    public function __construct(
        public readonly ?string $selector,
        public readonly ?string $attr,
        public readonly ?string $template,
    ) {}

    public function extractFrom(Crawler $crawler): Optional
    {
        if (!$this->selector) {
            return Optional::empty();
        }

        try {
            $value = $crawler->filter($this->selector);
            if ($this->attr) {
                $value = $value->attr($this->attr);
                if ($this->template) {
                    $value = sprintf($this->template, $value);
                }
            } else {
                $value = $value->outerHtml();
            }
        } catch (\Throwable $e) {
            throw new \LogicException(
                sprintf('Unable to extract %s from %s', $this->selector, $crawler->html()),
                $e->getCode(),
                $e,
            );
        }
        
        return $value ? Optional::of($value) : Optional::empty();
    }
}
