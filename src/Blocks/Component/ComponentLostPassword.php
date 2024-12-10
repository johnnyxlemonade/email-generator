<?php

namespace Lemonade\EmailGenerator\Blocks\Component;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;

/**
 * Class ComponentLostPassword
 * Represents a block component for displaying a lost password functionality.
 */
class ComponentLostPassword extends AbstractBlock
{
    /**
     * Constructor for `ComponentLostPassword`.
     *
     * Initializes the block with the website name and anchor link for password reset.
     *
     * @param string $webName The name of the website.
     * @param string $anchorLink The anchor link for password reset.
     */
    public function __construct(string $webName, string $anchorLink)
    {
        // Initialize context
        $context = new ContextData();
        $context->set("webName", $webName);
        $context->set("anchorLink", $anchorLink);

        // Pass context to the parent constructor
        parent::__construct($context);
    }

    /**
     * Validates the required keys in the context.
     *
     * Ensures that "webName" and "anchorLink" keys are present in the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // Validate that "webName" and "anchorLink" keys are present in the context
        $this->validateRequiredKeys(["webName", "anchorLink"]);
    }
}
