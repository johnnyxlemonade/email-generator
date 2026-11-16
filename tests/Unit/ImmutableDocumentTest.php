<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Closure;
use Lemonade\EmailGenerator\Component\{Group, Heading, Text};
use Lemonade\EmailGenerator\Contract\CompositionInterface;
use Lemonade\EmailGenerator\Document;
use Lemonade\EmailGenerator\Enum\Direction;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\Translation;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;

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
        $base = $this->emails->create(
            locale: 'cs',
            title: $title,
            preheader: $preheader,
            direction: Direction::Rtl,
        )->heading('Shared');
        $before = $base->render();
        $branch = $append($base);

        self::assertNotSame($base, $branch);
        self::assertCount(1, $base->components());
        self::assertCount(1 + $added, $branch->components());
        self::assertSame($before, $base->render());
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

    public function testBranchesFromSharedBaseRemainIndependent(): void
    {
        $base = $this->emails->create()->heading('Shared');
        $first = $base->text('First branch');
        $second = $base->text('Second branch');
        $extended = $first->button('First action', 'https://example.com');

        self::assertSame($this->emails->create()->heading('Shared')->render(), $base->render());
        self::assertSame($base->text('First branch')->render(), $first->render());
        self::assertSame($base->text('Second branch')->render(), $second->render());
        self::assertStringNotContainsString('First action', $first->render());
        self::assertStringNotContainsString('First branch', $second->render());
        self::assertStringContainsString('First action', $extended->render());
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
        $base = $this->emails->create()->group($composition);
        $before = $base->render();
        $first = $base->text('First');
        $second = $base->text('Second');
        $later = $this->emails->create()->add($composition);

        self::assertSame($before, $base->render());
        self::assertStringContainsString('Snapshot 1', $first->render());
        self::assertStringContainsString('Snapshot 1', $second->render());
        self::assertStringContainsString('Snapshot 2', $later->render());
    }

    public function testNestedContentAndDocumentPropertiesAreImmutable(): void
    {
        $translation = new Translation('emails.welcome', ['name' => 'Alex']);
        $run = TextRun::strong($translation);
        $rich = new RichText($run);
        $text = new Text($rich);
        $color = new Color('fff');
        $group = new Group([new Group([$text], backgroundColor: $color)]);
        $base = $this->emails->create(locale: 'cs')->add($group);
        $before = $base->render();
        $branch = $base->text('Branch');
        $replacements = $translation->replacements();
        $replacements['name'] = 'Changed';
        $runs = $rich->runs();
        $runs[] = new TextRun('Changed');
        $children = $group->children();
        $children[] = new Text('Changed');
        $components = $base->components();
        $components[] = new Text('Changed');

        foreach ([$base, $translation, $run, $rich, $text, $color, $group] as $value) {
            $reflection = new ReflectionClass($value);
            self::assertTrue($reflection->isFinal());
            self::assertTrue($reflection->isReadOnly());
            foreach ($reflection->getProperties() as $property) {
                self::assertTrue($property->isPrivate());
            }
        }
        self::assertSame($before, $base->render());
        self::assertStringNotContainsString('Changed', $branch->render());
        self::assertStringContainsString('Alex', $branch->render());
    }

    public function testRepeatedAppendingPreservesOrderAndPreviousDocuments(): void
    {
        $base = $this->emails->create()->heading('Shared');
        $document = $base;
        for ($index = 0; $index < 100; ++$index) {
            $document = $document->text('Paragraph ' . $index);
        }

        self::assertCount(1, $base->components());
        self::assertCount(101, $document->components());
        foreach (array_slice($document->components(), 1) as $index => $component) {
            self::assertInstanceOf(Text::class, $component);
            self::assertSame('Paragraph ' . $index, $component->content());
        }
    }
}
