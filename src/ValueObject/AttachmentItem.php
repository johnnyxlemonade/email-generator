<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\ValueObject;

use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Validation\InputValidator;

/**
 * Validated metadata for one file represented in an attachment list
 */
final readonly class AttachmentItem
{
    /**
     * Validates a display filename, optional byte count and optional download URL
     */
    public function __construct(
        private string $filename,
        private ?int $bytes = null,
        private ?string $url = null,
    ) {
        InputValidator::text($filename);

        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        if (
            trim($filename) === ''
            || str_contains($filename, '/')
            || str_contains($filename, '\\')
            || preg_match('/\A[a-zA-Z0-9]{1,12}\z/', $extension) !== 1
        ) {
            throw new InvalidInputException(
                ErrorCode::AttachmentFilenameInvalid,
                'Attachment filename must contain a safe file extension and must not contain a path.',
            );
        }

        if ($bytes !== null) {
            InputValidator::range($bytes, 0, PHP_INT_MAX, 'Attachment size');
        }

        if ($url !== null) {
            InputValidator::url($url);
        }
    }

    /**
     * Returns the display filename
     */
    public function filename(): string
    {
        return $this->filename;
    }

    /**
     * Derives an uppercase file type from the validated extension
     */
    public function type(): string
    {
        return strtoupper(pathinfo($this->filename, PATHINFO_EXTENSION));
    }

    /**
     * Returns the optional file size in bytes
     */
    public function bytes(): ?int
    {
        return $this->bytes;
    }

    /**
     * Returns the optional absolute HTTP(S) download URL
     */
    public function url(): ?string
    {
        return $this->url;
    }
}
