<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Closure;
use Lemonade\EmailGenerator\Component\{Group, Text};
use Lemonade\EmailGenerator\Composition\ComponentExpander;
use Lemonade\EmailGenerator\Contract\CompositionInterface;
use Lemonade\EmailGenerator\Exception\CompositionException;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Verifies visual-group validation and expansion boundaries
 */
final class GroupTest extends TestCase
{
    public function testExpansionPreservesTheGroupAndSnapshotsItsComposition(): void
    {
        $composition = new class implements CompositionInterface {
            public string $value = 'Original';

            public function components(): iterable
            {
                yield new Text($this->value);
            }
        };
        $expanded = ComponentExpander::expand([new Group([$composition], padding: 8)]);
        $composition->value = 'Changed';

        $group = $expanded[0];
        self::assertInstanceOf(Group::class, $group);
        self::assertSame(8, $group->padding());
        $child = $group->children()[0];
        self::assertInstanceOf(Text::class, $child);
        self::assertSame('Original', $child->content());
    }

    public function testCycleThroughAGroupIsRejected(): void
    {
        $cycle = new class implements CompositionInterface {
            public function components(): iterable
            {
                yield new Group([$this]);
            }
        };

        try {
            ComponentExpander::expand([$cycle]);
            self::fail('A cycle through a group was accepted.');
        } catch (CompositionException $exception) {
            self::assertSame(ErrorCode::CompositionCycle, $exception->errorCode());
        }
    }

    /**
     * @param Closure(): Group $construct
     */
    #[DataProvider('invalidGroups')]
    public function testInvalidGroupValuesAreRejected(Closure $construct, ErrorCode $code): void
    {
        try {
            $construct();
            self::fail('Invalid group was accepted.');
        } catch (InvalidInputException $exception) {
            self::assertSame($code, $exception->errorCode());
        }
    }

    /**
     * @return iterable<string, array{Closure(): Group, ErrorCode}>
     */
    public static function invalidGroups(): iterable
    {
        yield 'map' => [static fn(): Group => new Group(['named' => new Text('Text')]), ErrorCode::GroupChildrenInvalid]; // @phpstan-ignore argument.type
        yield 'foreign child' => [static fn(): Group => new Group(['Text']), ErrorCode::GroupChildrenInvalid]; // @phpstan-ignore argument.type
        yield 'negative padding' => [static fn(): Group => new Group(padding: -1), ErrorCode::ValueOutOfRange];
        yield 'large padding' => [static fn(): Group => new Group(padding: 65), ErrorCode::ValueOutOfRange];
    }
}
