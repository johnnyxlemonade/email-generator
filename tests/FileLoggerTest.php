<?php

namespace Lemonade\EmailGenerator\Tests\Logger;

use Lemonade\EmailGenerator\Logger\FileLogger;
use Lemonade\EmailGenerator\Logger\FileLoggerConfig;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RuntimeException;

class FileLoggerTest extends TestCase
{
    private string $logDir;

    /**
     * Příprava před spuštěním každého testu.
     * Vytvoření unikátního adresáře pro ukládání testovacích logů.
     */
    protected function setUp(): void
    {

        parent::setUp();

        // Unikátní logovací adresář pro každý test
        $this->logDir = __DIR__ . '/test_logs_' . uniqid();

        // Vytvoření adresáře pro testovací logy, pokud neexistuje
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0777, true);
        }
    }

    /**
     * Úklid po spuštění každého testu.
     * Smazání adresáře, aby se zabránilo nechtěným souborům na disku.
     */
    protected function tearDown(): void
    {
        // Smazání adresáře s testovacími logy po každém testu
        if (is_dir($this->logDir)) {
            $this->removeDirectory($this->logDir);
        }
    }

    /**
     * Rekurzivní mazání adresáře a jeho obsahu.
     * @param string $directory
     */
    private function removeDirectory(string $directory): void
    {
        foreach (glob($directory . '/*') as $file) {
            if (is_dir($file)) {
                $this->removeDirectory($file);
            } else {
                unlink($file);
            }
        }
        rmdir($directory);
    }

    /**
     * Testuje inicializaci FileLoggeru s konfigurací a vytváření logovacího souboru.
     */
    public function testLoggerInitialization(): void
    {
        $config = new FileLoggerConfig(
            logDirectory: $this->logDir,
            logLevel: LogLevel::DEBUG,
            maxFiles: 7
        );

        $logger = new FileLogger($config);

        // Ověření, že logger je instance LoggerInterface
        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }

    /**
     * Testuje záznam logovací zprávy a vytvoření logovacího souboru.
     */
    public function testLogMessage(): void
    {
        $config = new FileLoggerConfig(
            logDirectory: $this->logDir,
            logLevel: LogLevel::DEBUG,
            maxFiles: 7
        );

        $logger = new FileLogger($config);

        // Zalogování zprávy
        $logger->info('Testovací logovací zpráva.');

        // Zavření loggeru pro uvolnění zdrojů
        $logger->close();

        // Cesta k očekávanému logovacímu souboru
        $logFile = $this->logDir . '/app-' . date('Y-m-d') . '.log';

        // Ověření, že soubor byl vytvořen
        $this->assertFileExists($logFile);

        // Ověření, že soubor obsahuje logovanou zprávu
        $logContent = file_get_contents($logFile);
        $this->assertStringContainsString('Testovací logovací zpráva.', $logContent);
    }

    /**
     * Testuje různé úrovně logování.
     *
     * Ověříme, že různé logovací úrovně správně zapíšou zprávy do souboru.
     * Tento test zajišťuje, že všechny úrovně (DEBUG, INFO, WARNING, ERROR) jsou správně zaznamenány.
     */
    public function testLogDifferentLevels(): void
    {
        $config = new FileLoggerConfig(
            logDirectory: $this->logDir,
            logLevel: LogLevel::DEBUG,
            maxFiles: 7
        );

        $logger = new FileLogger($config);

        // Zalogování různých úrovní zpráv
        $logger->debug('Debug zpráva.');
        $logger->info('Info zpráva.');
        $logger->warning('Warning zpráva.');
        $logger->error('Error zpráva.');

        // Zavření loggeru pro uvolnění zdrojů
        $logger->close();

        // Cesta k očekávanému logovacímu souboru
        $logFile = $this->logDir . '/app-' . date('Y-m-d') . '.log';

        // Ověření, že soubor byl vytvořen
        $this->assertFileExists($logFile);

        // Ověření, že soubor obsahuje všechny logované zprávy
        $logContent = file_get_contents($logFile);
        $this->assertStringContainsString('Debug zpráva.', $logContent);
        $this->assertStringContainsString('Info zpráva.', $logContent);
        $this->assertStringContainsString('Warning zpráva.', $logContent);
        $this->assertStringContainsString('Error zpráva.', $logContent);
    }

}



