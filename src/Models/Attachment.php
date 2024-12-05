<?php

namespace Lemonade\EmailGenerator\Models;

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
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * Returns the name of the attachment.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the size of the attachment.
     *
     * @return string|null
     */
    public function getSize(): ?string
    {
        return $this->size;
    }

    /**
     * Returns the file extension.
     *
     * @return string|null
     */
    public function getExtension(): ?string
    {
        return $this->extension;
    }

    /**
     * Returns a description of the attachment.
     *
     * @return string
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
