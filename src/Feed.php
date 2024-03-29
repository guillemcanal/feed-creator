<?php

declare(strict_types=1);

namespace Gcanal\FeedCreator;

use Laminas\Feed\Writer\Feed as FeedWriter;
use Laminas\Feed\Writer\Renderer\Feed\Atom;
use Laminas\Feed\Writer\Renderer\RendererInterface;

final class Feed
{
    private const FEED_TYPE = 'atom';

    public readonly \DateTimeImmutable $dateModified;

    /**
     * @param array<Entry> $entries
     */
    public function __construct(
        public readonly string $title,
        public readonly string $link,
        public readonly string $url,
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
        $feed->setFeedLink($this->url, self::FEED_TYPE);
        $feed->setType(self::FEED_TYPE);

        foreach ($this->entries as $entryData) {
            $entry = $feed->createEntry();
            $entry->setTitle($entryData->title);
            $entry->setLink($entryData->link);
            $entry->setDateModified($entryData->creationDate);
            $entry->setDateCreated($entryData->creationDate);
            $entryData->description->ifPresent(static fn (string $s) => $entry->setDescription($s));
            $feed->addEntry($entry);
        }

        $writer = new Atom($feed);
        $writer->render();

        self::addXmlStylesheet($writer, './../atom-feed.xsl');

        return $writer->saveXml();
    }

    private static function addXmlStylesheet(RendererInterface $renderer, string $stylesheet): void
    {
        $renderer->getElement()->parentNode?->insertBefore(
            $renderer->getDomDocument()->createProcessingInstruction(
                'xml-stylesheet',
                'type="text/xsl" href="'.$stylesheet.'"'
            ),
            $renderer->getElement(),
        );
    }
}
