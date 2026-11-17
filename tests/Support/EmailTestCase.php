<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Support;

use DOMDocument;
use DOMXPath;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\EmailServiceProvider;
use Lemonade\EmailGenerator\Presentation\HtmlRenderer;
use Lemonade\Framework\Core\Context\ApplicationContext;
use Lemonade\Framework\Core\Context\DebugMode;
use Lemonade\Framework\Core\Context\Environment;
use Lemonade\Framework\Core\Context\Path;
use Lemonade\Framework\Localization\Config\LocalizationConfigDefinition;
use Lemonade\Framework\Localization\Config\LocalizationConfigResolver;
use Lemonade\Framework\Localization\FileTranslator;
use Lemonade\Framework\Localization\TranslationResourceRegistry;
use PHPUnit\Framework\TestCase;

/**
 * Provides fixture localization, email generation and HTML queries for package tests
 */
abstract class EmailTestCase extends TestCase
{
    protected FileTranslator $translator;
    protected EmailGenerator $emails;

    /**
     * Creates a testing application context rooted in the fixture directory
     */
    public static function context(): ApplicationContext
    {
        return new ApplicationContext(Environment::Testing, new Path(__DIR__ . '/../Fixtures', __DIR__ . '/../Fixtures/public'), new DebugMode(false));
    }

    /**
     * Initializes the test case with fixture translations and an email generator
     */
    public function __construct(string $name)
    {
        parent::__construct($name);
        $config = (new LocalizationConfigResolver())->resolve(
            LocalizationConfigDefinition::create()->defaultLocale('cs')->fallbackLocale('en')->supportedLocales(['cs', 'en']),
        );
        $resources = new TranslationResourceRegistry();
        $resources->register(dirname(__DIR__, 2) . '/resources/Language', EmailServiceProvider::class);
        $this->translator = new FileTranslator(self::context(), $config, $resources);
        $this->emails = new EmailGenerator(new HtmlRenderer($this->translator), $config->defaultLocale);
    }

    /**
     * Parses generated HTML for structural assertions while restoring libxml error handling
     */
    protected function xpath(string $html): DOMXPath
    {
        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);

        try {
            self::assertTrue($document->loadHTML($html));
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        return new DOMXPath($document);
    }
}
