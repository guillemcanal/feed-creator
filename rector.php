<?php

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(TypedPropertyFromStrictConstructorRector::class);
    $rectorConfig->rule(DeclareStrictTypesRector::class);

    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::PRIVATIZATION,
        SetList::CODING_STYLE,
        SetList::TYPE_DECLARATION,
        SetList::PHP_82,
    ]);
};
