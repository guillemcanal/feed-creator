<?php

namespace Gcanal\FeedCreator;

use Gcanal\FeedCreator\Extractor\DateExtractor;
use Gcanal\FeedCreator\Extractor\ElementsExtractor;
use Gcanal\FeedCreator\Extractor\HtmlExtractor;
use Gcanal\FeedCreator\Extractor\ValueExtractor;

class Provider
{

    public function __construct(
        public readonly Matcher   $matcher,
        public readonly ValueExtractor $feedTitle,
        public readonly ElementsExtractor $items,
        public readonly ValueExtractor $title,
        public readonly ValueExtractor $link,
        public readonly DateExtractor $date,
        public readonly HtmlExtractor $description,
    )
    {}
}