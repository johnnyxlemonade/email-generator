<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\EmailGenerator;

require dirname(__DIR__) . '/vendor/autoload.php';

/** @var EmailGenerator $emails */
$emails = require __DIR__ . '/app/bootstrap.php';
$sourceDirectory = __DIR__ . '/php';
$previewDirectory = __DIR__ . '/html';
$topLevelSources = glob($sourceDirectory . '/*.php');
$componentSources = glob($sourceDirectory . '/components/*.php');
if ($topLevelSources === false || $componentSources === false) {
    throw new RuntimeException('Could not discover example source files.');
}
$sources = [...$topLevelSources, ...$componentSources];
sort($sources);
$generated = [];
$componentCount = 0;

foreach ($sources as $source) {
    $relativePath = substr($source, strlen($sourceDirectory) + 1);
    $example = substr($relativePath, 0, -4);
    $html = require $source;
    if (!is_string($html)) {
        throw new RuntimeException(sprintf('Example %s did not return HTML.', $example));
    }
    $output = $previewDirectory . '/' . $example . '.html';
    $outputDirectory = dirname($output);
    if (!is_dir($outputDirectory) && !mkdir($outputDirectory, 0775, true) && !is_dir($outputDirectory)) {
        throw new RuntimeException(sprintf('Could not create %s.', $outputDirectory));
    }
    if (file_put_contents($output, $html) === false) {
        throw new RuntimeException(sprintf('Could not write %s.', $output));
    }
    $generated[] = $example . '.html';
    if (str_starts_with($relativePath, 'components/')) {
        ++$componentCount;
    }
}

echo sprintf(
    'Generated %d previews (%d component previews) in examples/html/.%s',
    count($generated),
    $componentCount,
    PHP_EOL,
);
