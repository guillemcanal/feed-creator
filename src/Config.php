<?php

namespace Gcanal\FeedCreator;

use Gcanal\FeedCreator\Extractor\DateExtractor;
use Gcanal\FeedCreator\Extractor\ElementsExtractor;
use Gcanal\FeedCreator\Extractor\HtmlExtractor;
use Gcanal\FeedCreator\Extractor\ValueExtractor;
use Gcanal\FeedCreator\Extractor\Extractor;

class Config
{
    /**
     * @param array<Provider> $providers
     * @param array<string> $urls
     */
    public function __construct(
        public readonly array $providers,
        public readonly array $urls,
    )
    {
    }

    public function getProviderFrom(string $url): Provider
    {
        foreach ($this->providers as $provider) {
            if ($provider->matcher->match($url)) {
                return $provider;
            }
        }

        throw new \LogicException('Not provider found for URL ' . $url);
    }

    public static function fromJSONFile(string $json): self
    {
        $data = json_decode(@file_get_contents($json) ?: '', true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \LogicException(\json_last_error_msg(), \json_last_error());
        }
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Expected array');
        }

        return new self(
            array_map(
                static fn (array $provider) => new Provider(
                    matcher: new Matcher($provider['match'] ?? throw new \LogicException('You must provide a matcher')),
                    feedTitle: self::toExtractor('feedTitle', $provider),
                    items: self::toExtractor('items', $provider),
                    title: self::toExtractor('title', $provider),
                    link: self::toExtractor('link', $provider),
                    date: self::toExtractor('date', $provider),
                    description: self::toExtractor('description', $provider),
                ),
                $data['providers'] ?? [],
            ),
            array_map(
                static function (string $url) {
                    return $url;
                },
                $data['urls'] ?? [],
            )
        );
    }

    /**
     * @param array<string, array{selector: string, attr?: string, dateFormat?: string, template?: string}> $data
     * 
     * @phpstan-return (
     *      $name is 'items' ? ElementsExtractor : 
     *      $name is 'date' ? DateExtractor : 
     *      $name is 'description' ? HtmlExtractor : 
     *      ValueExtractor
     * )
     */
    private static function toExtractor(string $name, array $data): Extractor
    {
        return match ($name) {
            'items' => new ElementsExtractor(
                $data[$name]['selector'] ?? throw new \LogicException('You must provide a selector form items'),
            ),
            'date' => new DateExtractor(
                $data[$name]['selector'] ?? null,
                $data[$name]['attr'] ?? null,
                $data[$name]['dateFormat'] ?? null,
            ),
            'description' => new HtmlExtractor(
                $data[$name]['selector'] ?? null,
                $data[$name]['attr'] ?? null,
                $data[$name]['template'] ?? null,
            ),
            default => new ValueExtractor(
                $data[$name]['selector'] ?? null,
                $data[$name]['attr'] ?? null,
            ),
        };
    }
}