<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Integration;

use Lemonade\EmailGenerator\Component\{BulletList, Group, Image, Spacer, Text};
use Lemonade\EmailGenerator\Document;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\Direction;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\Translation;
use Lemonade\Framework\Container\Container;
use Lemonade\Framework\Core\CliKernelFactory;

/**
 * Preserves complete HTML captured before splitting component renderers
 */
final class HtmlCompatibilityTest extends EmailTestCase
{
    public function testPublicApiMatchesOriginalHtml(): void
    {
        $container = new Container();
        (new CliKernelFactory($container))->create(self::context())->bootstrap();
        $emails = $container->get(EmailGenerator::class);

        foreach ($this->documents($emails) as $name => $document) {
            $path = __DIR__ . '/../Fixtures/html/' . $name . '.html';
            self::assertStringEqualsFile($path, $document->render());
        }
    }

    /**
     * @return array<string, Document>
     */
    private function documents(EmailGenerator $emails): array
    {
        $rich = new RichText('Before <', TextRun::strong('bold &'), TextRun::emphasis('italic'), TextRun::link('link', 'https://example.com/?a=1&b=2'));
        $all = $emails->create(locale: 'cs', title: new Translation('emails.welcome', ['name' => '<Alex>']), preheader: 'Preview &')
            ->heading($rich)->heading('Level two', 2)->heading('Level three', 3)
            ->heading('Level four', 4)->heading('Level five', 5)->heading('Level six', 6)
            ->text("Line one\r\nLine <two>\rLine three\nLine four")
            ->image('https://example.com/image.png?a=1&b=2', '<Alt>', 1000, 500)
            ->image('https://example.com/small.png', 'Small', 50)
            ->button('Go <now>', 'https://example.com/?a=1&b=2')
            ->list([$rich, new Translation('emails.activate')])->list(['One', 'Two'], ordered: true)->list([])
            ->table([$rich, 'Amount'], [[new Translation('emails.activate'), '<10>']])
            ->table([], [['No header']])->table(['No rows'], [])
            ->divider(new Color('abc'), 2)->spacer(25);
        $nested = $emails->create()->text('Before')->add(new Group([
            new Group(),
            new Text('Outer first'),
            new Spacer(13),
            new Group([
                new Group([new Image('https://example.com/image.png', '', 1000, 500)], padding: 7),
                new Text($rich),
                new Group(),
            ], backgroundColor: new Color('abc'), padding: 20, borderColor: new Color('123')),
            new Text('Outer last'),
            new Group(),
        ], padding: 10))->text('After');
        $edges = $emails->create()->group()
            ->group(new Text('Before spacer'), new Spacer(9), new Group())
            ->group(new Text('Before empty list'), new BulletList([]), new Group())
            ->group(new Group())->spacer(7)->group();

        return [
            'all-components' => $all,
            'nested-groups' => $nested,
            'spacing-edges' => $edges,
            'empty' => $emails->create(),
            'rtl' => $emails->create(locale: 'ar', direction: Direction::Rtl)->heading('مرحبا')->text($rich),
            'english' => $emails->create(locale: 'en')->heading(new Translation('emails.welcome', ['name' => 'Alex'])),
        ];
    }
}
