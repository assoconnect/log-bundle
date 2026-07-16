<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    // uncomment to reach your current PHP version
    // ->withPhpSets()
    ->withTypeCoverageLevel(0)
    ->withSets([
        __DIR__ . '/vendor/assoconnect/php-quality-config/src/Rector/rules.php',
    ])
    // Registered by php-quality-config but deprecated: rector >= 2.5.7 aborts when they match
    ->withSkip([
        \Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector::class,
        \Rector\CodingStyle\Rector\Closure\StaticClosureRector::class,
    ]);
