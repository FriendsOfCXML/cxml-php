<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\JMSSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpVersion(Rector\ValueObject\PhpVersion::PHP_84) //need this to enable PHP 8.4 features although lower requirement in composer
    ->withRules([
        ExplicitNullableParamTypeRector::class, // php8.4 feature, compatible with php8.0
    ])
    ->withSets([
        JMSSetList::ANNOTATIONS_TO_ATTRIBUTES,
        PHPUnitSetList::PHPUNIT_110,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES,
        SetList::TYPE_DECLARATION,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::PHP_82,
        SetList::PRIVATIZATION,
        SetList::EARLY_RETURN,
        SetList::INSTANCEOF,
        SetList::STRICT_BOOLEANS,
    ])
    ->withSkip([
        //allow to use promoted properties that only purpose is to get serialized
        \Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPromotedPropertyRector::class,
        //allow to use promoted properties that only purpose is to get serialized
        \Rector\DeadCode\Rector\ClassMethod\RemoveEmptyClassMethodRector::class,
        //this makes code unreadable
        \Rector\Strict\Rector\Ternary\BooleanInTernaryOperatorRuleFixerRector::class,
        \Rector\Strict\Rector\BooleanNot\BooleanInBooleanNotRuleFixerRector::class,
    ])
    ->withFileExtensions(['php'])
    ->withCache(
        cacheDirectory: '/tmp/rector',
        cacheClass: FileCacheStorage::class,
    )
    ->withParallel(
        maxNumberOfProcess: 4,
        jobSize: 16,
    )
    ->withImportNames(
        importDocBlockNames: false,
        removeUnusedImports: true,
    );
