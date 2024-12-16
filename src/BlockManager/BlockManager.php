<?php

namespace Lemonade\EmailGenerator\BlockManager;

use Exception;
use Lemonade\EmailGenerator\Blocks\BlockInterface;
use Lemonade\EmailGenerator\Localization\SupportedCurrencies;
use Lemonade\EmailGenerator\Localization\SupportedLanguage;
use Lemonade\EmailGenerator\Template\TemplateRenderer;
use Lemonade\EmailGenerator\Localization\Translator;
use Psr\Log\LoggerInterface;

class BlockManager
{

    /**
     * @var array<BlockInterface> List of blocks to be rendered.
     */
    private array $blocks = [];

    /**
     * @var string Page title for the rendered email.
     */
    private string $pageTitle = "Default Page Title";

    /**
     * @var SupportedCurrencies|null The selected currency for rendering blocks.
     */
    private ?SupportedCurrencies $currency = SupportedCurrencies::CZK;

    /**
     * Constructor for BlockManager.
     *
     * @param TemplateRenderer $templateRenderer Instance of TemplateRenderer for rendering templates.
     * @param LoggerInterface $logger Logger instance for recording logs.
     * @param Translator $translator Translator instance for managing translations.
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
     * Sets the currency for rendering blocks.
     *
     * @param SupportedCurrencies|null $currency The currency to be set.
     */
    public function setCurrency(?SupportedCurrencies $currency): void
    {
        $this->currency = $currency ?? SupportedCurrencies::CZK;
    }

    /**
     * Returns the currently set currency.
     *
     * If no currency is explicitly set, the default currency (CZK) is returned.
     *
     * @return SupportedCurrencies The currently set or default currency.
     */
    public function getCurrency(): SupportedCurrencies
    {
        return $this->currency ?? SupportedCurrencies::CZK;
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
        $currency = $this->getCurrency();
        $this->logger->info("Rendering block with currency: " . $currency->value);

        return $block->renderBlock($this->templateRenderer->getTwig(), $this->logger, $this->getCurrency());
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

    /**
     * Returns the instance of TemplateRenderer.
     *
     * @return TemplateRenderer The template renderer instance.
     */
    public function getTemplateRenderer(): TemplateRenderer
    {
        return $this->templateRenderer;
    }

    /**
     * Returns the instance of Logger.
     *
     * @return LoggerInterface The logger instance.
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Returns the instance of Translator.
     *
     * @return Translator The translator instance.
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
