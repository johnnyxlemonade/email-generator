<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Closure;
use Lemonade\EmailGenerator\Component\{Group, Steps};
use Lemonade\EmailGenerator\Enum\StepsProgress;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\ValueObject\Translation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Verifies process-state invariants, snapshots and validation
 */
final class StepsTest extends TestCase
{
    public function testStepsAndLabelsAreSnapshottedWithExplicitState(): void
    {
        $first = 'Created';
        $translation = new Translation('emails.activate');
        $input = [&$first, $translation, 'Delivered'];
        $steps = new Steps($input, currentStep: 2, completedLabel: 'Done');
        $first = 'Changed';
        $input[2] = 'Changed';
        $copy = $steps->steps();
        $copy[0] = 'Changed';

        self::assertSame(['Created', $translation, 'Delivered'], $steps->steps());
        self::assertSame(2, $steps->currentStep());
        self::assertSame(StepsProgress::InProgress, $steps->progress());
        self::assertSame('Done', $steps->completedLabel());
    }

    public function testTerminalStatesHaveNoCurrentPosition(): void
    {
        $notStarted = new Steps(['One'], currentStep: null, progress: StepsProgress::NotStarted);
        $completed = new Steps(['One'], currentStep: null, progress: StepsProgress::Completed);

        self::assertNull($notStarted->currentStep());
        self::assertNull($completed->currentStep());
        self::assertSame(StepsProgress::NotStarted, $notStarted->progress());
        self::assertSame(StepsProgress::Completed, $completed->progress());
    }

    /**
     * @param Closure(): Steps $construct
     */
    #[DataProvider('invalidSteps')]
    public function testInvalidStepsAreRejected(Closure $construct, ErrorCode $code): void
    {
        try {
            $construct();
            self::fail('Invalid steps were accepted.');
        } catch (InvalidInputException $exception) {
            self::assertSame($code, $exception->errorCode());
        }
    }

    /**
     * @return iterable<string, array{Closure(): Steps, ErrorCode}>
     */
    public static function invalidSteps(): iterable
    {
        yield 'empty' => [static fn(): Steps => new Steps([]), ErrorCode::StepsInvalid];
        yield 'map' => [static fn(): Steps => new Steps(['first' => 'One']), ErrorCode::StepsInvalid]; // @phpstan-ignore argument.type
        yield 'foreign value' => [static fn(): Steps => new Steps([new Group()]), ErrorCode::StepsInvalid]; // @phpstan-ignore argument.type
        yield 'invalid text' => [static fn(): Steps => new Steps(["Bad\0"]), ErrorCode::TextInvalid];
        yield 'zero current' => [static fn(): Steps => new Steps(['One'], 0), ErrorCode::StepsCurrentInvalid];
        yield 'current too large' => [static fn(): Steps => new Steps(['One'], 2), ErrorCode::StepsCurrentInvalid];
        yield 'missing current' => [static fn(): Steps => new Steps(['One'], null), ErrorCode::StepsCurrentInvalid];
        yield 'current before start' => [static fn(): Steps => new Steps(['One'], 1, StepsProgress::NotStarted), ErrorCode::StepsCurrentInvalid];
        yield 'current after completion' => [static fn(): Steps => new Steps(['One'], 1, StepsProgress::Completed), ErrorCode::StepsCurrentInvalid];
        yield 'bad state label' => [static fn(): Steps => new Steps(['One'], completedLabel: "Bad\0"), ErrorCode::TextInvalid];
    }
}
