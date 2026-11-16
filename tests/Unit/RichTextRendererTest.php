<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Lemonade\EmailGenerator\Configuration\EmailConfig;
use Lemonade\EmailGenerator\Enum\TextStyle;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\Renderer\RichTextRenderer;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * Verifies shared inline formatting independently of block and document layout
 */
final class RichTextRendererTest extends EmailTestCase
{
    public function testPlainTextEscapingAndLineBreaks(): void
    {
        $context = new RenderContext($this->translator, new EmailConfig(), 'en');
        self::assertSame(
            '&lt;script&gt;&amp;&quot;&apos;<br>A<br>B<br>C',
            RichTextRenderer::render("<script>&\"'\r\nA\rB\nC", $context),
        );
        self::assertSame('', RichTextRenderer::render('', $context));
        self::assertSame('', RichTextRenderer::render(new RichText(), $context));
    }

    public function testFormattingAndLinks(): void
    {
        $context = new RenderContext($this->translator, new EmailConfig(), 'en');
        $content = new RichText(
            'Plain ',
            TextRun::strong('<bold>'),
            TextRun::emphasis('italic'),
            new TextRun("both\nlines", TextStyle::StrongEmphasis),
            TextRun::link('A & B', 'https://example.com/?a=1&b=2'),
        );
        self::assertSame(
            'Plain <strong style="font-weight:bold;">&lt;bold&gt;</strong>'
            . '<em style="font-style:italic;">italic</em>'
            . '<strong style="font-weight:bold;"><em style="font-style:italic;">both<br>lines</em></strong>'
            . '<a href="https://example.com/?a=1&amp;b=2" style="color:#2563eb;text-decoration:underline;">A &amp; B</a>',
            RichTextRenderer::render($content, $context),
        );
    }

    public function testContextsKeepLocaleAndTranslationsIsolated(): void
    {
        $cs = new RenderContext($this->translator, new EmailConfig(), 'cs');
        $en = new RenderContext($this->translator, new EmailConfig(), 'en');
        $text = new RichText(TextRun::strong(new Translation('emails.welcome', ['name' => '<Alex>'])));
        $originalLocale = $this->translator->locale();
        $expectedCs = '<strong style="font-weight:bold;">Vítejte, &lt;Alex&gt;!</strong>';
        self::assertSame($expectedCs, RichTextRenderer::render($text, $cs));
        self::assertSame('<strong style="font-weight:bold;">Welcome, &lt;Alex&gt;!</strong>', RichTextRenderer::render($text, $en));
        self::assertSame($expectedCs, RichTextRenderer::render($text, $cs));
        self::assertSame($originalLocale, $this->translator->locale());
    }
}
