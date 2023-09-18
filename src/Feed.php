<?php

declare(strict_types=1);

namespace Gcanal\FeedCreator;

use Laminas\Feed\Writer\Feed as FeedWriter;

final class Feed
{
    public readonly \DateTimeImmutable $dateModified;

    /**
     * @param array<Entry> $entries
     */
    public function __construct(
        public readonly string $title,
        public readonly string $link,
        public readonly array  $entries,
    ) {
        $entry = current($this->entries);
        $this->dateModified = $entry ? $entry->creationDate : new \DateTimeImmutable();
    }

    public function toAtom(): string
    {
        $feed = new FeedWriter();
        $feed->setTitle($this->title);
        $feed->setDateModified($this->dateModified);
        $feed->setLink($this->link);
        $feed->setFeedLink('http://localhost', 'atom');

        foreach ($this->entries as $entryData) {
            $entry = $feed->createEntry();
            $entry->setTitle($entryData->title);
            $entry->setLink($entryData->link);
            $entry->setDateModified($entryData->creationDate);
            $entry->setDateCreated($entryData->creationDate);
            $entryData->description->ifPresent(static fn (string $s) => $entry->setDescription($s));
            $feed->addEntry($entry);
        }

        return $feed->export('atom');
    }
}
