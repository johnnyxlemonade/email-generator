<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use InvalidArgumentException;
use Lemonade\EmailGenerator\Component\{Group, Heading, Text};
use Lemonade\EmailGenerator\Composition\ComponentExpander;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Contract\CompositionInterface;
use Lemonade\EmailGenerator\Enum\Direction;
use Lemonade\EmailGenerator\Exception\CompositionException;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;

/**
 * Verifies document isolation and safe expansion of custom compositions
 */
final class DocumentTest extends EmailTestCase
{
    public function testDocumentsAndReturnedComponentArraysAreIndependent(): void
    {
        $first = $this->emails->create(locale: 'cs', title: 'First')->heading('Only first');
        $second = $this->emails->create(locale: 'en', title: 'Second')->text('Only second');
        $branch = $first->text('Only branch');
        $copy = $first->components();
        $copy[] = new Text('External');

        self::assertNotSame($first, $second);
        self::assertCount(1, $first->components());
        self::assertCount(2, $branch->components());
        $firstHeading = $first->components()[0];
        $secondText = $second->components()[0];
        self::assertInstanceOf(Heading::class, $firstHeading);
        self::assertInstanceOf(Text::class, $secondText);
        self::assertSame('Only first', $firstHeading->content());
        self::assertSame('Only second', $secondText->content());
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
        $document = $this->emails->create()->group(new Text('Before'), $custom)->text('After');
        $custom->text = 'Changed';

        $outer = $document->components()[0];
        self::assertInstanceOf(Group::class, $outer);
        self::assertCount(3, $outer->children());
        self::assertInstanceOf(Heading::class, $outer->children()[1]);
        $inner = $outer->children()[2];
        self::assertInstanceOf(Group::class, $inner);
        $text = $inner->children()[0];
        self::assertInstanceOf(Text::class, $text);
        self::assertSame('Original', $text->content());
    }

    public function testSameContainerMayBeReusedWithoutBeingTreatedAsCycle(): void
    {
        $group = new Group([new Text('Reusable')]);
        self::assertCount(2, ComponentExpander::expand([$group, $group]));
    }

    public function testCompositionFailureDoesNotReturnPartialExpansion(): void
    {
        $failing = new class implements CompositionInterface {
            public function components(): iterable
            {
                yield new Text('Discard');
                throw new RuntimeException('Failure');
            }
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failure');
        ComponentExpander::expand([new Text('Also discard'), $failing]);
    }

    #[DataProvider('invalidCompositions')]
    public function testCompositionLimitsAndCyclesExposeStableErrors(CompositionInterface|Group $component, ErrorCode $code): void
    {
        try {
            ComponentExpander::expand([$component]);
            self::fail('Invalid composition was accepted.');
        } catch (CompositionException $exception) {
            self::assertSame($code, $exception->errorCode());
        }
    }

    /**
     * @return iterable<string, array{CompositionInterface|Group, ErrorCode}>
     */
    public static function invalidCompositions(): iterable
    {
        $cycle = new class implements CompositionInterface {
            public function components(): iterable
            {
                yield $this;
            }
        };
        yield 'cycle' => [$cycle, ErrorCode::CompositionCycle];

        $unbounded = new class implements CompositionInterface {
            public function components(): iterable
            {
                while (true) {
                    yield new Text('x');
                }
            }
        };
        yield 'size' => [$unbounded, ErrorCode::CompositionSizeExceeded];

        $deep = new Group([new Text('Deep')]);
        for ($depth = 0; $depth < 33; ++$depth) {
            $deep = new Group([$deep]);
        }
        yield 'depth' => [$deep, ErrorCode::CompositionDepthExceeded];
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

    public function testMalformedCustomCompositionIsRejected(): void
    {
        $invalid = new class implements CompositionInterface {
            public function components(): iterable
            {
                yield 'invalid'; // @phpstan-ignore generator.valueType
            }
        };

        try {
            ComponentExpander::expand([$invalid]);
            self::fail('A non-component composition entry was accepted.');
        } catch (InvalidInputException $exception) {
            self::assertSame(ErrorCode::ComponentUnsupported, $exception->errorCode());
        }
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
}
