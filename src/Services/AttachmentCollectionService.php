<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\Collection\AttachmentCollection;
use Lemonade\EmailGenerator\DTO\AttachmentData;
use Lemonade\EmailGenerator\Factories\AttachmentFactory;
use Lemonade\EmailGenerator\Models\Attachment;

/**
 * Class AttachmentCollectionService
 * Provides functionalities for managing collections of attachments.
 */
class AttachmentCollectionService extends AbstractCollectionService implements AttachmentCollectionServiceInterface
{
    /**
     * @var AttachmentFactory Factory for creating Attachment instances.
     */
    private AttachmentFactory $attachmentFactory;

    /**
     * Constructor for AttachmentCollectionService.
     * Initializes the service with an AttachmentFactory.
     *
     * @param AttachmentFactory $attachmentFactory Factory for creating Attachment instances.
     */
    public function __construct(AttachmentFactory $attachmentFactory)
    {
        $this->attachmentFactory = $attachmentFactory;
    }

    /**
     * Creates a new AttachmentCollection.
     *
     * @return AttachmentCollection A new instance of AttachmentCollection.
     */
    public function createCollection(): AttachmentCollection
    {
        return new AttachmentCollection();
    }

    /**
     * Creates a new Attachment item from AttachmentData and adds it to the collection.
     *
     * @param AttachmentCollection $collection The collection to which the attachment will be added.
     * @param AttachmentData $data Data Transfer Object (DTO) containing attachment information.
     */
    public function createItem(AttachmentCollection $collection, AttachmentData $data): void
    {
        // Use the factory to create an attachment from the DTO data
        $attachment = $this->attachmentFactory->createFromDTO($data);

        // Add the attachment to the collection
        $collection->add($attachment);
    }
}
