<?php

namespace Gcanal\FeedCreator;

class Transliterator
{
    public static function slugify(string $string): string
    {
        $rules = <<<'RULES'
            :: Any-Latin;
            :: NFD;
            :: [:Nonspacing Mark:] Remove;
            :: NFC;
            :: [^-[:^Punctuation:]] Remove;
            :: Lower();
            [:^L:] { [-] > ;
            [-] } [:^L:] > ;
            [-[:Separator:]]+ > '-';
        RULES;

        return \Transliterator::createFromRules($rules)->transliterate($string);
    }
}