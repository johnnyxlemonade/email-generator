<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Enum\TextStyle;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * Renders safe inline formatting, links and line breaks for all text-bearing components
 *
 * @internal
 */
final readonly class RichTextRenderer
{
    /**
     * Renders literal or translated text and typed inline runs with line breaks
     */
    public static function render(string|Translation|RichText $value, RenderContext $context): string
    {
        if ($value instanceof RichText) {
            $html = '';
            foreach ($value->runs() as $run) {
                $html .= self::run($run, $context);
            }

            return $html;
        }

        return str_replace(["\r\n", "\r", "\n"], '<br>', $context->text($value));
    }

    /**
     * Applies a run's formatting and optional validated link to escaped content
     */
    private static function run(TextRun $run, RenderContext $context): string
    {
        $content = self::render($run->content(), $context);
        $content = match ($run->style()) {
            TextStyle::Plain => $content,
            TextStyle::Strong => '<strong style="font-weight:bold;">' . $content . '</strong>',
            TextStyle::Emphasis => '<em style="font-style:italic;">' . $content . '</em>',
            TextStyle::StrongEmphasis => '<strong style="font-weight:bold;"><em style="font-style:italic;">' . $content . '</em></strong>',
        };
        if ($run->url() !== null) {
            return '<a href="' . RenderContext::escape($run->url()) . '" style="color:' . $context->config()->accentColor()->hex()
                . ';text-decoration:underline;">' . $content . '</a>';
        }

        return $content;
    }
}
