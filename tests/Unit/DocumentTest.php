<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use InvalidArgumentException;
use Lemonade\EmailGenerator\Component\{Columns, Group, Heading, Text};
use Lemonade\EmailGenerator\Composition\ComponentExpander;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Contract\CompositionInterface;
use Lemonade\EmailGenerator\Document;
use Lemonade\EmailGenerator\Enum\Direction;
use Lemonade\EmailGenerator\Exception\CompositionException;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Presentation\HtmlRenderer;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;

/**
 * Verifies document isolation and safe expansion of custom compositions
 */
final class DocumentTest extends EmailTestCase
{
    public function testEveryCreateIsIsolatedAndCloneBranchesIndependently(): void
    {
        $first = $this->emails->create(locale: 'cs', title: 'First')->heading('Only first');
        $second = $this->emails->create(locale: 'en', title: 'Second')->text('Only second');
        $branch = clone $first;
        $branch = $branch->text('Only branch');
        self::assertNotSame($first, $second);
        self::assertStringNotContainsString('Only first', $second->render());
        self::assertStringNotContainsString('Only second', $first->render());
        self::assertStringNotContainsString('Only branch', $first->render());
        self::assertStringContainsString('Only branch', $branch->render());
        self::assertSame($first->render(), $first->render());
        $copy = $first->components();
        $copy[] = new Text('External');
        self::assertCount(1, $first->components());
    }

    public function testNestedCompositionPreservesOrderAndSnapshotsMutableSources(): void
    {
        $custom = new class implements CompositionInterface {
            public string $text = 'Original';

            public function components(): iterable
            {
                yield new Heading('Section');
                yield new Group([new Text($this->text)]);
            }
        };
        $first = $this->emails->create()->group(new Text('Before'), $custom)->text('After');
        $custom->text = 'Changed';
        $second = $this->emails->create()->add($custom);
        $components = $first->components();
        self::assertCount(2, $components);
        self::assertInstanceOf(Group::class, $components[0]);
        $children = $components[0]->children();
        self::assertCount(3, $children);
        self::assertInstanceOf(Text::class, $children[0]);
        self::assertInstanceOf(Heading::class, $children[1]);
        self::assertInstanceOf(Group::class, $children[2]);
        self::assertInstanceOf(Text::class, $children[2]->children()[0]);
        self::assertSame('Original', $children[2]->children()[0]->content());
        self::assertStringNotContainsString('Changed', $first->render());
        self::assertStringContainsString('Changed', $second->render());
        self::assertSame($first->render(), $first->render());
    }

    public function testSameGroupMayBeReusedWithoutBeingTreatedAsACycle(): void
    {
        $group = new Group([new Text('Reusable')]);
        self::assertCount(2, $this->emails->create()->add($group, $group)->components());
    }

    public function testCompositionFailureDoesNotPartiallyAppendContent(): void
    {
        $document = $this->emails->create()->text('Keep');
        $failing = new class implements CompositionInterface {
            public function components(): iterable
            {
                yield new Text('Do not append');
                throw new RuntimeException('Failure');
            }
        };
        try {
            $document->add(new Text('Also do not append'), $failing);
            self::fail('Expected composition error.');
        } catch (RuntimeException $exception) {
            self::assertSame('Failure', $exception->getMessage());
        }
        self::assertCount(1, $document->components());
        self::assertStringNotContainsString('append', $document->render());
    }

    public function testCyclicCompositionFailsClearly(): void
    {
        $cycle = new class implements CompositionInterface {
            public function components(): iterable
            {
                yield $this;
            }
        };
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Cyclic');
        $this->emails->create()->add($cycle);
    }

    public function testUnboundedCompositionIsLimited(): void
    {
        $infinite = new class implements CompositionInterface {
            public function components(): iterable
            {
                while (true) {
                    yield new Text('x');
                }
            }
        };
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('10000');
        $this->emails->create()->add($infinite);
    }

    public function testDeepCompositionIsLimited(): void
    {
        $component = new Group([new Text('Deep')]);
        for ($i = 0; $i < 33; ++$i) {
            $component = new Group([$component]);
        }
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('32 levels');
        $this->emails->create()->add($component);
    }

    public function testExpansionPreservesUnknownLeafIdentityAndOrder(): void
    {
        $first = new Text('Before');
        $unknown = new class implements ComponentInterface {};
        $last = new Text('After');
        $composition = new class ($unknown) implements CompositionInterface {
            public function __construct(private ComponentInterface $child) {}

            public function components(): iterable
            {
                yield $this->child;
            }
        };

        self::assertSame([$first, $unknown, $last], ComponentExpander::expand([$first, $composition, $last]));
    }

    #[DataProvider('unsupportedComponents')]
    public function testUnsupportedComponentsFailOnlyDuringRendering(ComponentInterface $component): void
    {
        $base = $this->emails->create()->text('Keep');
        $before = $base->render();
        $branch = $base->add($component);
        $constructed = new Document(new HtmlRenderer($this->translator), components: [$component]);
        self::assertCount(2, $branch->components());
        self::assertCount(1, $constructed->components());

        foreach ([$branch, $constructed] as $document) {
            try {
                $document->render();
                self::fail('An unsupported component produced HTML.');
            } catch (CompositionException $exception) {
                self::assertSame(ErrorCode::ComponentUnsupported, $exception->errorCode());
            }
        }
        self::assertSame($before, $base->render());
        self::assertStringContainsString('Valid sibling', $base->text('Valid sibling')->render());
    }

    /**
     * @return iterable<string, array{ComponentInterface}>
     */
    public static function unsupportedComponents(): iterable
    {
        $unknown = new class implements ComponentInterface {};
        yield 'leaf' => [$unknown];
        yield 'group' => [new Group([$unknown])];
        yield 'first column' => [new Columns([$unknown], [])];
        yield 'second column' => [new Columns([], [$unknown])];
        yield 'nested containers' => [new Group([new Columns([], [new Group([$unknown])])])];
        yield 'custom composition' => [new class ($unknown) implements CompositionInterface {
            public function __construct(private ComponentInterface $child) {}

            public function components(): iterable
            {
                yield $this->child;
            }
        }];
    }

    public function testMalformedCustomCompositionFailsClearly(): void
    {
        $invalid = new class implements CompositionInterface {
            public function components(): iterable
            {
                yield new Text('Discard');
                // Deliberately simulate an extension violating the content contract.
                yield 'invalid'; // @phpstan-ignore generator.valueType
            }
        };
        $base = $this->emails->create()->text('Keep');
        try {
            $base->add($invalid);
            self::fail('A non-component composition entry was accepted.');
        } catch (InvalidInputException $exception) {
            self::assertSame(ErrorCode::ComponentUnsupported, $exception->errorCode());
        }
        self::assertCount(1, $base->components());
        self::assertStringNotContainsString('Discard', $base->render());
    }

    #[DataProvider('invalidLocales')]
    public function testInvalidLocaleIsRejected(string $locale): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->emails->create(locale: $locale);
    }

    /**
     * @return list<array{string}>
     */
    public static function invalidLocales(): array
    {
        return [[''], ['../cs'], ['cs" onclick="evil'], ["en\n"], ['en-'], [str_repeat('a', 65)]];
    }

    public function testDefaultDirectionIsLtr(): void
    {
        self::assertSame(Direction::Ltr, $this->emails->create()->direction());
    }

    public function testEmptyDocumentAndGroupAreRenderable(): void
    {
        $empty = $this->emails->create();
        self::assertSame($empty->render(), $this->emails->create()->group()->render());
        self::assertStringContainsString('<!DOCTYPE html>', $empty->render());
    }
}
