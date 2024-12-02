<?php

namespace Lemonade\EmailGenerator\Collection;
use Lemonade\EmailGenerator\Models\Attachment;

class AttachmentCollection extends AbstractCollection
{

    /**
     * Ověří, zda je daný objekt platným typem pro kolekci (v tomto případě instancí třídy Attachment).
     *
     * @param mixed $item Objekt, který se ověřuje.
     * @return bool True, pokud je objekt platný (instancí Attachment); jinak false.
     */
    protected function validateItem($item): bool
    {
        return $item instanceof Attachment;
    }

}