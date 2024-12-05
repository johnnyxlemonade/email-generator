<?php

namespace Lemonade\EmailGenerator\Blocks\Component;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Collection\AttachmentCollection;
use Lemonade\EmailGenerator\Context\ContextData;

class AttachmentList extends AbstractBlock
{
    /**
     * Constructor for `AttachmentList`.
     *
     * @param AttachmentCollection $collection Collection of attachments.
     */
    public function __construct(AttachmentCollection $collection)
    {
        // Initialize context
        $context = new ContextData();
        $context->set("attachments", $collection->all());

        // Pass context to the parent constructor
        parent::__construct($context);
    }

    /**
     * Validate context for the block.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // Ensure `attachments` key is set in the context
        $this->context->validate(["attachments"]);
    }
}
