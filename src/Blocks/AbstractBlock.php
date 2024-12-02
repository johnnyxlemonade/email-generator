<?php

namespace Lemonade\EmailGenerator\Blocks;

use InvalidArgumentException;
use Lemonade\EmailGenerator\Context\ContextData;
use Twig\Environment;
use Psr\Log\LoggerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use ReflectionClass;
use Exception;

abstract class AbstractBlock implements BlockInterface
{

    private string $blocksDir = "@Blocks/";
    private string $extension = ".twig";

    protected string $templateBlock;
    protected ContextData $context;

    /**
     * Konstruktor, kde se automaticky nastaví název šablony podle názvu třídy.
     *
     * @param ContextData $context Kontext dat pro blok.
     * @param string|null $templateBlock Název šablony, pokud se chce přepsat výchozí šablona.
     */
    public function __construct(ContextData $context, ?string $templateBlock = null)
    {
        // Pokud není specifikována šablona, použije se automaticky odvozená šablona podle názvu třídy
        if ($templateBlock === null) {

            $className = (new ReflectionClass($this))->getShortName();
            $this->templateBlock = $this->blocksDir . $className . $this->extension;

        } else {

            $this->templateBlock = $this->blocksDir . $templateBlock . $this->extension;
        }

        // Inicializace kontextu
        $this->context = $context;

        $this->validateContext();
    }

    /**
     * Vrátí kontextová data.
     *
     * @return ContextData Kontextová data.
     */
    public function getContext(): ContextData
    {
        return $this->context;
    }

    /**
     * Validace
     * @return void
     */
    abstract public function validateContext(): void;

    /**
     * Centralizovaná validace, která se provádí na všech klíčích.
     * Pokud nějaký klíč chybí, vyhodí výjimku.
     *
     * @param string[] $keys Pole požadovaných klíčů.
     * @throws InvalidArgumentException Pokud chybí některý požadovaný klíč.
     */
    protected function validateRequiredKeys(array $keys): void
    {

        foreach ($keys as $key) {
            if ($this->context->get($key) === null) {
                throw new InvalidArgumentException("Chybí požadovaný klíč: '$key' v kontextu.");
            }
        }

    }

    /**
     * Vykreslí obsah bloku.
     *
     * @param Environment $twig Twig renderer.
     * @param LoggerInterface $logger Logger.
     * @return string Vykreslený HTML obsah bloku.
     */
    public function renderBlock(Environment $twig, LoggerInterface $logger): string
    {
        try {

            $logger->info("Rendering block '{$this->templateBlock}' with context data:", $this->context->toArray());

            return $twig->render($this->templateBlock, $this->context->toArray());

        } catch (LoaderError $e) {

            $logger->error("Chyba při načítání šablony bloku '{$this->templateBlock}': " . $e->getMessage(), ['exception' => $e]);

        } catch (RuntimeError $e) {

            $logger->error("Runtime chyba při vykreslování šablony bloku '{$this->templateBlock}': " . $e->getMessage(), ['exception' => $e]);

        } catch (SyntaxError $e) {

            $logger->error("Syntaxe chyba ve šabloně bloku '{$this->templateBlock}': " . $e->getMessage(), ['exception' => $e]);

        } catch (Exception $e) {

            // Obecná chyba jako fallback
            $logger->error("Neznámá chyba při vykreslování šablony bloku '{$this->templateBlock}': " . $e->getMessage(), ['exception' => $e]);
        }

        return "";
    }

}
