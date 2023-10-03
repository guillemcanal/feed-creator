<?php

declare(strict_types=1);

namespace Gcanal\FeedCreator\Extractor;

use Gcanal\FeedCreator\Optional;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @implements Extractor<string>
 */
final class HtmlExtractor implements Extractor
{
    public function __construct(
        public readonly ?string $selector,
        public readonly ?string $attr,
        public readonly ?string $template,
    ) {
    }

    public function extractFrom(Crawler $crawler): Optional
    {
        if ($this->selector === null) {
            return Optional::empty();
        }

        try {
            $value = $crawler->filter($this->selector);
            if ($this->attr !== null) {
                $value = $value->attr($this->attr);
                if ($this->template !== null) {
                    $value = sprintf($this->template, $value);
                }
            }
            if ($value instanceof Crawler) {
                $value = $value->outerHtml();
            }
        } catch (\Throwable $throwable) {
            throw new \LogicException(
                sprintf('Unable to extract %s from %s', $this->selector, $crawler->html()),
                $throwable->getCode(),
                $throwable,
            );
        }

        return $value !== null ? Optional::of($value) : Optional::empty();
    }
}
