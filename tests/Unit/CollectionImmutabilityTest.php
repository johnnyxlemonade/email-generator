<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Closure;
use Lemonade\EmailGenerator\Component\BulletList;
use Lemonade\EmailGenerator\Component\Table;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Translation;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use stdClass;

/**
 * Protects content collections from external array references and mutable objects
 */
final class CollectionImmutabilityTest extends EmailTestCase
{
    public function testListDetachesReferences(): void
    {
        $item = 'Original';
        $items = [&$item, 'Second'];
        $list = new BulletList(items: $items, ordered: true);
        $item = 'Changed through variable';
        $items[0] = 'Changed through input';
        $items[1] = 'Changed second';
        $items[] = 'Added';
        $copy = $list->items();
        $copy[0] = 'Changed through getter';

        self::assertSame(['Original', 'Second'], $list->items());
        self::assertTrue($list->ordered());
    }

    public function testTableDetachesReferencesAtEveryArrayLevel(): void
    {
        $header = 'Header';
        $cell = 'Cell';
        $row = [&$cell];
        $headers = [&$header];
        $rows = [&$row];
        $table = new Table(headers: $headers, rows: $rows);
        $header = 'Changed header';
        $cell = 'Changed cell';
        $row[] = 'Added column';
        $rows[0] = ['Replaced row'];
        $rows[] = ['Added row'];
        $headers[0] = 'Changed through input';
        $copy = $table->rows();
        $copy[0][0] = 'Changed through getter';
        $copyHeaders = $table->headers();
        $copyHeaders[0] = 'Changed through getter';

        self::assertSame(['Header'], $table->headers());
        self::assertSame([['Cell']], $table->rows());
    }

    public function testTranslationDetachesReferencesWithoutCoercingValues(): void
    {
        $name = 'Alex';
        $count = 2;
        $ratio = 1.5;
        $enabled = false;
        $missing = null;
        $replacements = [
            'name' => &$name,
            'count' => &$count,
            'ratio' => &$ratio,
            'enabled' => &$enabled,
            'missing' => &$missing,
        ];
        $translation = new Translation(key: 'emails.welcome', replacements: $replacements);
        $name = 'Changed';
        $count = 3;
        $ratio = 2.5;
        $enabled = true;
        $missing = 'Present';
        $replacements['name'] = 'Changed through input';
        $replacements['extra'] = 'Added';
        $copy = $translation->replacements();
        $copy['count'] = 4;

        self::assertSame([
            'name' => 'Alex',
            'count' => 2,
            'ratio' => 1.5,
            'enabled' => false,
            'missing' => null,
        ], $translation->replacements());
        self::assertSame('emails.welcome', $translation->key());
    }

    public function testNestedObjectsRemainImmutableAndReusable(): void
    {
        $name = 'Alex';
        $translation = new Translation('emails.welcome', ['name' => &$name]);
        $run = TextRun::strong($translation);
        $parts = [&$run];
        $rich = new RichText(...$parts);
        $items = [&$translation, &$rich];
        $list = new BulletList($items);
        $table = new Table([$rich], [[$translation]]);
        $base = $this->emails->create(locale: 'cs', title: $translation, preheader: $translation)
            ->group($list, $table);
        $before = $base->render();
        $branch = $base->text('Branch');
        $branchBefore = $branch->render();

        foreach ([$translation, $rich, $run] as $value) {
            $reflection = new ReflectionClass($value);
            self::assertTrue($reflection->isFinal());
            self::assertTrue($reflection->isReadOnly());
        }
        self::assertSame($translation, $list->items()[0]);
        self::assertSame($rich, $list->items()[1]);
        self::assertSame($translation, $rich->runs()[0]->content());
        self::assertSame($rich, $table->headers()[0]);
        self::assertSame($translation, $table->rows()[0][0]);

        $name = 'Changed name';
        $translation = new Translation('emails.missing');
        $rich = new RichText('Changed rich text');
        $run = new TextRun('Changed run');
        $items[0] = 'Changed item';
        $parts[0] = new TextRun('Changed part');

        self::assertSame($before, $base->render());
        self::assertSame($branchBefore, $branch->render());
        self::assertStringContainsString('Alex', $branch->render());
        self::assertStringNotContainsString('Changed', $branch->render());
    }

    /**
     * @param Closure(): object $construct
     */
    #[DataProvider('mutableObjectInputs')]
    public function testMutableObjectsAreRejected(Closure $construct): void
    {
        $this->expectException(InvalidInputException::class);
        $construct();
    }

    /**
     * @return iterable<string, array{Closure(): object}>
     */
    public static function mutableObjectInputs(): iterable
    {
        yield 'list item' => [static fn(): object => (new ReflectionClass(BulletList::class))->newInstanceArgs([[new stdClass()]])];
        yield 'table header' => [static fn(): object => (new ReflectionClass(Table::class))->newInstanceArgs([[new stdClass()], []])];
        yield 'table cell' => [static fn(): object => (new ReflectionClass(Table::class))->newInstanceArgs([['Header'], [[new stdClass()]]])];
        yield 'translation replacement' => [static fn(): object => (new ReflectionClass(Translation::class))->newInstanceArgs(['key', ['value' => new stdClass()]])];
    }
}
