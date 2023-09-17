<?php

namespace Gcanal\FeedCreator;

class Matcher
{
    public function __construct(
        private readonly string $pattern,
    ){}

    public function match(string $url): bool
    {
        return preg_match('@'.$this->pattern.'@', $url) === 1;
    }
}