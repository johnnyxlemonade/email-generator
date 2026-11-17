<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\EmailWidth;

require dirname(__DIR__) . '/vendor/autoload.php';

$sourceDirectory = __DIR__ . '/php';
$previewDirectory = __DIR__ . '/html';
$sources = glob($sourceDirectory . '/*.php');

if ($sources === false) {
    throw new RuntimeException('Could not discover example source files.');
}

sort($sources);
$componentSources = array_filter(
    $sources,
    static fn(string $source): bool => str_starts_with(basename($source), 'component-'),
);
$variants = [];

foreach (EmailWidth::cases() as $width) {
    $variants[strtolower($width->name)] = $width;
}

$requestedVariant = $argv[1] ?? null;

if (count($argv) > 2 || ($requestedVariant !== null && !isset($variants[strtolower($requestedVariant)]))) {
    throw new RuntimeException(sprintf(
        'Usage: php examples/generate.php [%s]',
        implode('|', array_keys($variants)),
    ));
}

if ($requestedVariant === null) {
    foreach ($variants as $variant => $width) {
        /** @var array<int, resource> $pipes */
        $pipes = [];
        $process = proc_open(
            [PHP_BINARY, __FILE__, $variant],
            [
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
        );

        if (!is_resource($process)) {
            throw new RuntimeException(sprintf('Could not start the %s example generator.', $variant));
        }

        $childOutput = stream_get_contents($pipes[1]);
        $errorOutput = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        if ($exitCode !== 0 || $childOutput === false) {
            throw new RuntimeException(sprintf(
                'Could not generate %s examples: %s',
                $variant,
                trim($errorOutput === false ? '' : $errorOutput),
            ));
        }
    }

    $selectedVariants = $variants;
    $cleanupDirectory = $previewDirectory;
} else {
    $variant = strtolower($requestedVariant);
    $emailWidth = $variants[$variant];
    /** @var EmailGenerator $emails */
    $emails = require __DIR__ . '/app/bootstrap.php';

    foreach ($sources as $source) {
        $relativePath = substr($source, strlen($sourceDirectory) + 1);
        $previewPath = substr($relativePath, 0, -4) . '.html';
        $html = require $source;

        if (!is_string($html)) {
            throw new RuntimeException(sprintf('Example %s did not return HTML.', $relativePath));
        }

        $output = $previewDirectory . '/' . $variant . '/' . $previewPath;
        $outputDirectory = dirname($output);

        if (!is_dir($outputDirectory) && !mkdir($outputDirectory, 0775, true) && !is_dir($outputDirectory)) {
            throw new RuntimeException(sprintf('Could not create %s.', $outputDirectory));
        }
        if (file_put_contents($output, $html) === false) {
            throw new RuntimeException(sprintf('Could not write %s.', $output));
        }
    }

    $selectedVariants = [$variant => $emailWidth];
    $cleanupDirectory = $previewDirectory . '/' . $variant;
}

$expectedFiles = [];

foreach ($selectedVariants as $variant => $width) {
    foreach ($sources as $source) {
        $relativePath = substr($source, strlen($sourceDirectory) + 1);
        $previewPath = substr($relativePath, 0, -4) . '.html';
        $expectedFiles[$previewDirectory . '/' . $variant . '/' . $previewPath] = true;
    }
}

$existing = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($cleanupDirectory, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::CHILD_FIRST,
);

foreach ($existing as $item) {
    $path = $item->getPathname();

    if ($item->isFile() && $item->getExtension() === 'html' && !isset($expectedFiles[$path])) {
        if (!unlink($path)) {
            throw new RuntimeException(sprintf('Could not remove stale preview %s.', $path));
        }

        continue;
    }

    if ($item->isDir() && iterator_count(new FilesystemIterator($path)) === 0 && !rmdir($path)) {
        throw new RuntimeException(sprintf('Could not remove empty preview directory %s.', $path));
    }
}

$variantCount = count($selectedVariants);
$componentCount = count($componentSources) * $variantCount;

echo sprintf(
    'Generated %d previews (%d component previews) for %s.%s',
    count($sources) * $variantCount,
    $componentCount,
    implode(', ', array_keys($selectedVariants)),
    PHP_EOL,
);
