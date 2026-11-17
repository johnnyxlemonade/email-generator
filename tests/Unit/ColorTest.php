<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use InvalidArgumentException;
use Lemonade\EmailGenerator\ValueObject\Color;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Verifies hexadecimal color validation and normalization
 */
final class ColorTest extends TestCase
{
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
        return [['fff', '#ffffff'], ['#fff', '#ffffff'], ['ffffff', '#ffffff'], ['#ffffff', '#ffffff'], ['#aBcD01', '#abcd01'],
            ['AbC', '#aabbcc'], ['#AbC', '#aabbcc'], ['A1B2C3', '#a1b2c3'], ['000', '#000000']];
    }
}
