<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use InvalidArgumentException;
use Lemonade\EmailGenerator\ValueObject\Color;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Verifies hexadecimal color validation, normalization and immutability
 */
final class ColorTest extends TestCase
{
    public function testColorIsImmutableAndExposesValidatedHex(): void
    {
        $color = new Color('#aBcD01');
        self::assertSame('#abcd01', $color->hex());
        $reflection = new ReflectionClass($color);
        self::assertTrue($reflection->isReadOnly());
        self::assertTrue($reflection->getProperty('hex')->isPrivate());
    }

    #[DataProvider('invalidColors')]
    public function testInvalidColorsCannotEnterTheRenderer(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Color($value);
    }

    /**
     * @return list<array{string}>
     */
    public static function invalidColors(): array
    {
        return [['red'], ['#ffff'], ['##ffffff'], ['#gggggg'], ['#ffffffff'], ['#ffffff;display:none'], ["#ffffff\n"], ['" onload="alert(1)']];
    }

    #[DataProvider('validColors')]
    public function testAcceptedNotationsNormalizeToCssHex(string $value, string $expected): void
    {
        self::assertSame($expected, (new Color($value))->hex());
    }

    /**
     * @return list<array{string, string}>
     */
    public static function validColors(): array
    {
        return [['fff', '#ffffff'], ['#fff', '#ffffff'], ['ffffff', '#ffffff'], ['#ffffff', '#ffffff'],
            ['AbC', '#aabbcc'], ['#AbC', '#aabbcc'], ['A1B2C3', '#a1b2c3'], ['000', '#000000']];
    }
}
