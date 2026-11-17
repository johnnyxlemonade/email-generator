<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Closure;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\Spacer;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\Configuration\EmailConfigDefinition;
use Lemonade\EmailGenerator\Configuration\EmailConfigResolver;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\ValueObject\Color;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Verifies stable error codes across public validation boundaries
 */
final class ExceptionTest extends TestCase
{
    #[DataProvider('invalidInputs')]
    public function testErrorsExposeStableTypedCodes(Closure $operation, ErrorCode $expected): void
    {
        try {
            $operation();
            self::fail('Invalid input was accepted.');
        } catch (InvalidInputException $exception) {
            self::assertSame($expected, $exception->errorCode());
            self::assertNotSame('', $exception->getMessage());
        }
    }

    /**
     * @return iterable<string, array{Closure(): mixed, ErrorCode}>
     */
    public static function invalidInputs(): iterable
    {
        yield 'color' => [static fn() => new Color('red'), ErrorCode::ColorInvalid];
        yield 'URL' => [static fn() => new Button('Open', 'javascript:alert(1)'), ErrorCode::UrlInvalid];
        yield 'text' => [static fn() => new Text("\xFF"), ErrorCode::TextInvalid];
        yield 'dimension' => [static fn() => new Spacer(-1), ErrorCode::ValueOutOfRange];
        yield 'width' => [static fn() => (new EmailConfigResolver())->resolve(EmailConfigDefinition::fromArrayData(['width' => 640])), ErrorCode::WidthInvalid];
        yield 'config type' => [static fn() => (new EmailConfigResolver())->resolve(EmailConfigDefinition::fromArrayData(['width' => '600'])), ErrorCode::ConfigurationInvalidType];
        yield 'config key' => [static fn() => (new EmailConfigResolver())->resolve(EmailConfigDefinition::fromArrayData(['unknown' => true])), ErrorCode::ConfigurationUnknownOption];
    }
}
