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
     * Constructor, which automatically sets the template name based on the class name.
     *
     * @param ContextData $context Context data for the block.
     * @param string|null $templateBlock Template name if you want to override the default template.
     */
    public function __construct(ContextData $context, ?string $templateBlock = null)
    {
        // If no template is specified, use the automatically derived template based on the class name
        if ($templateBlock === null) {
            $className = (new ReflectionClass($this))->getShortName();
            $this->templateBlock = $this->blocksDir . $className . $this->extension;
        } else {
            $this->templateBlock = $this->blocksDir . $templateBlock . $this->extension;
        }

        // Initialize context
        $this->context = $context;

        $this->validateContext();
    }

    /**
     * Returns the context data.
     *
     * @return ContextData Context data.
     */
    public function getContext(): ContextData
    {
        return $this->context;
    }

    /**
     * Validation of context data.
     *
     * @return void
     */
    abstract public function validateContext(): void;

    /**
     * Centralized validation that is performed on all keys.
     * If a key is missing, an exception is thrown.
     *
     * @param string[] $keys Array of required keys.
     * @throws InvalidArgumentException If a required key is missing.
     */
    protected function validateRequiredKeys(array $keys): void
    {
        foreach ($keys as $key) {
            if ($this->context->get($key) === null) {
                throw new InvalidArgumentException("Missing required key: '$key' in context.");
            }
        }
    }

    /**
     * Renders the content of the block.
     *
     * @param Environment $twig Twig renderer.
     * @param LoggerInterface $logger Logger.
     * @return string Rendered HTML content of the block.
     */
    public function renderBlock(Environment $twig, LoggerInterface $logger): string
    {
        try {
            $logger->info("Rendering block '{$this->templateBlock}' with context data:", $this->context->toArray());

            return $twig->render($this->templateBlock, $this->context->toArray());
        } catch (LoaderError $e) {
            $logger->error("Error loading block template '{$this->templateBlock}': " . $e->getMessage(), ['exception' => $e]);
        } catch (RuntimeError $e) {
            $logger->error("Runtime error while rendering block template '{$this->templateBlock}': " . $e->getMessage(), ['exception' => $e]);
        } catch (SyntaxError $e) {
            $logger->error("Syntax error in block template '{$this->templateBlock}': " . $e->getMessage(), ['exception' => $e]);
        } catch (Exception $e) {
            // General error as fallback
            $logger->error("Unknown error while rendering block template '{$this->templateBlock}': " . $e->getMessage(), ['exception' => $e]);
        }

        return "";
    }
}
