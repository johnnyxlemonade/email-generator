<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\Collection\AttachmentCollection;
use Lemonade\EmailGenerator\DTO\AttachmentData;
use Lemonade\EmailGenerator\Models\Attachment;

/**
 * Interface AttachmentCollectionServiceInterface
 * Defines the contract for managing attachment collections and items.
 */
interface AttachmentCollectionServiceInterface
{
    /**
     * Creates a new AttachmentCollection.
     *
     * @return AttachmentCollection A new instance of AttachmentCollection.
     */
    public function createCollection(): AttachmentCollection;

    /**
     * Creates a new Attachment item from AttachmentData and adds it to the collection.
     *
     * @param AttachmentCollection $collection The collection to which the attachment will be added.
     * @param AttachmentData $data Data Transfer Object (DTO) containing attachment information.
     * @return void
     */
    public function createItem(AttachmentCollection $collection, AttachmentData $data): void;

    /**
     * Retrieves all Attachment items from the given collection.
     *
     * @param AttachmentCollection $collection The collection from which to retrieve items.
     * @return Attachment[] An array of Attachment items.
     */
    public function getAllItems(AttachmentCollection $collection): array;
}