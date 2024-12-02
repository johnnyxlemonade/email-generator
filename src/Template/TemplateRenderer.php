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
     * Konstruktor TemplateRendereru.
     *
     * @param LoggerInterface $logger
     * @param Translator $translator
     */
    public function __construct(private readonly LoggerInterface $logger, private readonly Translator $translator)
    {
        $this->initializeTwig();
    }

    /**
     * Inicializace prostředí Twig.
     */
    private function initializeTwig(): void
    {

        $loader = new FilesystemLoader(self::TEMPLATE_DIRECTORY);

        try {

            $loader->addPath(self::TEMPLATE_DIRECTORY . '/Blocks', 'Blocks');

        } catch (LoaderError $e) {

            $this->logger->error("Chyba při nastavení cesty k šablonám: " . $e->getMessage(), ['exception' => $e]);
        }

        $this->twig = new Environment($loader);

        // Přidání funkcí do Twig
        $this->twig->addFunction(new TwigFunction('translate', [$this->translator, 'translate'])); // prekladac
        $this->twig->addFunction(new TwigFunction('format_number', [new NumberFormatter(), 'format'])); // formatovani cisel
        $this->twig->addExtension(new MinifyExtension());
        $this->twig->addGlobal("alignment", "left");
    }

    /**
     * Vykreslí výchozí šablonu s daty.
     *
     * @param array $data Data předaná šabloně.
     * @return string Vykreslený obsah šablony.
     */
    public function render(array $data = []): string
    {
        try {

            return $this->twig->render($this->template, $data);

        } catch (\Exception $e) {

            $this->logger->error("Chyba při vykreslování šablony '{$this->template}': " . $e->getMessage(), [
                'exception' => $e,
                'template' => $this->template
            ]);

            return "Omlouváme se, došlo k chybě při vykreslování.";
        }
    }

    /**
     * Getter pro Twig Environment.
     *
     * @return Environment
     */
    public function getTwig(): Environment
    {
        return $this->twig;
    }

}
