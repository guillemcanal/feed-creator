<?php

namespace Gcanal\FeedCreator;

class Entry
{
    public function __construct(
        public readonly string             $title,
        public readonly string             $link,
        public readonly \DateTimeImmutable $creationDate,
    )
    {
    }
}
