<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation;

use Lemonade\EmailGenerator\Configuration\EmailConfig;
use Lemonade\EmailGenerator\Document;
use Lemonade\Framework\Localization\TranslatorInterface;

/**
 * Assembles the complete email using an isolated context and shared document layout
 */
final readonly class HtmlRenderer
{
    /**
     * Receives the framework translator and shared immutable configuration
     */
    public function __construct(private TranslatorInterface $translator, private EmailConfig $config = new EmailConfig()) {}

    /**
     * Renders document content through the dispatcher and adds the global layout
     */
    public function render(Document $document): string
    {
        $context = new RenderContext($this->translator, $this->config, $document->locale());
        $renderer = new ComponentRenderer($context);
        $rows = $renderer->renderComponents($document->components());

        return DocumentLayout::render($document, $context, $rows);
    }
}
