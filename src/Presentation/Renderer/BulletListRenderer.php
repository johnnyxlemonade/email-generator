<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\BulletList;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;

/**
 * Renders BulletList content without its outer layout row
 *
 * @implements HtmlComponentRendererInterface<BulletList>
 *
 * @internal
 */
final readonly class BulletListRenderer implements HtmlComponentRendererInterface
{
    /**
     * Renders ordered or unordered items while retaining an empty row for an empty list
     *
     * @param BulletList $component
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        ComponentRenderer $dispatcher,
        int $contentWidth,
    ): RenderedComponent {
        if ($component->items() === []) {
            return new RenderedComponent('');
        }
        $tag = $component->ordered() ? 'ol' : 'ul';
        $html = '<' . $tag . ' style="margin:0;padding:0 24px;">';
        foreach ($component->items() as $item) {
            $html .= '<li style="margin:0;padding:0 0 4px;">' . RichTextRenderer::render($item, $context) . '</li>';
        }

        return new RenderedComponent($html . '</' . $tag . '>', spacingAfter: 20);
    }
}
