<?php

namespace Lemonade\EmailGenerator\Tests\Logger;

use Lemonade\EmailGenerator\Logger\FileLoggerConfig;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use InvalidArgumentException;
use RuntimeException;

class FileLoggerConfigTest extends TestCase
{
    private string $tempLogDir;

    /**
     * Příprava před spuštěním každého testu
     * Vytvoření dočasného adresáře pro ukládání testovacích logů.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Vytvoření dočasného adresáře pro testy
        $this->tempLogDir = sys_get_temp_dir() . '/logs_' . uniqid();
    }

    /**
     * Úklid po spuštění každého testu
     * Smazání dočasného adresáře, aby se zabránilo nechtěným souborům na disku.
     */
    protected function tearDown(): void
    {

        // Smazání dočasného adresáře po každém testu
        if (is_dir($this->tempLogDir)) {
            $this->removeDirectory($this->tempLogDir);
        }

        parent::tearDown();
    }

    /**
     * Rekurzivní mazání adresáře a jeho obsahu
     * @param string $directory
     */
    private function removeDirectory(string $directory): void
    {
        foreach (glob($directory . '/*') as $file) {
            if (is_dir($file)) {
                $this->removeDirectory($file);
            } else {
                if (!unlink($file)) {
                    echo "Varování: Nepodařilo se odstranit soubor $file\n";
                }
            }
        }
        if (!rmdir($directory)) {
            echo "Varování: Nepodařilo se odstranit adresář $directory\n";
        }
    }

    /**
     * Testuje, zda lze inicializovat FileLoggerConfig s výchozími hodnotami.
     */
    public function testInitializationWithDefaults(): void
    {
        $config = new FileLoggerConfig();

        // Ověření, že výchozí log level je DEBUG
        $this->assertSame(LogLevel::DEBUG, $config->getLogLevel());

        // Ověření, že výchozí počet dnů pro uchování logů je 14
        $this->assertSame(14, $config->getMaxFiles());

        // Ověření, že adresář pro logy existuje
        $this->assertDirectoryExists($config->getLogDirectory());
    }

    /**
     * Testuje, zda lze inicializovat FileLoggerConfig s vlastním adresářem pro logy.
     */
    public function testInitializationWithCustomLogDirectory(): void
    {
        $config = new FileLoggerConfig(logDirectory: $this->tempLogDir);

        // Ověření, že cesta k logům odpovídá nastavené hodnotě
        $this->assertSame($this->tempLogDir, $config->getLogDirectory());

        // Ověření, že adresář pro logy existuje
        $this->assertDirectoryExists($this->tempLogDir);
    }

    /**
     * Testuje, zda se vyhodí výjimka RuntimeException, pokud nelze vytvořit adresář pro logy.
     * Tento test je spuštěn pouze na Linuxových systémech.
     */
    public function testThrowsExceptionWhenCannotCreateLogDirectory(): void
    {

        // Ověření operačního systému a případné přeskočení testu
        if (DIRECTORY_SEPARATOR !== '/') {
            $this->markTestSkipped('Tento test je určen pouze pro Linuxové systémy.');
        }

        // Pro Linux použijeme /root nebo /proc, kde běžný uživatel nemá přístup
        $invalidDirectory = '/root/invalid_directory_' . uniqid();

        // Očekáváme, že se vyhodí RuntimeException
        $this->expectException(RuntimeException::class);

        new FileLoggerConfig(logDirectory: $invalidDirectory);
    }

    /**
     * Testuje, zda se vyhodí výjimka InvalidArgumentException při zadání neplatné úrovně logování.
     */
    public function testThrowsExceptionForInvalidLogLevel(): void
    {
        $invalidLogLevel = 'INVALID_LEVEL';

        // Očekáváme, že se vyhodí InvalidArgumentException s odpovídajícím textem
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Neplatná úroveň logování: $invalidLogLevel");

        new FileLoggerConfig(logLevel: $invalidLogLevel);
    }

    /**
     * Testuje inicializaci FileLoggerConfig s vlastním logovacím levelem (např. INFO).
     */
    public function testInitializationWithCustomLogLevel(): void
    {
        $config = new FileLoggerConfig(logLevel: LogLevel::INFO);

        // Ověření, že log level je správně nastaven na INFO
        $this->assertSame(LogLevel::INFO, $config->getLogLevel());
    }

    /**
     * Testuje inicializaci FileLoggerConfig s vlastním počtem maximálních uchovávaných logovacích souborů.
     */
    public function testInitializationWithCustomMaxFiles(): void
    {
        $customMaxFiles = 30;
        $config = new FileLoggerConfig(maxFiles: $customMaxFiles);

        // Ověření, že maximální počet souborů je správně nastaven na 30
        $this->assertSame($customMaxFiles, $config->getMaxFiles());
    }
}
