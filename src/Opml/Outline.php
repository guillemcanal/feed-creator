<?php

namespace Gcanal\FeedCreator\Opml;

use Gcanal\FeedCreator\Optional;

class Outline {
    public function __construct(
        public readonly string $title,
        public readonly string $xmlUrl,
        public readonly string $htmlUrl,
        /** @var Optional<int> */
        public readonly Optional $scanDelay,
    ) {}
}