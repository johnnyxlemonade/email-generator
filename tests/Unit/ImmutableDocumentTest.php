<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Closure;
use Lemonade\EmailGenerator\Component\{Group, Heading, Text};
use Lemonade\EmailGenerator\Contract\CompositionInterface;
use Lemonade\EmailGenerator\Document;
use Lemonade\EmailGenerator\Enum\Direction;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\ValueObject\Translation;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Verifies independent document branches through the fluent public API
 */
final class ImmutableDocumentTest extends EmailTestCase
{
    /**
     * @param Closure(Document): Document $append
     */
    #[DataProvider('fluentMethods')]
    public function testFluentMethodsReturnNewDocuments(Closure $append, int $added): void
    {
        $title = new Translation('emails.title');
        $preheader = new Translation('emails.preview');
        $base = $this->emails()->create(
            locale: 'cs',
            title: $title,
            preheader: $preheader,
            direction: Direction::Rtl,
        )->heading('Shared');
        $branch = $append($base);

        self::assertNotSame($base, $branch);
        self::assertCount(1, $base->components());
        self::assertCount(1 + $added, $branch->components());
        self::assertSame('cs', $branch->locale());
        self::assertSame($title, $branch->title());
        self::assertSame($preheader, $branch->preheader());
        self::assertSame(Direction::Rtl, $branch->direction());
        self::assertSame($base->components()[0], $branch->components()[0]);
    }

    /**
     * @return iterable<string, array{Closure(Document): Document, int}>
     */
    public static function fluentMethods(): iterable
    {
        yield 'add' => [static fn(Document $document): Document => $document->add(new Text('Added'), new Heading('More')), 2];
        yield 'empty add' => [static fn(Document $document): Document => $document->add(), 0];
        yield 'heading' => [static fn(Document $document): Document => $document->heading('Added'), 1];
        yield 'text' => [static fn(Document $document): Document => $document->text('Added'), 1];
        yield 'image' => [static fn(Document $document): Document => $document->image('https://example.com/image.png', 'Added'), 1];
        yield 'button' => [static fn(Document $document): Document => $document->button('Added', 'https://example.com'), 1];
        yield 'list' => [static fn(Document $document): Document => $document->list(['Added']), 1];
        yield 'table' => [static fn(Document $document): Document => $document->table(['Header'], [['Added']]), 1];
        yield 'divider' => [static fn(Document $document): Document => $document->divider(), 1];
        yield 'spacer' => [static fn(Document $document): Document => $document->spacer(), 1];
        yield 'group' => [static fn(Document $document): Document => $document->group(new Text('Added')), 1];
    }

    public function testNestedCompositionIsSnapshottedBeforeBranching(): void
    {
        $composition = new class implements CompositionInterface {
            private int $calls = 0;

            /**
             * Produces different content on each expansion
             */
            public function components(): iterable
            {
                yield new Group([new Text('Snapshot ' . ++$this->calls)]);
            }
        };
        $base = $this->emails()->create()->group($composition);
        $first = $base->text('First');
        $second = $base->text('Second');
        $later = $this->emails()->create()->add($composition);

        self::assertSame('Snapshot 1', self::snapshotText($base));
        self::assertSame('Snapshot 1', self::snapshotText($first));
        self::assertSame('Snapshot 1', self::snapshotText($second));
        self::assertSame('Snapshot 2', self::snapshotText($later));
    }

    /**
     * Reads the text produced inside the snapshotted group
     */
    private static function snapshotText(Document $document): string
    {
        $component = $document->components()[0];

        while ($component instanceof Group) {
            $component = $component->children()[0];
        }
        self::assertInstanceOf(Text::class, $component);
        $content = $component->content();
        self::assertIsString($content);

        return $content;
    }
}
