<?php

namespace Gcanal\FeedCreator\Extractor;

use Gcanal\FeedCreator\Optional;
use Symfony\Component\DomCrawler\Crawler;

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

        $value = $crawler->filter($this->selector);
        if ($this->attr) {
            $value = $value->attr($this->attr);
            if ($this->template) {
                $value = sprintf($this->template, $value);
            }
        } else {
            $value = $value->outerHtml();
        }

        return Optional::of($value);
    }
}