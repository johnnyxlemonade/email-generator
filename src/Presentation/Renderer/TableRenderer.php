<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\Table;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Exception\CompositionException;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\DataTableRenderer;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;

/**
 * Renders Table content without its outer layout row
 *
 * @internal
 */
final readonly class TableRenderer implements HtmlComponentRendererInterface
{
    /**
     * Delegates semantic table markup to the shared data-table renderer
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        int $contentWidth,
        ComponentRenderer $dispatcher,
    ): RenderedComponent {
        if (!$component instanceof Table) {
            throw new CompositionException(ErrorCode::ComponentUnsupported, 'TableRenderer requires Table.');
        }
        return new RenderedComponent(DataTableRenderer::render($component, $context));
    }
}
