<?php

namespace Lemonade\EmailGenerator\Factories;

use Lemonade\EmailGenerator\Models\Attachment;
use Lemonade\EmailGenerator\DTO\AttachmentData;

class AttachmentFactory
{
    /**
     * Creates an instance of `Attachment` based on the data from `AttachmentData`.
     *
     * @param AttachmentData $data The data for creating the attachment.
     * @return Attachment A new instance of Attachment.
     */
    public static function createFromDTO(AttachmentData $data): Attachment
    {
        return new Attachment(
            $data->name,
            $data->link,
            $data->size,
            $data->extension
        );
    }
}
