<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use InvalidArgumentException;
use Lemonade\EmailGenerator\Configuration\{EmailConfig, EmailConfigDefinition, EmailConfigResolver};
use Lemonade\EmailGenerator\Enum\EmailWidth;
use Lemonade\EmailGenerator\ValueObject\Color;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Verifies configuration defaults, ordered overrides and invalid settings
 */
final class ConfigTest extends TestCase
{
    public function testDefinitionNormalizesColorValues(): void
    {
        self::assertSame(EmailWidth::Standard, (new EmailConfig())->width());
        $config = (new EmailConfigResolver())->resolve(EmailConfigDefinition::fromArrayData(['background_color' => 'ABC']));
        self::assertSame('#aabbcc', $config->backgroundColor()->hex());
    }

    public function testDefaultsAndOrderedOverrides(): void
    {
        $resolver = new EmailConfigResolver();
        self::assertEquals(new EmailConfig(), $resolver->resolve());
        $base = EmailConfigDefinition::create()->width(EmailWidth::Compact)->accentColor(new Color('#ff0000'))->backgroundColor(new Color('#eeeeee'));
        $override = EmailConfigDefinition::create()->width(EmailWidth::Wide)->accentColor(new Color('#00ff00'))
            ->textColor(new Color('#111111'))->contentColor(new Color('#fafafa'))->buttonTextColor(new Color('#000000'));
        $resolved = $resolver->resolve($base, $override);
        self::assertSame(EmailWidth::Wide, $resolved->width());
        self::assertSame('#00ff00', $resolved->accentColor()->hex());
        self::assertSame('#eeeeee', $resolved->backgroundColor()->hex());
        self::assertSame('#111111', $resolved->textColor()->hex());
        self::assertSame('#fafafa', $resolved->contentColor()->hex());
        self::assertSame('#000000', $resolved->buttonTextColor()->hex());
        self::assertSame(EmailWidth::Compact, $resolver->resolve($base)->width());
        self::assertEquals($override, EmailConfigDefinition::fromArrayData($override->toArray()));
    }

    /**
     * @param array<mixed> $values
     */
    #[DataProvider('invalidConfiguration')]
    public function testInvalidConfigurationFailsExplicitly(array $values): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new EmailConfigResolver())->resolve(EmailConfigDefinition::fromArrayData($values));
    }

    /**
     * @return iterable<string, array{array<mixed>}>
     */
    public static function invalidConfiguration(): iterable
    {
        yield 'narrow' => [['width' => 319]];
        yield 'wide' => [['width' => 1001]];
        yield 'string width' => [['width' => '600']];
        yield 'null width' => [['width' => null]];
        yield 'boolean width' => [['width' => true]];
        yield 'float width' => [['width' => 600.0]];
        yield 'unknown key' => [['widht' => 600]];
        yield 'numeric key' => [[0 => 600]];
        yield 'wrong color type' => [['accent_color' => 123]];
        yield 'color CSS injection' => [['accent_color' => '#ffffff;display:none']];
        yield 'color HTML injection' => [['text_color' => '"><script>']];
        yield 'invalid color length' => [['background_color' => '#ffff']];
    }
}
