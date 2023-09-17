<?php

namespace Gcanal\FeedCreator;

use Laminas\Feed\Reader\Entry\EntryInterface;
use Laminas\Feed\Reader\Reader;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\containsOnlyInstancesOf;
use function PHPUnit\Framework\countOf;
use function PHPUnit\Framework\equalTo;
use function PHPUnit\Framework\isInstanceOf;

class ExtractTest extends TestCase
{
    /** @test */
    public function itCanParseAWebPageAndGenerateAFeed(): void
    {
        $testPage = 'file://'.__DIR__.'/fixtures/demo.html';
        $jsonConfig = __DIR__.'/fixtures/config.json';

        $provider = Config::fromJSONFile($jsonConfig)->getProviderFrom($testPage);
        $feed = (new FeedCreator($provider))->getFeed($testPage);

        assertThat($feed->title, equalTo('Page title'));
        assertThat($feed->entries, countOf(3));

        $firstEntry = current($feed->entries);
        assertThat($firstEntry->title, equalTo('Item 3'));
        assertThat($firstEntry->link, equalTo(dirname($testPage).'/content?id=3'));
        assertThat($firstEntry->creationDate, isInstanceOf(\DateTimeImmutable::class));
        assertThat($firstEntry->creationDate->format('Y-m-d H:i'), equalTo('2023-09-15 16:00'));
    }

    /** @test */
    public function itCanGenerateAnAtomFeed(): void
    {
        $testPage = 'file://'.__DIR__.'/fixtures/demo.html';
        $jsonConfig = __DIR__.'/fixtures/config.json';

        $provider = Config::fromJSONFile($jsonConfig)->getProviderFrom($testPage);
        $feed = (new FeedCreator($provider))->getFeed($testPage);
        $atomFeed = Reader::importString($feed->toAtom());

        assertThat($atomFeed->getTitle(), equalTo('Page title'));
        assertThat($atomFeed->getLink(), equalTo($testPage));

        $entries = iterator_to_array($atomFeed);
        assertThat($entries, countOf(3));
        assertThat($entries, containsOnlyInstancesOf(EntryInterface::class));

        $entry = current($entries);
        assertThat($entry->getTitle(), equalTo('Item 3'));
        assertThat($entry->getLink(), equalTo(dirname($testPage).'/content?id=3'));
        assertThat($entry->getDateCreated(), isInstanceOf(\DateTimeInterface::class));
        assertThat($entry->getDateCreated()->format('Y-m-d H:i'), equalTo('2023-09-15 16:00'));
    }
}