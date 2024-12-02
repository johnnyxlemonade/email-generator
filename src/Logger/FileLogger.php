<?php

namespace Lemonade\EmailGenerator\Logger;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Stringable;
use Psr\Log\InvalidArgumentException;

class FileLogger implements LoggerInterface
{
    use LoggerTrait;

    protected Logger $logger;

    /**
     * Inicializuje Logger s konfigurací.
     *
     * @param FileLoggerConfig $config Konfigurace loggeru.
     */
    public function __construct(FileLoggerConfig $config)
    {
        // Vytvoření adresáře pro logy, pokud neexistuje
        if (!is_dir($config->getLogDirectory())) {
            mkdir($config->getLogDirectory(), 0777, true);
        }

        // Vytvoření loggeru
        $this->logger = new Logger("LemonadeEmailGenerator");

        // Použití RotatingFileHandler pro logování s rotací na základě konfigurace
        $handler = new RotatingFileHandler($config->getLogDirectory() . '/app.log', $config->getMaxFiles(), $config->getLogLevel(), true, 0644);
        $handler->setFilenameFormat('{filename}-{date}', RotatingFileHandler::FILE_PER_DAY);

        // Přidání handleru do loggeru
        $this->logger->pushHandler($handler);
    }

    /**
     * Zavře logger a uvolní zdroje.
     */
    public function close(): void
    {
        foreach ($this->logger->getHandlers() as $handler) {
            $handler->close();
        }
    }

    /**
     * Metoda pro záznam logovací zprávy podle úrovně.
     *
     * @param string|int|Level $level Úroveň logování.
     * @param Stringable|string $message Zpráva k zalogování.
     * @param array<string, mixed> $context Kontextová data.
     * @return void
     *
     * @throws InvalidArgumentException Pokud úroveň logování není podporována.
     */
    public function log($level, Stringable|string $message, array $context = []): void
    {
        if (!is_string($level) && !is_int($level) && !($level instanceof Level)) {
            throw new InvalidArgumentException('$level must be a string, integer, or instance of ' . Level::class);
        }

        $this->logger->log($level, (string)$message, $context);
    }

    /**
     * Zaloguje chybovou zprávu.
     *
     * @param Stringable|string $message Zpráva k zalogování.
     * @param array<string, mixed> $context Kontextová data.
     * @return void
     */
    public function error(Stringable|string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    /**
     * Zaloguje informativní zprávu.
     *
     * @param Stringable|string $message Zpráva k zalogování.
     * @param array<string, mixed> $context Kontextová data.
     * @return void
     */
    public function info(Stringable|string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    /**
     * Zaloguje varování.
     *
     * @param Stringable|string $message Zpráva k zalogování.
     * @param array<string, mixed> $context Kontextová data.
     * @return void
     */
    public function warning(Stringable|string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }
}

