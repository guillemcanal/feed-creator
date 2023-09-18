<?php

declare(strict_types=1);

namespace Gcanal\FeedCreator\Opml;

use DOMElement;
use Gcanal\FeedCreator\Optional;

final class Feed
{
    /** @var Outline[] */
    public readonly array $outlines;

    public function __construct(
        public readonly string $title,
        Outline ...$outlines,
    ) {
        $this->outlines = $outlines;
    }

    public static function fromString(string $opml): self
    {
        $toInt = static fn (string $v): int => (int) $v;

        $dom = new \DOMDocument();
        $dom->loadXML($opml);

        return new self(
            $dom->getElementsByTagName('title')->item(0)?->nodeValue ?? 'Feed subscriptions',
            ...array_map(
                static fn (DOMElement $node): Outline => new Outline(
                    $node->getAttribute('text'),
                    $node->getAttribute('xmlUrl'),
                    $node->getAttribute('htmlUrl'),
                    Optional::ofNullable($node->getAttribute('scanDelay') ?: '90')->map($toInt),
                ),
                iterator_to_array($dom->getElementsByTagName('outline')),
            )
        );
    }

    public function toXML(): string
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $opml = $dom->createElement('opml');
        $opml->setAttribute('version', '2.0');

        $head = $dom->createElement('head');
        $opml->appendChild($head);

        $title = $dom->createElement('title', $this->title);
        $head->appendChild($title);

        $body = $dom->createElement('body');
        $opml->appendChild($body);

        foreach ($this->outlines as $outlineData) {
            $outline = $dom->createElement('outline');
            $outline->setAttribute('text', $outlineData->title);
            $outline->setAttribute('type', 'rss');
            $outline->setAttribute('xmlUrl', $outlineData->xmlUrl);
            $outline->setAttribute('htmlUrl', $outlineData->htmlUrl);
            $outline->setAttribute('scanDelay', (string) $outlineData->scanDelay->orElse('90'));
            $body->appendChild($outline);
        }

        $dom->appendChild($opml);

        return $dom->saveXML() ?: throw new \RuntimeException('Unable to generate the OPML feed');
    }
}
