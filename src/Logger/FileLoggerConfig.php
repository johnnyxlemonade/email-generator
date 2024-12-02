<?php

namespace Lemonade\EmailGenerator\Logger;

use InvalidArgumentException;
use Psr\Log\LogLevel;
use RuntimeException;

class FileLoggerConfig
{

    private const DEFAULT_LOG_DIRECTORY = __DIR__ . '/../../../../../logs';

    /**
     * Konstruktor FileLoggerConfig.
     *
     * @param string $logDirectory Cesta k adresáři logů.
     * @param string $logLevel Úroveň logování (např. DEBUG, INFO).
     * @param int $maxFiles Počet dní pro uchování logů.
     * @throws InvalidArgumentException Pokud jsou parametry neplatné.
     */
    public function __construct(
        protected readonly string $logDirectory = self::DEFAULT_LOG_DIRECTORY,
        protected readonly string $logLevel = LogLevel::DEBUG,
        protected readonly int $maxFiles = 14
    ) {
        $this->validateAndCreateLogDirectory($this->logDirectory);
        $this->validateLogLevel($this->logLevel);
    }

    /**
     * Vrací cestu k adresáři logů.
     *
     * @return string
     */
    public function getLogDirectory(): string
    {
        return $this->logDirectory;
    }

    /**
     * Vrací úroveň logování.
     *
     * @return string
     */
    public function getLogLevel(): string
    {
        return $this->logLevel;
    }

    /**
     * Vrací počet dní pro uchování logů.
     *
     * @return int
     */
    public function getMaxFiles(): int
    {
        return $this->maxFiles;
    }

    /**
     * Ověří existenci adresáře a vytvoří ho, pokud neexistuje.
     *
     * @param string $logDirectory Cesta k adresáři logů.
     * @throws RuntimeException Pokud se nepodaří adresář vytvořit nebo pokud adresář není zapisovatelný.
     */
    private function validateAndCreateLogDirectory(string $logDirectory): void
    {
        $parentDirectory = dirname($logDirectory);

        if (!is_dir($logDirectory)) {
            if (!is_writable($parentDirectory)) {
                throw new RuntimeException("Nadřazený adresář '$parentDirectory' není zapisovatelný. Nelze vytvořit '$logDirectory'.");
            }

            // Pokus o vytvoření adresáře
            if (!mkdir($logDirectory, 0777, true)) {
                throw new RuntimeException("Nepodařilo se vytvořit adresář pro logy: $logDirectory. Zkontrolujte oprávnění k zápisu.");
            }
        }

        if (!is_writable($logDirectory)) {
            throw new RuntimeException("Adresář pro logy: $logDirectory není zapisovatelný. Zkontrolujte oprávnění.");
        }
    }

    /**
     * Ověří, zda zadaná úroveň logování je platná.
     *
     * @param string $logLevel Úroveň logování.
     * @throws InvalidArgumentException Pokud je úroveň logování neplatná.
     */
    private function validateLogLevel(string $logLevel): void
    {
        $validLogLevels = [
            LogLevel::DEBUG, LogLevel::INFO, LogLevel::NOTICE,
            LogLevel::WARNING, LogLevel::ERROR, LogLevel::CRITICAL,
            LogLevel::ALERT, LogLevel::EMERGENCY
        ];

        if (!in_array($logLevel, $validLogLevels, true)) {
            throw new InvalidArgumentException("Neplatná úroveň logování: $logLevel");
        }
    }
}

