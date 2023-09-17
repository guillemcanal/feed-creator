<?php

namespace Gcanal\FeedCreator;

use Gcanal\FeedCreator\Extractor\Extractor;
use Symfony\Component\DomCrawler\Crawler;

class Provider
{
    /**
     * @param Extractor<Crawler[]> $items
     * @param Extractor<string> $feedTitle
     * @param Extractor<string> $title
     * @param Extractor<string> $link
     * @param Extractor<\DateTimeImmutable> $date
     * @param Extractor<string> $description
     */
    public function __construct(
        public readonly Matcher   $matcher,
        public readonly Extractor $feedTitle,
        public readonly Extractor $items,
        public readonly Extractor $title,
        public readonly Extractor $link,
        public readonly Extractor $date,
        public readonly Extractor $description,
    )
    {}
}