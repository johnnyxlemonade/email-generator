<?php

namespace Lemonade\EmailGenerator\Template;

use Lemonade\EmailGenerator\Blocks\BlockInterface;
use Lemonade\EmailGenerator\Localization\Translator;
use Lemonade\EmailGenerator\Twig\Extensions\MinifyExtension;
use Lemonade\EmailGenerator\Twig\Extensions\NumberFormatter;
use Psr\Log\LoggerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class TemplateRenderer
{
    private const TEMPLATE_DIRECTORY = __DIR__ . '/../Views';
    private string $template = "default.html.twig";
    private Environment $twig;

    /**
     * Constructor for TemplateRenderer.
     *
     * @param LoggerInterface $logger Logger instance.
     * @param Translator $translator Translator instance.
     */
    public function __construct(private readonly LoggerInterface $logger, private readonly Translator $translator)
    {
        $this->initializeTwig();
    }

    /**
     * Initializes the Twig environment.
     */
    private function initializeTwig(): void
    {
        $loader = new FilesystemLoader(self::TEMPLATE_DIRECTORY);

        try {
            $loader->addPath(self::TEMPLATE_DIRECTORY . '/Blocks', 'Blocks');
        } catch (LoaderError $e) {
            $this->logger->error("Error setting template path: " . $e->getMessage(), ['exception' => $e]);
        }

        $this->twig = new Environment($loader);

        // Add functions to Twig
        $this->twig->addFunction(new TwigFunction('translate', [$this->translator, 'translate'])); // Translator
        $this->twig->addFunction(new TwigFunction('format_price', [new NumberFormatter(), 'formatPrice'])); // Number formatting
        $this->twig->addExtension(new MinifyExtension());
        $this->twig->addGlobal("alignment", "left");
    }

    /**
     * Renders the default template with data.
     *
     * @param array $data Data passed to the template.
     * @return string Rendered template content.
     */
    public function render(array $data = []): string
    {
        try {

            return $this->twig->render($this->template, $data);

        } catch (\Exception $e) {
            $this->logger->error("Error rendering template '{$this->template}': " . $e->getMessage(), [
                'exception' => $e,
                'template' => $this->template
            ]);

            return "We apologize, an error occurred during rendering.";
        }
    }

    /**
     * Getter for Twig Environment.
     *
     * @return Environment
     */
    public function getTwig(): Environment
    {
        return $this->twig;
    }
}
