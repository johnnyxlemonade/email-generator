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
    /** @var BlockInterface[] */
    private array $blocks = [];

    private string $pageTitle = "Default Page Title";

    /**
     * @param TemplateRenderer $templateRenderer
     * @param LoggerInterface $logger
     * @param Translator $translator
     */
    public function __construct(
        private readonly TemplateRenderer $templateRenderer,
        private readonly LoggerInterface $logger,
        private readonly Translator $translator
    ) {}

    /**
     * Přidá blok do fronty pro vykreslování.
     *
     * @param BlockInterface $block Blok, který se má přidat.
     */
    public function addBlock(BlockInterface $block): void
    {
        $this->blocks[] = $block;
    }

    /**
     * @return BlockInterface[]
     */
    function getBlocks(): array
    {
        return $this->blocks;
    }

    /**
     * Nastaví titulek stránky.
     *
     * @param string $title Titulek stránky.
     */
    public function setPageTitle(string $title): void
    {
        $this->pageTitle = $title;
    }

    /**
     * Vrací titulek stránky.
     *
     * @return string Titulek stránky.
     */
    public function getPageTitle(): string
    {
        return $this->pageTitle;
    }

    /**
     * Nastaví nový jazyk pro překlad.
     *
     * @param string $language Jazykový kód.
     */
    public function setLanguage(string $language): void
    {
        $supportedLanguage = SupportedLanguage::tryFrom($language);

        if ($supportedLanguage !== null) {

            $this->translator->setLanguage($supportedLanguage);

        } else {

            $this->logger->warning("Nepodporovaný jazyk: $language. Ponecháváme výchozí jazyk.");
        }
    }

    /**
     * Nastaveni zarovnani pro renderovani BlockInterface prvku
     *
     * @return void
     */
    public function setBlockRenderCenter(): void
    {

        $this->templateRenderer->getTwig()->addGlobal("alignment", "center");
    }

    /**
     * Vrátí kompletní HTML obsah včetně všech bloků.
     *
     * @return string Vykreslený obsah HTML.
     */
    public function getHtml(): string
    {

        // Vykreslit všechny bloky
        $renderedBlocks = array_map(function (BlockInterface $block) {
            return $block->renderBlock($this->templateRenderer->getTwig(), $this->logger);
        }, $this->blocks);

        // Vykreslit hlavní šablonu
        try {

            return $this->templateRenderer->render([
                "pageTitle" => $this->getPageTitle(),
                "blocks"    => $renderedBlocks,
                "language"  => $this->translator->getCurrentLanguage(),
            ]);

        } catch (Exception $e) {

            $this->logger->error("Chyba při vykreslování hlavní šablony: " . $e->getMessage(), ['exception' => $e]);
            return 'Omlouváme se, došlo k chybě při vykreslování celé stránky.';
        }

    }


}
