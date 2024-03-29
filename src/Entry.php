<?php

declare(strict_types=1);

namespace Gcanal\FeedCreator;

final class Entry
{
    /**
     * @param Optional<string> $description
     */
    public function __construct(
        public readonly string             $title,
        public readonly string             $link,
        public readonly Optional           $description,
        public readonly \DateTimeImmutable $creationDate,
    ) {
    }
}
