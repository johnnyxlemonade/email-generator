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
     * Initializes the Logger with configuration.
     *
     * @param FileLoggerConfig $config Configuration for the logger.
     */
    public function __construct(FileLoggerConfig $config)
    {
        // Create the log directory if it does not exist
        if (!is_dir($config->getLogDirectory())) {
            mkdir($config->getLogDirectory(), 0777, true);
        }

        // Create the logger
        $this->logger = new Logger("LemonadeEmailGenerator");

        // Use RotatingFileHandler for logging with rotation based on configuration
        $handler = new RotatingFileHandler($config->getLogDirectory() . '/app.log', $config->getMaxFiles(), $config->getLogLevel(), true, 0644);
        $handler->setFilenameFormat('{filename}-{date}', RotatingFileHandler::FILE_PER_DAY);

        // Add the handler to the logger
        $this->logger->pushHandler($handler);
    }

    /**
     * Closes the logger and releases resources.
     */
    public function close(): void
    {
        foreach ($this->logger->getHandlers() as $handler) {
            $handler->close();
        }
    }

    /**
     * Logs a message with the given level.
     *
     * @param string|int|Level $level The log level.
     * @param Stringable|string $message The message to log.
     * @param array<string, mixed> $context Contextual data.
     * @return void
     *
     * @throws InvalidArgumentException If the log level is not supported.
     */
    public function log($level, Stringable|string $message, array $context = []): void
    {
        if (!is_string($level) && !is_int($level) && !($level instanceof Level)) {
            throw new InvalidArgumentException('$level must be a string, integer, or instance of ' . Level::class);
        }

        $this->logger->log($level, (string)$message, $context);
    }

    /**
     * Logs an error message.
     *
     * @param Stringable|string $message The message to log.
     * @param array<string, mixed> $context Contextual data.
     * @return void
     */
    public function error(Stringable|string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    /**
     * Logs an informational message.
     *
     * @param Stringable|string $message The message to log.
     * @param array<string, mixed> $context Contextual data.
     * @return void
     */
    public function info(Stringable|string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    /**
     * Logs a warning message.
     *
     * @param Stringable|string $message The message to log.
     * @param array<string, mixed> $context Contextual data.
     * @return void
     */
    public function warning(Stringable|string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }
}