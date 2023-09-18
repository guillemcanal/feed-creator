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

        /** @var \Transliterator $transliterator  */
        $transliterator = \Transliterator::createFromRules($rules);

        return $transliterator->transliterate($string) ?: throw new \RuntimeException('Cannot slugify ' . $string);
    }
}