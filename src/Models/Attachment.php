<?php

namespace Lemonade\EmailGenerator\Models;

/**
 * Class Attachment
 * Represents an attachment with details such as name, link, size, and file extension.
 */
class Attachment
{
    /**
     * @param string $name Name of the attachment
     * @param string $link URL link to the attachment
     * @param string|null $size Size of the attachment in bytes (optional)
     * @param string|null $extension File extension (optional)
     */
    public function __construct(
        protected readonly string $name,
        protected readonly string $link,
        protected readonly ?string $size = null,
        protected readonly ?string $extension = null
    ) {}

    /**
     * Returns the link to the attachment.
     *
     * @return string URL link to the attachment.
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * Returns the name of the attachment.
     *
     * @return string The name of the attachment.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the size of the attachment.
     *
     * @return string|null The size of the attachment in bytes, or null if not provided.
     */
    public function getSize(): ?string
    {
        return $this->size;
    }

    /**
     * Returns the file extension.
     *
     * @return string|null The file extension of the attachment, or null if not provided.
     */
    public function getExtension(): ?string
    {
        return $this->extension;
    }

    /**
     * Returns a description of the attachment.
     *
     * Combines file extension and size into a single string, separated by a comma.
     * If either value is missing, it is excluded from the description.
     *
     * @return string A description of the attachment (e.g., "pdf, 12345").
     */
    public function getDescription(): string
    {
        $data = [];

        if ($this->extension !== null) {
            $data[] = $this->extension;
        }

        if ($this->size !== null) {
            $data[] = $this->size;
        }

        return implode(', ', $data);
    }
}
