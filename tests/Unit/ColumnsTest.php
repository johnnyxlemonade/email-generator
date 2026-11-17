<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Closure;
use Lemonade\EmailGenerator\Component\{Columns, Text};
use Lemonade\EmailGenerator\Composition\ComponentExpander;
use Lemonade\EmailGenerator\Contract\CompositionInterface;
use Lemonade\EmailGenerator\Exception\CompositionException;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Verifies column snapshots, expansion and constructor validation
 */
final class ColumnsTest extends TestCase
{
    public function testColumnsDetachInputAndGetterArrays(): void
    {
        $first = new Text('First');
        $second = new Text('Second');
        $firstInput = [&$first];
        $secondInput = [&$second];
        $columns = new Columns($firstInput, $secondInput, gap: 23);
        $originalFirst = $first;
        $originalSecond = $second;
        $first = new Text('Changed');
        $secondInput[0] = new Text('Changed');
        $copy = $columns->first();
        $copy[0] = new Text('Changed');

        self::assertSame([$originalFirst], $columns->first());
        self::assertSame([$originalSecond], $columns->second());
        self::assertSame(23, $columns->gap());
    }

    public function testExpansionSnapshotsCompositionsInBothColumns(): void
    {
        $composition = new class implements CompositionInterface {
            public string $value = 'Original';

            public function components(): iterable
            {
                yield new Text($this->value);
            }
        };
        $expanded = ComponentExpander::expand([new Columns([$composition], [$composition])]);
        $composition->value = 'Changed';
        $columns = $expanded[0];
        self::assertInstanceOf(Columns::class, $columns);

        $first = $columns->first()[0];
        $second = $columns->second()[0];
        self::assertInstanceOf(Text::class, $first);
        self::assertInstanceOf(Text::class, $second);
        self::assertSame('Original', $first->content());
        self::assertSame('Original', $second->content());
    }

    public function testCycleThroughColumnsIsRejected(): void
    {
        $cycle = new class implements CompositionInterface {
            public function components(): iterable
            {
                yield new Columns([$this], []);
            }
        };

        try {
            ComponentExpander::expand([$cycle]);
            self::fail('A cycle through columns was accepted.');
        } catch (CompositionException $exception) {
            self::assertSame(ErrorCode::CompositionCycle, $exception->errorCode());
        }
    }

    /**
     * @param Closure(): Columns $construct
     */
    #[DataProvider('invalidColumns')]
    public function testInvalidColumnsAreRejected(Closure $construct, ErrorCode $code): void
    {
        try {
            $construct();
            self::fail('Invalid columns were accepted.');
        } catch (InvalidInputException $exception) {
            self::assertSame($code, $exception->errorCode());
        }
    }

    /**
     * @return iterable<string, array{Closure(): Columns, ErrorCode}>
     */
    public static function invalidColumns(): iterable
    {
        yield 'first map' => [static fn(): Columns => new Columns(['named' => new Text('Text')], []), ErrorCode::ColumnsChildrenInvalid]; // @phpstan-ignore argument.type
        yield 'second map' => [static fn(): Columns => new Columns([], [1 => new Text('Text')]), ErrorCode::ColumnsChildrenInvalid]; // @phpstan-ignore argument.type
        yield 'first foreign child' => [static fn(): Columns => new Columns(['Text'], []), ErrorCode::ColumnsChildrenInvalid]; // @phpstan-ignore argument.type
        yield 'second foreign child' => [static fn(): Columns => new Columns([], ['Text']), ErrorCode::ColumnsChildrenInvalid]; // @phpstan-ignore argument.type
        yield 'negative gap' => [static fn(): Columns => new Columns([], [], -1), ErrorCode::ValueOutOfRange];
        yield 'large gap' => [static fn(): Columns => new Columns([], [], 65), ErrorCode::ValueOutOfRange];
    }
}
