<?php

namespace Lemonade\EmailGenerator\DTO;

class AttachmentData
{
    /**
     * Constructor for AttachmentData.
     *
     * @param string $name The name of the attachment.
     * @param string $link The URL link to the attachment.
     * @param string|null $size The size of the attachment in bytes (optional).
     * @param string|null $extension The file extension (optional).
     */
    public function __construct(
        public string $name,
        public string $link,
        public ?string $size = null,
        public ?string $extension = null
    ) {}
}
