<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Integration;

use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;

/**
 * Protects every readable PHP example and its versioned HTML preview
 */
final class ExamplesTest extends EmailTestCase
{
    public function testEveryPhpExampleReproducesItsHtmlPreview(): void
    {
        $examplesDirectory = dirname(__DIR__, 2) . '/examples';
        /** @var EmailGenerator $emails */
        $emails = require $examplesDirectory . '/app/bootstrap.php';
        $topLevelSources = glob($examplesDirectory . '/php/*.php');
        $componentSources = glob($examplesDirectory . '/php/components/*.php');
        self::assertIsArray($topLevelSources);
        self::assertIsArray($componentSources);
        $sources = [...$topLevelSources, ...$componentSources];
        sort($sources);

        foreach ($sources as $source) {
            $relativePath = substr($source, strlen($examplesDirectory . '/php/'));
            $example = substr($relativePath, 0, -4);
            $html = require $source;
            self::assertIsString($html);
            self::assertStringEqualsFile($examplesDirectory . '/html/' . $example . '.html', $html);
        }
    }
}
