<?php

namespace Lemonade\EmailGenerator\Factories;

use Lemonade\EmailGenerator\Models\Attachment;
use Lemonade\EmailGenerator\DTO\AttachmentData;

class AttachmentFactory
{
    /**
     * Vytvoří instanci `Attachment` na základě dat z `AttachmentData`.
     *
     * @param AttachmentData $data Data pro vytvoření přílohy.
     * @return Attachment
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