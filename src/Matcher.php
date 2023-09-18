<?php

declare(strict_types=1);

namespace Gcanal\FeedCreator;

final class Matcher
{
    public function __construct(
        private readonly string $pattern,
    ) {
    }

    public function match(string $url): bool
    {
        return preg_match('@'.$this->pattern.'@', $url) === 1;
    }
}
