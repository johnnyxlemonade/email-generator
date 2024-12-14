<?php

namespace Lemonade\EmailGenerator\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Lemonade\EmailGenerator\Localization\Translator;
use Lemonade\EmailGenerator\Localization\SupportedLanguage;

class TranslatorTest extends TestCase
{
    private LoggerInterface $logger;
    private Translator $translator;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testTranslateWithFallbackToBaseDictionary(): void
    {
        // Initialize with current language as EN
        $this->translator = new Translator(SupportedLanguage::LANG_EN, $this->logger);

        // Base dictionary contains the key, but not the specific language
        $this->translator->setLanguage(SupportedLanguage::LANG_EN);
        $translation = $this->translator->translate('attachmentListLabel');

        // Assert the translation falls back to base dictionary
        $this->assertEquals('Attachments', $translation);
    }

    public function testLoggerWarningOnMissingTranslation(): void
    {
        // Initialize translator
        $this->translator = new Translator(SupportedLanguage::LANG_EN, $this->logger);

        // Expect a warning log for a missing translation
        $this->logger->expects($this->once())
            ->method('warning')
            ->with($this->stringContains("Translation for key 'missing_key' not found in any dictionary."));

        // Attempt to translate a non-existing key
        $this->translator->translate('missing_key');
    }

    public function testLazyLoadingDictionary(): void
    {
        // Lazy-load is triggered on the first translate call
        $this->translator = new Translator(SupportedLanguage::LANG_EN, $this->logger);

        // Simulate empty dictionary before lazy-loading
        $this->assertEmpty($this->translator->getDictionary());

        // First translate call should trigger dictionary loading
        $this->translator->translate('test_key');
        $this->assertNotEmpty($this->translator->getDictionary());
    }

    public function testDictionaryCaching(): void
    {
        // Initialize translator
        $this->translator = new Translator(SupportedLanguage::LANG_EN, $this->logger);

        // Ensure dictionary is initialized (force lazy-load)
        $this->translator->translate('test_key');

        // Load dictionary and verify it's cached
        $cachedDictionary = $this->translator->getDictionary();

        $this->assertNotEmpty($cachedDictionary, "Dictionary should not be empty after initialization.");
        $this->assertSame($cachedDictionary, $this->translator->getDictionary(), "Cached dictionary should remain the same.");
    }

    public function testSetLanguageChangesCurrentLanguage(): void
    {
        // Initialize with current language as CS
        $this->translator = new Translator(SupportedLanguage::LANG_CS, $this->logger);

        // Set language to EN and check if dictionary changes accordingly
        $this->translator->setLanguage(SupportedLanguage::LANG_EN);
        $this->assertEquals('en', $this->translator->getCurrentLanguage());
    }

    public function testTranslateWithInterpolation(): void
    {
        // Initialize translator with English language
        $this->translator = new Translator(SupportedLanguage::LANG_EN, $this->logger);

        // Mock dictionary to include a translation with placeholders
        $this->translator->setLanguage(SupportedLanguage::LANG_EN);
        $translation = $this->translator->translate('lostPasswordPartOne', ['webName' => 'myWebsiteName']);

        // Assert interpolation works correctly
        $this->assertEquals('We received your request to create a new password for logging in to your account on the myWebsiteName website.', $translation);
    }

    public function testAddOrOverrideTranslation(): void
    {
        // Initialize translator with English language
        $this->translator = new Translator(SupportedLanguage::LANG_EN, $this->logger);

        // Ensure the initial dictionary does not contain the key
        $this->assertArrayNotHasKey('customKey', $this->translator->getDictionary());

        // Add a new translation dynamically
        $this->translator->addOrOverrideTranslation('customKey', 'Custom Value');

        // Verify the translation is immediately available
        $this->assertEquals('Custom Value', $this->translator->translate('customKey'));

        // Override the translation dynamically
        $this->translator->addOrOverrideTranslation('customKey', 'Updated Value');

        // Verify the updated translation
        $this->assertEquals('Updated Value', $this->translator->translate('customKey'));

        // Verify the change persists in the dictionary cache
        $cachedDictionary = $this->translator->getDictionary();
        $this->assertArrayHasKey('customKey', $cachedDictionary);
        $this->assertEquals('Updated Value', $cachedDictionary['customKey']);
    }

}
