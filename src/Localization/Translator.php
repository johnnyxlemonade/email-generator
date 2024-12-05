<?php

namespace Lemonade\EmailGenerator\Localization;

use Psr\Log\LoggerInterface;

class Translator
{
    private const DEFAULT_LANGUAGE = "cs";
    private const DICTIONARY_PATH = __DIR__ . '/dictionaries';

    private string $currentLanguage;
    private array $dictionary = [];
    private array $baseDictionary = [];
    private array $cachedDictionaries = [];
    private LoggerInterface $logger;
    private string $dictionaryPath;

    /**
     * Constructor for Translator.
     *
     * @param LoggerInterface $logger Logger for logging errors and events.
     * @param string|null $dictionaryPath Path to the dictionaries (optional).
     */
    public function __construct(LoggerInterface $logger, ?string $dictionaryPath = null)
    {
        $this->currentLanguage = self::DEFAULT_LANGUAGE;
        $this->logger = $logger;
        $this->dictionaryPath = $dictionaryPath ?? self::DICTIONARY_PATH;

        $this->baseDictionary = $this->loadDictionaryFromFile(self::DEFAULT_LANGUAGE);
        $this->dictionary = $this->baseDictionary;
    }

    /**
     * Sets the current language.
     *
     * @param SupportedLanguage $language Language code.
     */
    public function setLanguage(SupportedLanguage $language): void
    {
        $this->currentLanguage = $language->value;

        if ($this->currentLanguage !== self::DEFAULT_LANGUAGE) {
            $this->dictionary = array_merge(
                $this->baseDictionary,
                $this->loadDictionaryWithFallback($this->currentLanguage)
            );
        } else {
            $this->dictionary = $this->baseDictionary;
        }
    }

    /**
     * Gets the current language.
     *
     * @return string The current language.
     */
    public function getCurrentLanguage(): string
    {
        return $this->currentLanguage;
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
        // Get the translation if it exists, or use fallback in the default language
        $translation = $this->dictionary[$key] ?? $this->baseDictionary[$key] ?? $key;

        // Interpolate values if available
        foreach ($parameters as $placeholder => $value) {
            $translation = str_replace("{{{$placeholder}}}", $value, $translation);
        }

        return $translation;
    }

    /**
     * Updates the dictionary.
     *
     * @param SupportedLanguage $language The language to update or generate.
     * @return void
     */
    public function updateLanguage(SupportedLanguage $language): void
    {
        $languageCode = $language->value;

        try {

            // Generate or update the dictionary for the given language
            $data = $this->generateOrUpdateLanguage($language);

            // Build the file path
            $file = $this->dictionaryPath . DIRECTORY_SEPARATOR . "$languageCode.php";

            // Check if the file exists and if the data matches
            if (file_exists($file)) {
                $existingData = include $file;

                // Compare generated data with existing data
                if ($existingData === $data) {
                    $this->logger->info("Dictionary for language '$languageCode' already exists and is up to date. No update needed.");
                    return;
                }
            }

            // If data is different or file does not exist, try to update the dictionary
            if (file_put_contents($file, '<?php return ' . var_export($data, true) . ';') === false) {
                throw new \RuntimeException("Failed to write to file: $file");
            }

            $this->logger->info("Dictionary for language '$languageCode' has been successfully updated.");

        } catch (\Exception $e) {

            // Log the error
            $this->logger->error("Error processing dictionary for language '$languageCode': " . $e->getMessage());
        }
    }

    /**
     * Updates or generates dictionaries for selected or all supported languages.
     *
     * @param SupportedLanguage[]|null $languages Array of languages to update. If null, all languages are updated.
     * @param bool $dryRun If true, the method only simulates changes without actually writing to files.
     * @return void
     */
    public function generateOrUpdateAllLanguages(?array $languages = null, bool $dryRun = false): void
    {
        // If no specific array of languages is provided, use all languages from `SupportedLanguage::cases()`
        $languagesToProcess = $languages ?? SupportedLanguage::cases();

        foreach ($languagesToProcess as $language) {
            try {
                $updatedDictionary = $this->generateOrUpdateLanguage($language);
                $filePath = $this->dictionaryPath . DIRECTORY_SEPARATOR . $language->value . '.php';

                // Check if the file already exists and if the data has changed
                if (file_exists($filePath)) {
                    $existingData = include $filePath;

                    if ($existingData === $updatedDictionary) {

                        $this->logger->info("Dictionary for language '{$language->value}' is up to date. No update needed.");

                        continue;
                    }
                }

                // If dry run is active, only log what would be changed
                if ($dryRun) {

                    $this->logger->info("Dry run: Dictionary for language '{$language->value}' would be updated.");

                } else {

                    // Save the updated dictionary to the file
                    if (file_put_contents($filePath, '<?php return ' . var_export($updatedDictionary, true) . ';') === false) {
                        throw new \RuntimeException("Failed to write to file: $filePath");
                    }

                    $this->logger->info("Dictionary for language '{$language->value}' has been successfully updated.");
                }

            } catch (\Exception $e) {
                $this->logger->error("Error generating dictionary for language '{$language->value}': " . $e->getMessage());
            }
        }
    }

    /**
     * Updates or generates the dictionary for the given language based on existing translations and the reference key list.
     *
     * @param SupportedLanguage $language The language to update or generate.
     * @return array The generated or updated dictionary.
     */
    protected function generateOrUpdateLanguage(SupportedLanguage $language): array
    {
        // Load the reference dictionary with keys and default Czech translations
        $referenceDictionary = ReferenceDictionary::getDictionary();

        // Load dictionaries for the given languages
        $languageDictionary = $this->loadDictionaryFromFile($language->value);
        $czechDictionary = $this->loadDictionaryFromFile(SupportedLanguage::LANG_CS->value);
        $englishDictionary = $this->loadDictionaryFromFile(SupportedLanguage::LANG_EN->value);

        // Generate or update the dictionary
        $updatedDictionary = [];

        foreach ($referenceDictionary as $key => $defaultValue) {
            $updatedDictionary[$key] = $this->resolveValue(
                $key,
                $language,
                $languageDictionary,
                $czechDictionary,
                $englishDictionary,
                $defaultValue
            );
        }

        return $updatedDictionary;
    }

    /**
     * Retrieves the value for a given key based on preferences and fallbacks.
     *
     * @param string $key The key to search for.
     * @param SupportedLanguage $language The language to update or generate.
     * @param array $languageDictionary The dictionary for the current language.
     * @param array $czechDictionary The Czech dictionary as a fallback.
     * @param array $englishDictionary The English dictionary as a fallback.
     * @param string $defaultValue The default value from the reference dictionary.
     * @return string The value for the given key.
     */
    private function resolveValue(
        string $key,
        SupportedLanguage $language,
        array $languageDictionary,
        array $czechDictionary,
        array $englishDictionary,
        string $defaultValue
    ): string {

        // Use the value if it exists and is not empty
        if ($this->isValidValue($languageDictionary[$key] ?? null)) {
            return $languageDictionary[$key];
        }

        return match ($language) {
            SupportedLanguage::LANG_CS => $defaultValue,
            SupportedLanguage::LANG_SK, SupportedLanguage::LANG_EN => $this->getValidValue($czechDictionary[$key] ?? null, $defaultValue),
            default => $this->getValidValue(
                $englishDictionary[$key] ?? null,
                $czechDictionary[$key] ?? null,
                $defaultValue
            ),
        };
    }

    /**
     * Checks if the value is valid (non-empty and non-null).
     *
     * @param mixed $value The value to verify.
     * @return bool True if the value is valid, false otherwise.
     */
    private function isValidValue(mixed $value): bool
    {
        return $value !== null && $value !== '';
    }

    /**
     * Returns the first valid value from the given arguments.
     *
     * @param mixed ...$values List of values to verify.
     * @return string The first valid value if it exists, otherwise the last value.
     */
    private function getValidValue(mixed ...$values): string
    {
        foreach ($values as $value) {
            if ($this->isValidValue($value)) {
                return (string) $value;
            }
        }
        // Always return the last value as the default (must be `string`)
        return (string) end($values);
    }

    /**
     * Loads a dictionary from a file.
     *
     * @param string $language The language code.
     * @return array The dictionary for the given language.
     */
    public function loadDictionaryFromFile(string $language): array
    {
        // Check if the dictionary is already cached
        if (isset($this->cachedDictionaries[$language])) {
            return $this->cachedDictionaries[$language];
        }

        $filePath = $this->dictionaryPath . "/$language.php";

        if (!file_exists($filePath)) {

            $this->logger->warning("Dictionary for language '$language' not found: $filePath");
            $this->cachedDictionaries[$language] = [];  // Cache an empty dictionary

            return [];
        }

        $dictionary = require $filePath;

        // Check if the returned value is an array, if not, log a warning and return an empty array
        if (!is_array($dictionary)) {

            $this->logger->warning("Dictionary for language '$language' is not valid (expected an array): $filePath");

            $this->cachedDictionaries[$language] = []; // Cache an empty dictionary

            return [];
        }

        $this->cachedDictionaries[$language] = $dictionary; // Cache the dictionary
        return $dictionary;
    }

    /**
     * Loads a dictionary with a fallback mechanism.
     *
     * @param string $language The language code.
     * @return array The dictionary for the given language with fallback to a more general language.
     */
    private function loadDictionaryWithFallback(string $language): array
    {
        $dictionary = [];

        // If the language code contains a regional specification (e.g., "en-US")
        $parts = explode('-', $language);
        while (count($parts) > 0) {
            $langCode = implode('-', $parts);
            $dictionary = array_merge($this->loadDictionaryFromFile($langCode), $dictionary);
            array_pop($parts); // Remove the regional part and continue with the more general language
        }

        return $dictionary;
    }
}
