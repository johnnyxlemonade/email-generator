<?php

namespace Lemonade\EmailGenerator\Blocks\Component;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Context\ContextData;

class ComponentLostPassword extends AbstractBlock
{
    /**
     * Constructor for `ComponentLostPassword`.
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
     * Validate the required keys in the context.
     *
     * @return void
     */
    public function validateContext(): void
    {
        // Validate that "webName" and "anchorLink" keys are present in the context
        $this->validateRequiredKeys(["webName", "anchorLink"]);
    }
}
