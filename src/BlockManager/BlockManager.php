<?php

namespace Lemonade\EmailGenerator\BlockManager;

use Exception;
use Lemonade\EmailGenerator\Blocks\BlockInterface;
use Lemonade\EmailGenerator\Localization\SupportedLanguage;
use Lemonade\EmailGenerator\Template\TemplateRenderer;
use Lemonade\EmailGenerator\Localization\Translator;
use Psr\Log\LoggerInterface;

class BlockManager
{

    /**
     * @var array BlockInterface[]
     */
    private array $blocks = [];

    /**
     * @var string Page title
     */
    private string $pageTitle = "Default Page Title";

    /**
     * Constructor for BlockManager.
     *
     * @param TemplateRenderer $templateRenderer Template renderer instance.
     * @param LoggerInterface $logger Logger instance.
     * @param Translator $translator Translator instance.
     */
    public function __construct(
        private readonly TemplateRenderer $templateRenderer,
        private readonly LoggerInterface $logger,
        private readonly Translator $translator
    ) {}

    /**
     * Adds a block to the queue for rendering.
     *
     * @param BlockInterface $block Block to be added.
     */
    public function addBlock(BlockInterface $block): void
    {
        $this->blocks[] = $block;
    }

    /**
     * Returns the list of blocks.
     *
     * @return BlockInterface[] Array of blocks.
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    /**
     * Sets the page title.
     *
     * @param string $title Page title.
     */
    public function setPageTitle(string $title): void
    {
        $this->pageTitle = $title;
    }

    /**
     * Returns the page title.
     *
     * @return string Page title.
     */
    public function getPageTitle(): string
    {
        return $this->pageTitle;
    }

    /**
     * Sets alignment for rendering BlockInterface elements.
     *
     * @return void
     */
    public function setBlockRenderCenter(): void
    {
        $this->templateRenderer->getTwig()->addGlobal("alignment", "center");
    }


    /**
     * Renders a block using the provided template renderer and logger.
     *
     * @param BlockInterface $block The block to be rendered.
     * @return string The rendered block as a string.
     */
    private function renderBlockCallback(BlockInterface $block): string
    {
        return $block->renderBlock($this->templateRenderer->getTwig(), $this->logger);
    }

    /**
     * Returns the complete HTML content including all blocks.
     *
     * @return string Rendered HTML content.
     */
    public function getHtml(): string
    {
        // Render all blocks
        $renderedBlocks = array_map([$this, 'renderBlockCallback'], $this->blocks);

        // Render the main template
        try {

            return $this->templateRenderer->render([
                "title" => $this->getPageTitle(),
                "blocks"    => $renderedBlocks,
                "language"  => $this->translator->getCurrentLanguage()
            ]);

        } catch (Exception $e) {

            $this->logger->error("Error rendering main template: " . $e->getMessage(), ['exception' => $e]);

            return 'We apologize, an error occurred while rendering the entire page.';
        }
    }
}
