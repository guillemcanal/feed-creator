<?php

declare(strict_types=1);

namespace Gcanal\FeedCreator;

final class Transliterator
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

        return ($slug = $transliterator->transliterate($string)) !== false 
            ? $slug 
            : throw new \RuntimeException('Cannot slugify ' . $string);
    }
}
