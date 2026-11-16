<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Exception;

use InvalidArgumentException;

/**
 * Reports invalid content or configuration with a stable error code
 */
final class InvalidInputException extends InvalidArgumentException
{
    /**
     * Associates an input error code with its human-readable message
     */
    public function __construct(private readonly ErrorCode $errorCode, string $message)
    {
        parent::__construct($message);
    }

    /**
     * Returns the stable input validation error code
     */
    public function errorCode(): ErrorCode
    {
        return $this->errorCode;
    }
}
