<?php

declare(strict_types=1);

namespace Gcanal\FeedCreator\Extractor;

use Gcanal\FeedCreator\Optional;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @template T
 */
interface Extractor
{
    /**
     * @return Optional<T>
     */
    public function extractFrom(Crawler $crawler): Optional;
}
