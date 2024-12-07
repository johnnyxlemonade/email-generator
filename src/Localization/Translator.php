<?php

namespace Lemonade\EmailGenerator\Localization;

use Psr\Log\LoggerInterface;

class Translator
{

    /**
     * Default path to dictionary
     */
    private const DICTIONARY_PATH = __DIR__ . '/dictionaries';

    /**
     * @var SupportedLanguage
     */
    private SupportedLanguage $defaultLanguage;

    /**
     * @var SupportedLanguage
     */
    private SupportedLanguage $currentLanguage;

    private array $dictionary = [];
    private array $baseDictionary = [];
    private array $cachedDictionaries = [];

    private string $dictionaryPath;


    /**
     * Constructor for Translator.
     *
     * @param SupportedLanguage $currentLanguage
     * @param LoggerInterface $logger Logger for logging errors and events.
     */
    public function __construct(SupportedLanguage $currentLanguage, protected readonly LoggerInterface $logger)
    {

        $this->defaultLanguage = SupportedLanguage::LANG_CS;
        $this->currentLanguage = $currentLanguage;

        // We will not load the dictionaries immediately, so that lazy-loading can be used
        $this->dictionary = [];
        $this->baseDictionary = [];
    }

    /**
     * Adds or overrides a translation key in the current dictionary or base dictionary.
     *
     * @param string $key The key of the phrase.
     * @param string $value The translation for the key.
     * @param bool $overrideBase Whether to override the base dictionary as well (default: false).
     * @return void
     */
    public function addOrOverrideTranslation(string $key, string $value, bool $overrideBase = false): void
    {
        // Ensure the dictionary is loaded (lazy-load if necessary)
        if (empty($this->dictionary)) {
            $this->initializeDictionary();
        }

        // Update the currently loaded dictionary
        $this->dictionary[$key] = $value;

        // Optionally update the base dictionary
        if ($overrideBase) {
            $this->baseDictionary[$key] = $value;

            // Ensure cache reflects this update for future lazy-loads
            $this->cachedDictionaries[$this->defaultLanguage->value][$key] = $value;
        }

        // Ensure the cached dictionary for the current language is updated
        $this->cachedDictionaries[$this->currentLanguage->value][$key] = $value;

        $this->logger->info("Translation for key '$key' has been added or overridden.");
    }

    /**
     * Sets the current language.
     * This method updates the current language used by the Translator and then re-initializes the dictionary to reflect the new language settings.
     *
     * @param SupportedLanguage $language Language code.
     * @return self Returns the current instance of the Translator, allowing for method chaining.
     */
    public function setLanguage(SupportedLanguage $language): self
    {

        if ($this->currentLanguage !== $language) {
            $this->currentLanguage = $language;

            // Lazy-load dictionary for the new language
            $this->dictionary = [];
            $this->baseDictionary = [];
            $this->initializeDictionary();
        }

        return $this;
    }

    /**
     * Gets the current language.
     *
     * @return string The current language.
     */
    public function getCurrentLanguage(): string
    {
        return $this->currentLanguage->value;
    }

    /**
     * Returns the current dictionary.
     *
     * @return array The current dictionary for the set language.
     */
    public function getDictionary(): array
    {
        return $this->dictionary;
    }

    /**
     * Translates a phrase or returns its default value.
     *
     * @param string $key The key of the phrase.
     * @param array $parameters Parameters for interpolation.
     * @return string The translated phrase or the default key.
     */
    public function translate(string $key, array $parameters = []): string
    {
        // Lazy-load dictionary if not already initialized
        if (empty($this->dictionary)) {
            $this->initializeDictionary();
        }

        // Get the translation if it exists, or use fallback in the default language
        $translation = $this->dictionary[$key] ?? $this->baseDictionary[$key] ?? $key;

        // Log if the translation key is missing in both dictionaries
        if ($translation === $key) {
            $this->logger->warning("Translation for key '$key' not found in any dictionary.");
        }

        // Interpolate values if available
        foreach ($parameters as $placeholder => $value) {
            $translation = str_replace("{{{$placeholder}}}", $value, $translation);
        }

        return $translation;
    }

    /**
     * Loads a dictionary from a file.
     *
     * @param SupportedLanguage $language The language code.
     * @return array The dictionary for the given language.
     */
    public function loadDictionaryFromFile(SupportedLanguage $language): array
    {

        // Check if the dictionary is already cached
        if (isset($this->cachedDictionaries[$language->value])) {
            $this->logger->info("Dictionary for language '$language->value' using cache.");
            return $this->cachedDictionaries[$language->value];
        }

        $filePath = self::DICTIONARY_PATH . DIRECTORY_SEPARATOR . "$language->value.php";

        if (!file_exists($filePath)) {
            $this->logger->warning("Dictionary for language '{$language->value}' not found: $filePath");
            $this->cachedDictionaries[$language->value] = [];  // Cache an empty dictionary
            return [];
        }

        $dictionary = require $filePath;

        // Check if the returned value is an array, if not, log a warning and return an empty array
        if (!is_array($dictionary)) {
            $this->logger->warning("Dictionary for language '{$language->value}' is not valid (expected an array): $filePath");
            $this->cachedDictionaries[$language->value] = []; // Cache an empty dictionary
            return [];
        }

        $this->cachedDictionaries[$language->value] = $dictionary; // Cache the dictionary

        return $dictionary;
    }

    /**
     * Initializes the dictionary based on the current language setting.
     * It loads the default base dictionary and merges it with the current language dictionary if necessary.
     *
     * @return void
     */
    private function initializeDictionary(): void
    {
        $this->baseDictionary = $this->loadDictionaryFromFile($this->defaultLanguage);

        if ($this->currentLanguage !== $this->defaultLanguage) {
            $this->dictionary = array_merge(
                $this->baseDictionary,
                $this->loadDictionaryWithFallback($this->currentLanguage)
            );
        } else {
            $this->dictionary = $this->baseDictionary;
        }
    }

    /**
     * Loads a dictionary with a fallback mechanism.
     *
     * @param SupportedLanguage $language The language code.
     * @return array The dictionary for the given language with fallback to a more general language.
     */
    private function loadDictionaryWithFallback(SupportedLanguage $language): array
    {
        // Load the dictionary for the given language
        $dictionary = $this->loadDictionaryFromFile($language);

        // Merge the language dictionary with the base dictionary to use fallback for missing translations
        return array_merge($this->baseDictionary, $dictionary);
    }
}
