<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Closure;
use Lemonade\EmailGenerator\Component\AttachmentList;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\ValueObject\AttachmentItem;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Verifies attachment metadata, validation and immutable collection snapshots
 */
final class AttachmentListTest extends TestCase
{
    public function testItemKeepsMetadataAndDerivesItsType(): void
    {
        $item = new AttachmentItem(
            filename: 'Technical-specification.xlsx',
            bytes: 245_000,
            url: 'https://example.com/documents/specification.xlsx',
        );

        self::assertSame('Technical-specification.xlsx', $item->filename());
        self::assertSame('XLSX', $item->type());
        self::assertSame(245_000, $item->bytes());
        self::assertSame('https://example.com/documents/specification.xlsx', $item->url());
    }

    public function testEmptyListRetainsNoItems(): void
    {
        $list = new AttachmentList([]);

        self::assertSame([], $list->items());
    }

    public function testCollectionIsAnImmutableSnapshot(): void
    {
        $original = new AttachmentItem('Contract.pdf');
        $item = $original;
        $items = [&$item];
        $list = new AttachmentList($items);
        $item = new AttachmentItem('Changed.docx');
        $items[0] = $item;
        $items[] = $item;
        $copy = $list->items();
        $copy[0] = $item;

        self::assertSame([$original], $list->items());
    }

    /**
     * @param Closure(): object $construct
     */
    #[DataProvider('invalidValues')]
    public function testInvalidValuesAreRejected(Closure $construct, ErrorCode $errorCode): void
    {
        try {
            $construct();
            self::fail('Invalid attachment input was accepted.');
        } catch (InvalidInputException $exception) {
            self::assertSame($errorCode, $exception->errorCode());
        }
    }

    /**
     * @return iterable<string, array{Closure(): object, ErrorCode}>
     */
    public static function invalidValues(): iterable
    {
        yield 'blank filename' => [static fn(): object => new AttachmentItem(' '), ErrorCode::AttachmentFilenameInvalid];
        yield 'missing extension' => [static fn(): object => new AttachmentItem('Contract'), ErrorCode::AttachmentFilenameInvalid];
        yield 'path instead of filename' => [static fn(): object => new AttachmentItem('../Contract.pdf'), ErrorCode::AttachmentFilenameInvalid];
        yield 'control character' => [static fn(): object => new AttachmentItem("Contract\0.pdf"), ErrorCode::TextInvalid];
        yield 'negative size' => [static fn(): object => new AttachmentItem('Contract.pdf', -1), ErrorCode::ValueOutOfRange];
        yield 'unsafe URL' => [static fn(): object => new AttachmentItem('Contract.pdf', url: 'javascript:alert(1)'), ErrorCode::UrlInvalid];
        yield 'mapped list' => [
            static fn(): object => (new ReflectionClass(AttachmentList::class))->newInstanceArgs([
                ['contract' => new AttachmentItem('Contract.pdf')],
            ]),
            ErrorCode::AttachmentListInvalid,
        ];
        yield 'invalid item' => [
            static fn(): object => (new ReflectionClass(AttachmentList::class))->newInstanceArgs([['Contract.pdf']]),
            ErrorCode::AttachmentListInvalid,
        ];
    }
}
