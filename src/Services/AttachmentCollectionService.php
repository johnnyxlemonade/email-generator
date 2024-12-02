<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\Collection\AttachmentCollection;
use Lemonade\EmailGenerator\DTO\AttachmentData;
use Lemonade\EmailGenerator\Factories\AttachmentFactory;
use Lemonade\EmailGenerator\Models\Attachment;

class AttachmentCollectionService
{
    private AttachmentFactory $attachmentFactory;

    /**
     * Konstruktor pro AttachmentCollectionService.
     *
     * @param AttachmentFactory $attachmentFactory
     */
    public function __construct(AttachmentFactory $attachmentFactory)
    {
        $this->attachmentFactory = $attachmentFactory;
    }

    /**
     * Přidává přílohu do kolekce na základě `AttachmentData`.
     *
     * @param AttachmentCollection $collection Kolekce příloh, kam se má přidat příloha.
     * @param AttachmentData $data Data potřebná pro vytvoření přílohy.
     * @return void
     */
    public function addAttachmentToCollection(AttachmentCollection $collection, AttachmentData $data): void
    {
        // Vytvoření přílohy pomocí továrny
        $attachment = $this->attachmentFactory->createFromDTO($data);

        // Přidání přílohy do kolekce
        $collection->add($attachment);
    }

    /**
     * Vrací novou instanci `AttachmentCollection`.
     *
     * @return AttachmentCollection
     */
    public function createAttachmentCollection(): AttachmentCollection
    {
        return new AttachmentCollection();
    }
}
