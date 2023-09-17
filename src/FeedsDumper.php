<?php

namespace Gcanal\FeedCreator;

use Laminas\Feed\Reader\Reader;

class FeedsDumper
{
    public function __construct(
        private readonly Config $config,
        private string $feedsDirectory,
        private ?Filesystem $filesystem = null,
    ) {
        if (!file_exists($feedsDirectory) || !is_dir($feedsDirectory)) {
            throw new \InvalidArgumentException($feedsDirectory . ' doesn\'t exist.');
        }

        $this->filesystem ??= new LocalFilesystem();
    }

    public function dump(): void
    {
        foreach ($this->config->urls as $url) {
            $creator = new FeedCreator($this->config->getProviderFrom($url), $this->filesystem);
            $feed = $creator->getFeed($url);
            $feedFilename = $this->getFeedFilename($feed);
            if ($this->feedShouldBeDumped($feedFilename, $feed)) {
                $this->filesystem->putContents($feedFilename, $feed->toAtom());
                printf("generated feed\n");
            } else {
                printf("feed already up to date\n");
            }
        }

        $this->updateIndex();
    }

    private function getFeedFilename(Feed $feed): string
    {
        return $this->feedsDirectory . '/' . Transliterator::slugify($feed->title) . '.atom';
    }

    private function feedShouldBeDumped(string $feedFilename, Feed $feed): bool
    {
        if (!$this->filesystem->exists($feedFilename)) {
            return true;
        }

        return Reader::importFile($feedFilename)->getDateModified() != $feed->dateModified;
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
        foreach ($directory as $fileInfo) {
            if ($fileInfo->getExtension() !== 'atom') {
                continue;
            }

            $reader = Reader::importFile($fileInfo->getRealPath());

            $entries[] = sprintf(
                $entry,
                './'.$fileInfo->getFilename(),
                $reader->getTitle(),
                $reader->getDateModified()->format(\DateTime::ATOM),
                $reader->getDateModified()->format('Y-m-d H:i:s'),
            );
        }

        $content = sprintf($html, implode(PHP_EOL, $entries));
        $this->filesystem->putContents($this->feedsDirectory . '/index.html', $content);
    }
}