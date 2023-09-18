<?php

namespace Gcanal\FeedCreator;

use Gcanal\FeedCreator\Opml as Opml;
use Laminas\Feed\Reader\Reader;

class FeedsDumper
{
    private Filesystem $filesystem;

    public function __construct(
        private readonly Config $config,
        private readonly string $feedsDirectory,
        ?Filesystem $filesystem = null,
    ) {
        if (!file_exists($feedsDirectory) || !is_dir($feedsDirectory)) {
            throw new \InvalidArgumentException($feedsDirectory . ' doesn\'t exist.');
        }

        $this->filesystem = $filesystem ?? new LocalFilesystem();
    }

    public function dump(): void
    {
        /** @var Feed[] */
        $feeds = [];
        foreach ($this->config->urls as $url) {
            $creator = new FeedCreator($this->config->getProviderFrom($url), $this->filesystem);
            $feeds[] = $feed = $creator->getFeed($url);
            $this->filesystem->putContents($this->getFeedFilename($feed), $feed->toAtom());
        }

        $this->createOpml($feeds);
        $this->updateIndex();
    }

    /** @param Feed[] $feeds */
    private function createOpml(array $feeds): void
    {
        $opml = new Opml\Feed(
            'Feed subcriptions',
            ...array_map(
                fn(Feed $feed): Opml\Outline => new Opml\Outline(
                    title: $feed->title,
                    xmlUrl: $this->getFeedFilename($feed),
                    htmlUrl: $feed->link,
                    scanDelay: Optional::of(90)
                ),
                $feeds,
            )
        );

        $this->filesystem->putContents(
            $this->feedsDirectory . '/subscrptions.opml',
            $opml->toXML(),
        );
    }

    private function getFeedFilename(Feed $feed): string
    {
        return $this->feedsDirectory . '/' . Transliterator::slugify($feed->title) . '.atom';
    }

    private function updateIndex(): void
    {
        $html = <<<'HTML'
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Feeds</title>
        </head>
        <body>
            <table>%s</table>
        </body>
        </html>
        HTML;

        $entry = <<<'HTML'
        <tr>
            <td><strong><a href="%s">%s</a></strong></td>
            <td>last update : <time datetime="%s">%s</time></td>
        </tr>
        HTML;

        $entries = [];
        $directory = new \RecursiveDirectoryIterator($this->feedsDirectory, \FilesystemIterator::SKIP_DOTS);
        /** @var \SplFileInfo $fileInfo */
        foreach ($directory as $fileInfo) {
            if ($fileInfo->getExtension() !== 'atom') {
                continue;
            }

            $reader = Reader::importFile($fileInfo->getRealPath());

            $entries[] = sprintf(
                $entry,
                './'.$fileInfo->getFilename(),
                $reader->getTitle(),
                $reader->getDateModified()?->format(\DateTime::ATOM) ?? '',
                $reader->getDateModified()?->format('Y-m-d H:i:s') ?? '',
            );
        }

        $content = sprintf($html, implode(PHP_EOL, $entries));
        $this->filesystem->putContents($this->feedsDirectory . '/index.html', $content);
    }
}