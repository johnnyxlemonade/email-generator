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
     * Konstruktor Translatoru.
     *
     * @param LoggerInterface $logger Logger pro logování chyb a událostí.
     * @param string|null $dictionaryPath Cesta ke slovníkům (volitelná).
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
     * Nastaví aktuální jazyk.
     *
     * @param SupportedLanguage $language Jazykový kód.
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
     * Získá aktuální jazyk.
     *
     * @return string Aktuální jazyk.
     */
    public function getCurrentLanguage(): string
    {
        return $this->currentLanguage;
    }

    /**
     * Přeloží frázi nebo vrátí její výchozí hodnotu.
     *
     * @param string $key Klíč fráze.
     * @param array $parameters Parametry pro interpolaci.
     * @return string Přeložená fráze nebo výchozí klíč.
     */
    public function translate(string $key, array $parameters = []): string
    {
        // Získejte překlad, pokud existuje, nebo použijte fallback ve výchozím jazyce
        $translation = $this->dictionary[$key] ?? $this->baseDictionary[$key] ?? $key;

        // Interpolace hodnot, pokud jsou k dispozici
        foreach ($parameters as $placeholder => $value) {
            $translation = str_replace("{{{$placeholder}}}", $value, $translation);
        }

        return $translation;
    }

    /**
     * Aktualizace slovníku.
     *
     * @param SupportedLanguage $language Jazyk, který chceme aktualizovat nebo generovat.
     * @return void
     */
    public function updateLanguage(SupportedLanguage $language): void
    {
        $languageCode = $language->value;

        try {

            // Generování nebo aktualizace slovníku pro daný jazyk
            $data = $this->generateOrUpdateLanguage($language);

            // Sestavení cesty k souboru
            $file = $this->dictionaryPath . DIRECTORY_SEPARATOR . "$languageCode.php";

            // Zkontrolujeme, zda soubor existuje a zda jsou data totožná
            if (file_exists($file)) {
                $existingData = include $file;

                // Porovnání generovaných dat s existujícími daty
                if ($existingData === $data) {
                    $this->logger->info("Slovník pro jazyk '$languageCode' již existuje a je aktuální. Žádná aktualizace není nutná.");
                    return;
                }
            }

            // Pokud jsou data růzňá nebo soubor neexistuje, pokusíme se aktualizovat slovník
            if (file_put_contents($file, '<?php return ' . var_export($data, true) . ';') === false) {
                throw new \RuntimeException("Nepodařilo se zapsat do souboru: $file");
            }

            $this->logger->info("Slovník pro jazyk '$languageCode' byl úšpěšně aktualizován.");

        } catch (\Exception $e) {

            // Logování chyby
            $this->logger->error("Chyba při zpracování slovníku pro jazyk '$languageCode': " . $e->getMessage());
        }
    }

    /**
     * Aktualizuje nebo generuje slovníky pro vybrané nebo všechny podporované jazyky.
     *
     * @param SupportedLanguage[]|null $languages Pole jazyků, které chceme aktualizovat. Pokud je null, aktualizují se všechny jazyky.
     * @param bool $dryRun Pokud je true, metoda pouze simuluje změny bez skutečného zápisu do souborů.
     * @return void
     */
    public function generateOrUpdateAllLanguages(?array $languages = null, bool $dryRun = false): void
    {
        // Pokud není specifikované pole jazyků, použijeme všechny jazyky z `SupportedLanguage::cases()`
        $languagesToProcess = $languages ?? SupportedLanguage::cases();

        foreach ($languagesToProcess as $language) {
            try {
                $updatedDictionary = $this->generateOrUpdateLanguage($language);
                $filePath = $this->dictionaryPath . DIRECTORY_SEPARATOR . $language->value . '.php';

                // Zkontrolujeme, zda soubor již existuje a jestli se změnila data
                if (file_exists($filePath)) {
                    $existingData = include $filePath;

                    if ($existingData === $updatedDictionary) {

                        $this->logger->info("Slovník pro jazyk '{$language->value}' je aktuální. Žádná aktualizace není nutná.");

                        continue;
                    }
                }

                // Pokud je suchý režim aktivní, pouze logujeme, co by se změnilo
                if ($dryRun) {
                    $this->logger->info("Suchý režim: Slovník pro jazyk '{$language->value}' by byl aktualizován.");
                } else {
                    // Uložení aktualizovaného slovníku do souboru
                    if (file_put_contents($filePath, '<?php return ' . var_export($updatedDictionary, true) . ';') === false) {
                        throw new \RuntimeException("Nepodařilo se zapsat do souboru: $filePath");
                    }

                    $this->logger->info("Slovník pro jazyk '{$language->value}' byl úspěšně aktualizován.");
                }

            } catch (\Exception $e) {
                $this->logger->error("Chyba při generování slovníku pro jazyk '{$language->value}': " . $e->getMessage());
            }
        }
    }


    /**
     * Aktualizuje nebo generuje slovník pro daný jazyk na základě existujících překladů a referenčního seznamu klíčů.
     *
     * @param SupportedLanguage $language Jazyk, který chceme aktualizovat nebo generovat.
     * @return array Vygenerovaný nebo aktualizovaný slovník.
     */
    protected function generateOrUpdateLanguage(SupportedLanguage $language): array
    {
        // Načteme referenční slovník s klíči a výchozími českými překlady
        $referenceDictionary = ReferenceDictionary::getDictionary();

        // Načteme slovníky pro dané jazyky
        $languageDictionary = $this->loadDictionaryFromFile($language->value);
        $czechDictionary = $this->loadDictionaryFromFile(SupportedLanguage::LANG_CS->value);
        $englishDictionary = $this->loadDictionaryFromFile(SupportedLanguage::LANG_EN->value);

        // Generujeme nebo aktualizujeme slovník
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
     * Získá hodnotu pro daný klíč na základě preferencí a fallbacků.
     *
     * @param string $key Klíč, který hledáme.
     * @param SupportedLanguage $language Jazyk, který chceme aktualizovat nebo generovat.
     * @param array $languageDictionary Slovník pro aktuální jazyk.
     * @param array $czechDictionary Český slovník jako fallback.
     * @param array $englishDictionary Anglický slovník jako fallback.
     * @param string $defaultValue Výchozí hodnota z referenčního slovníku.
     * @return string Hodnota pro daný klíč.
     */
    private function resolveValue(
        string $key,
        SupportedLanguage $language,
        array $languageDictionary,
        array $czechDictionary,
        array $englishDictionary,
        string $defaultValue
    ): string {
        // Pokud hodnota existuje a není prázdná, použijeme ji
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
     * Zkontroluje, zda je hodnota platná (neprázdná a nenulová).
     *
     * @param mixed $value Hodnota k ověření.
     * @return bool True, pokud je hodnota platná, jinak false.
     */
    private function isValidValue(mixed $value): bool
    {
        return $value !== null && $value !== '';
    }

    /**
     * Vrátí první platnou hodnotu z předaných argumentů.
     *
     * @param mixed ...$values Seznam hodnot k ověření.
     * @return string První platná hodnota, pokud existuje, jinak poslední hodnota.
     */
    private function getValidValue(mixed ...$values): string
    {
        foreach ($values as $value) {
            if ($this->isValidValue($value)) {
                return (string) $value;
            }
        }
        // Vždy vracíme poslední hodnotu jako výchozí (musí být `string`)
        return (string) end($values);
    }

    /**
     * Načte slovník ze souboru.
     *
     * @param string $language Jazykový kód.
     * @return array Slovník pro daný jazyk.
     */
    public function loadDictionaryFromFile(string $language): array
    {
        // Zkontrolujeme, zda už máme slovník uložený v cache
        if (isset($this->cachedDictionaries[$language])) {
            return $this->cachedDictionaries[$language];
        }

        $filePath = $this->dictionaryPath . "/$language.php";

        if (!file_exists($filePath)) {
            $this->logger->warning("Slovník pro jazyk '$language' nenalezen: $filePath");
            $this->cachedDictionaries[$language] = []; // Uložíme prázdný slovník do cache
            return [];
        }

        $dictionary = require $filePath;

        // Zkontrolujeme, zda vrácená hodnota je pole, pokud ne, logujeme a vrátíme prázdné pole
        if (!is_array($dictionary)) {
            $this->logger->warning("Slovník pro jazyk '$language' není platný (očekáváno pole): $filePath");
            $this->cachedDictionaries[$language] = []; // Uložíme prázdný slovník do cache
            return [];
        }

        $this->cachedDictionaries[$language] = $dictionary; // Uložíme do cache
        return $dictionary;
    }

    /**
     * Načte slovník s fallback mechanismem.
     *
     * @param string $language Jazykový kód.
     * @return array Slovník pro daný jazyk s fallbackem na obecnější jazyk.
     */
    private function loadDictionaryWithFallback(string $language): array
    {
        $dictionary = [];

        // Pokud jazykový kód obsahuje regionální specifikaci (např. "en-US")
        $parts = explode('-', $language);
        while (count($parts) > 0) {
            $langCode = implode('-', $parts);
            $dictionary = array_merge($this->loadDictionaryFromFile($langCode), $dictionary);
            array_pop($parts); // Odstraníme regionální část a pokračujeme s obecnějším jazykem
        }

        return $dictionary;
    }
}
