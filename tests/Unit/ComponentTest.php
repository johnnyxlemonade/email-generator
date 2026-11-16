<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Closure;
use InvalidArgumentException;
use Lemonade\EmailGenerator\Component\{BulletList, Button, Divider, Group, Heading, Image, Spacer, Table, Text};
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\Translation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Verifies component content, validation boundaries and immutable properties
 */
final class ComponentTest extends TestCase
{
    public function testComponentsDescribeContentAndHaveImmutableProperties(): void
    {
        $heading = new Heading('Title', 2);
        $text = new Text('Body');
        $button = new Button('Go', 'https://example.com/?a=1&b=2');
        $image = new Image('https://example.com/banner.png', 'Banner', 200, 100);
        $list = new BulletList(['A', new Translation('emails.activate')], true);
        $table = new Table(['Name'], [['Value']]);
        $divider = new Divider(new Color('#abcdef'), 2);
        $spacer = new Spacer(30);
        $group = new Group([$heading, $text, $button, $image, $list, $table, $divider, $spacer]);
        self::assertSame([$heading, $text, $button, $image, $list, $table, $divider, $spacer], $group->children());
        foreach ([...$group->children(), $group, new Translation('key')] as $value) {
            self::assertTrue((new ReflectionClass($value))->isReadOnly());
        }
        self::assertSame(2, $heading->level());
        self::assertSame('Body', $text->content());
        self::assertSame(100, $image->height());
        self::assertTrue($list->ordered());
        self::assertSame([['Value']], $table->rows());
        self::assertSame(30, $spacer->height());
    }

    #[DataProvider('invalidValues')]
    public function testInvalidContentFailsAtConstruction(Closure $construct): void
    {
        $this->expectException(InvalidArgumentException::class);
        $construct();
    }

    /**
     * @return iterable<string, array{Closure(): mixed}>
     */
    public static function invalidValues(): iterable
    {
        yield 'heading zero' => [static fn() => new Heading('x', 0)];
        yield 'heading seven' => [static fn() => new Heading('x', 7)];
        yield 'image width zero' => [static fn() => new Image('https://example.com/a.png', '', 0)];
        yield 'image height zero' => [static fn() => new Image('https://example.com/a.png', '', 1, 0)];
        yield 'image too wide' => [static fn() => new Image('https://example.com/a.png', '', 2001)];
        yield 'image too tall' => [static fn() => new Image('https://example.com/a.png', '', 1, 2001)];
        yield 'spacer zero' => [static fn() => new Spacer(0)];
        yield 'spacer too tall' => [static fn() => new Spacer(1001)];
        yield 'divider zero' => [static fn() => new Divider(thickness: 0)];
        yield 'divider too thick' => [static fn() => new Divider(thickness: 11)];
        yield 'CSS injection' => [static fn() => new Color('#fff;display:none')];
        yield 'invalid UTF8' => [static fn() => new Text("\xFF")];
        yield 'null byte' => [static fn() => new Text("a\0b")];
        yield 'translation key' => [static fn() => new Translation(' ')];
        yield 'translation replacement' => [static fn() => new Translation('key', ['value' => "\xFF"])];
        yield 'empty table' => [static fn() => new Table([], [])];
        yield 'empty row' => [static fn() => new Table([], [[]])];
        yield 'mismatched row' => [static fn() => new Table(['a', 'b'], [['a']])];
        yield 'ragged table' => [static fn() => new Table([], [['a'], ['b', 'c']])];
    }

    #[DataProvider('unsafeUrls')]
    public function testUnsafeUrlsAreRejectedByButtonsAndImages(string $url): void
    {
        foreach ([Button::class, Image::class] as $class) {
            try {
                if ($class === Button::class) {
                    new Button('link', $url);
                } else {
                    new Image($url, 'image');
                }
                self::fail('Unsafe URL was accepted: ' . $url);
            } catch (InvalidArgumentException $exception) {
                self::assertStringContainsString('URL', $exception->getMessage());
            }
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function unsafeUrls(): iterable
    {
        foreach (['javascript:alert(1)', 'data:image/png;base64,AA', 'file:///etc/passwd', 'ftp://example.com/a',
            '//example.com/a', '/relative', '', 'https://', 'https://user:pass@example.com',
            'https://example.com/a b', "https://example.com/\n", 'https://example.com/%0d%0aX',
            'https://example.com/\\evil', 'https://example.com/"onload="x', 'mailto:user@example.com',
        ] as $url) {
            yield $url => [$url];
        }
    }

    public function testSafeUrlsAndBoundaryValues(): void
    {
        foreach (['http://example.com', 'https://example.com:8443/a%20b?a=1&b=2#part', 'HTTPS://example.com/path'] as $url) {
            self::assertSame($url, (new Button('Go', $url))->url());
            self::assertSame($url, (new Image($url, ''))->url());
        }
        self::assertSame(6, (new Heading('x', 6))->level());
        self::assertSame(1000, (new Spacer(1000))->height());
        self::assertSame([], (new BulletList([]))->items());
        self::assertSame([], (new Group())->children());
        self::assertSame([], (new Table(['Header'], []))->rows());
        self::assertSame([], (new Table([], [['Cell']]))->headers());
    }

    /**
     * @param class-string $type
     * @param list<mixed> $arguments
     */
    #[DataProvider('malformedCollections')]
    public function testMalformedCollectionsHaveExplicitValidation(string $type, array $arguments): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new ReflectionClass($type))->newInstanceArgs($arguments);
    }

    /**
     * @return iterable<string, array{class-string, list<mixed>}>
     */
    public static function malformedCollections(): iterable
    {
        yield 'list map' => [BulletList::class, [['a' => 'b']]];
        yield 'list scalar' => [BulletList::class, [[42]]];
        yield 'header scalar' => [Table::class, [[42], []]];
        yield 'row scalar' => [Table::class, [['a'], ['not a row']]];
        yield 'row map' => [Table::class, [['a'], ['row' => ['value']]]];
        yield 'cell object' => [Table::class, [['a'], [[new Text('value')]]]];
        yield 'replacement object' => [Translation::class, ['key', ['value' => new Text('value')]]];
        yield 'replacement integer key' => [Translation::class, ['key', ['value']]];
    }
}
