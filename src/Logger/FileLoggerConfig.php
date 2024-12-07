<?php

namespace Lemonade\EmailGenerator\Logger;

use InvalidArgumentException;
use Psr\Log\LogLevel;
use RuntimeException;

class FileLoggerConfig
{

    private const DEFAULT_LOG_DIRECTORY = __DIR__ . '/../../../../../logs';

    /**
     * Constructor for FileLoggerConfig.
     *
     * @param string $logDirectory Path to the log directory.
     * @param string $logLevel Logging level (e.g., DEBUG, INFO).
     * @param int $maxFiles Number of days to retain logs.
     * @throws InvalidArgumentException If the parameters are invalid.
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
     * Returns the path to the log directory.
     *
     * @return string
     */
    public function getLogDirectory(): string
    {
        return $this->logDirectory;
    }

    /**
     * Returns the logging level.
     *
     * @return string
     */
    public function getLogLevel(): string
    {
        return $this->logLevel;
    }

    /**
     * Returns the number of days to retain logs.
     *
     * @return int
     */
    public function getMaxFiles(): int
    {
        return $this->maxFiles;
    }

    /**
     * Validates the existence of the directory and creates it if it does not exist.
     *
     * @param string $logDirectory Path to the log directory.
     * @throws RuntimeException If the directory cannot be created or is not writable.
     */
    private function validateAndCreateLogDirectory(string $logDirectory): void
    {
        $parentDirectory = dirname($logDirectory);

        if (!is_dir($logDirectory)) {
            if (!is_writable($parentDirectory)) {
                throw new RuntimeException("The parent directory '$parentDirectory' is not writable. Cannot create '$logDirectory'.");
            }

            // Attempt to create the directory
            if (!mkdir($logDirectory, 0755, true)) {
                throw new RuntimeException("Failed to create the log directory: $logDirectory. Check write permissions.");
            }
        }

        if (!is_writable($logDirectory)) {
            throw new RuntimeException("The log directory: $logDirectory is not writable. Check permissions.");
        }
    }

    /**
     * Validates whether the given log level is valid.
     *
     * @param string $logLevel The logging level.
     * @throws InvalidArgumentException If the log level is invalid.
     */
    private function validateLogLevel(string $logLevel): void
    {
        $validLogLevels = [
            LogLevel::DEBUG, LogLevel::INFO, LogLevel::NOTICE,
            LogLevel::WARNING, LogLevel::ERROR, LogLevel::CRITICAL,
            LogLevel::ALERT, LogLevel::EMERGENCY
        ];

        if (!in_array($logLevel, $validLogLevels, true)) {
            throw new InvalidArgumentException("Invalid log level: $logLevel");
        }
    }
}

