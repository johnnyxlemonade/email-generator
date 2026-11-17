<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Closure;
use Lemonade\EmailGenerator\Component\{Button, ButtonGroup, Group};
use Lemonade\EmailGenerator\Composition\ComponentExpander;
use Lemonade\EmailGenerator\Enum\ButtonGroupAlignment;
use Lemonade\EmailGenerator\Exception\CompositionException;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Verifies action-group snapshots, limits and constructor validation
 */
final class ButtonGroupTest extends TestCase
{
    public function testButtonsAreSnapshottedWithGroupOptions(): void
    {
        $first = new Button('Primary', 'https://example.com/primary');
        $second = new Button('Secondary', 'https://example.com/secondary');
        $input = [&$first, &$second];
        $group = new ButtonGroup($input, gap: 23, alignment: ButtonGroupAlignment::Center);
        $first = new Button('Changed', 'https://example.com/changed');
        $input[] = $first;
        $copy = $group->buttons();
        $copy[0] = $first;

        self::assertSame(2, count($group->buttons()));
        self::assertSame('Primary', $group->buttons()[0]->label());
        self::assertSame(23, $group->gap());
        self::assertSame(ButtonGroupAlignment::Center, $group->alignment());
    }

    public function testButtonsParticipateInTheSharedExpansionLimit(): void
    {
        $button = new Button('Action', 'https://example.com');
        try {
            ComponentExpander::expand([new ButtonGroup(array_fill(0, 10000, $button))]);
            self::fail('Button group children bypassed the shared expansion limit.');
        } catch (CompositionException $exception) {
            self::assertSame(ErrorCode::CompositionSizeExceeded, $exception->errorCode());
        }
    }

    /**
     * @param Closure(): ButtonGroup $construct
     */
    #[DataProvider('invalidGroups')]
    public function testInvalidGroupsAreRejected(Closure $construct, ErrorCode $code): void
    {
        try {
            $construct();
            self::fail('Invalid button group was accepted.');
        } catch (InvalidInputException $exception) {
            self::assertSame($code, $exception->errorCode());
        }
    }

    /**
     * @return iterable<string, array{Closure(): ButtonGroup, ErrorCode}>
     */
    public static function invalidGroups(): iterable
    {
        $button = new Button('Action', 'https://example.com');
        yield 'empty' => [static fn(): ButtonGroup => new ButtonGroup([]), ErrorCode::ButtonGroupButtonsInvalid];
        yield 'single' => [static fn(): ButtonGroup => new ButtonGroup([$button]), ErrorCode::ButtonGroupButtonsInvalid];
        yield 'map' => [static fn(): ButtonGroup => new ButtonGroup(['first' => $button, 'second' => $button]), ErrorCode::ButtonGroupButtonsInvalid]; // @phpstan-ignore argument.type
        yield 'foreign component' => [static fn(): ButtonGroup => new ButtonGroup([$button, new Group()]), ErrorCode::ButtonGroupButtonsInvalid];
        yield 'negative gap' => [static fn(): ButtonGroup => new ButtonGroup([$button, $button], -1), ErrorCode::ValueOutOfRange];
        yield 'large gap' => [static fn(): ButtonGroup => new ButtonGroup([$button, $button], 65), ErrorCode::ValueOutOfRange];
    }
}
