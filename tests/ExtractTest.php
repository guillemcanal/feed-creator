<?php

namespace Gcanal\FeedCreator;

use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;
use function PHPUnit\Framework\isInstanceOf;
use function PHPUnit\Framework\isType;
use function PHPUnit\Framework\stringStartsWith;

class ExtractTest extends TestCase
{
    /** @test */
    public function itWork(): void
    {
        $url = 'https://readmanganato.com/manga-mj990066';
        $provider = Config::fromJSONFile(dirname(__DIR__) . '/config.json')->getProviderFrom($url);
        $feed = (new FeedCreator($provider))->getFeed($url);

        assertThat($feed->title, equalTo('The Apothecary Is Gonna Make This Ragged Elf Happy'));
        assertThat($feed->entries[0]->title, stringStartsWith('Chapter'));
        assertThat($feed->entries[0]->link, stringStartsWith('https://readmanganato.com/manga-mj990066/chapter-'));
        assertThat($feed->entries[0]->creationDate, isInstanceOf(\DateTimeImmutable::class));

        assertThat($feed->toAtom(), isType('string'));
    }
}