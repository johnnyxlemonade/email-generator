<?php

namespace Lemonade\EmailGenerator\Blocks\Component;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Collection\AttachmentCollection;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Services\ContextService;

/**
 * Class AttachmentList
 * Represents a block component for displaying a list of attachments.
 */
class AttachmentList extends AbstractBlock
{
    /**
     * Constructor for `AttachmentList`.
     *
     * Initializes the block with a collection of attachments.
     *
     * @param ContextService $contextData Context Service.
     * @param AttachmentCollection $collection Collection of attachments.
     */
    public function __construct(protected readonly ContextService $contextService, AttachmentCollection $collection)
    {

        // Initialize context
        $context = $this->contextService->createContext([
            "attachments" => $collection->all()
        ]);

        // Pass context to the parent constructor
        parent::__construct($context);
    }

    /**
     * Validates the context for the block.
     *
     * Ensures that the required data (in this case, "attachments") is present in the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // Ensure `attachments` key is set in the context
        $this->context->validate(["attachments"]);
    }
}
