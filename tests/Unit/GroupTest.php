<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\Image;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\Contract\CompositionInterface;
use Lemonade\EmailGenerator\Exception\CompositionException;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\ValueObject\Color;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use RuntimeException;

/**
 * Verifies visual group boundaries, nested widths and composition failures
 */
final class GroupTest extends EmailTestCase
{
    public function testVisualGroupSurvivesExpansionWithItsOptionsAndOwnTable(): void
    {
        $group = new Group([new Heading('Inside'), new Text('Body')], new Color('abc'), 24, new Color('123'));
        $document = $this->emails->create()->text('Before')->add($group)->text('After');
        $snapshot = $document->components()[1];
        self::assertInstanceOf(Group::class, $snapshot);
        self::assertSame($group->backgroundColor(), $snapshot->backgroundColor());
        self::assertSame($group->borderColor(), $snapshot->borderColor());
        self::assertSame(24, $snapshot->padding());
        self::assertCount(2, $snapshot->children());
        $xpath = $this->xpath($document->render());
        self::assertSame(1.0, $xpath->evaluate('count(//table[@class="email-group"])'));
        self::assertSame('Inside', $xpath->evaluate('string(//table[@class="email-group"]//h1)'));
        self::assertSame('Body', $xpath->evaluate('string(//table[@class="email-group"]//p)'));
        self::assertSame('#aabbcc', $xpath->evaluate('string(//table[@class="email-group"]/tr/td/@bgcolor)'));
        self::assertStringContainsString('padding:24px;', (string) $xpath->evaluate('string(//table[@class="email-group"]/tr/td/@style)'));
        self::assertStringContainsString('border:1px solid #112233;', $document->render());
        self::assertSame(2.0, $xpath->evaluate('count(//p[not(ancestor::table[@class="email-group"])])'));
    }

    public function testLogicalCompositionOnlyAddsAContainerWhenItReturnsAGroup(): void
    {
        $plain = new class implements CompositionInterface {
            public function components(): iterable
            {
                yield new Heading('Title');
                yield new Text('Body');
            }
        };
        $visual = new class implements CompositionInterface {
            public function components(): iterable
            {
                yield new Group([new Heading('Title'), new Text('Body')]);
            }
        };
        self::assertStringNotContainsString('email-group', $this->emails->create()->add($plain)->render());
        self::assertStringContainsString('email-group', $this->emails->create()->add($visual)->render());
    }

    public function testNestedGroupsRetainBoundariesAndConstrainImagesToInnerWidth(): void
    {
        $document = $this->emails->create()->add(new Group([
            new Text('Outer'),
            new Group([new Image('https://example.com/image.png', '', 1000, 500)], padding: 20, borderColor: new Color('ccc')),
        ], padding: 10, backgroundColor: new Color('fff')));
        $xpath = $this->xpath($document->render());
        self::assertSame(2.0, $xpath->evaluate('count(//table[@class="email-group"])'));
        // 600 - document padding 64 - outer padding 20 - inner padding 40 - border 2.
        self::assertSame('474', $xpath->evaluate('string(//img/@width)'));
        self::assertSame('237', $xpath->evaluate('string(//img/@height)'));
        self::assertSame(1.0, $xpath->evaluate('count(//table[@class="email-group"]//table[@class="email-group"])'));
    }

    public function testLastChildDoesNotAddSpacingBeyondGroupPadding(): void
    {
        $xpath = $this->xpath($this->emails->create()->group(new Text('First'), new Text('Last'), new Group())->render());
        self::assertStringContainsString('padding:0 0 16px;', (string) $xpath->evaluate('string(//p[text()="First"]/../@style)'));
        self::assertStringContainsString('padding:0 0 0px;', (string) $xpath->evaluate('string(//p[text()="Last"]/../@style)'));
    }

    public function testFluentGroupMatchesDirectGroup(): void
    {
        $components = [new Heading('Title'), new Text('Body')];
        self::assertSame($this->emails->create()->group(...$components)->render(), $this->emails->create()->add(new Group($components))->render());
    }

    public function testACompositionCycleThroughAGroupIsRejectedAtomically(): void
    {
        $cyclic = new class implements CompositionInterface {
            public function components(): iterable
            {
                yield new Group([$this]);
            }
        };
        $document = $this->emails->create()->text('Keep');
        try {
            $document->add(new Group([new Text('Discard'), $cyclic]));
            self::fail('A cycle through a group was accepted.');
        } catch (CompositionException $exception) {
            self::assertSame(ErrorCode::CompositionCycle, $exception->errorCode());
        }
        self::assertCount(1, $document->components());
    }

    public function testFailingCompositionInsideGroupLeavesDocumentUnchanged(): void
    {
        $failing = new class implements CompositionInterface {
            public function components(): iterable
            {
                yield new Text('Discard');
                throw new RuntimeException('Composition failed.');
            }
        };
        $document = $this->emails->create()->text('Keep');
        try {
            $document->add(new Group([$failing]));
            self::fail('Expected an exception.');
        } catch (RuntimeException $exception) {
            self::assertSame('Composition failed.', $exception->getMessage());
        }
        self::assertCount(1, $document->components());
    }

    public function testGroupLayoutRejectsExhaustedWidthWithoutAffectingOtherDocuments(): void
    {
        $group = new Group([new Text('Too deep')], padding: 64);
        for ($i = 0; $i < 4; ++$i) {
            $group = new Group([$group], padding: 64);
        }
        try {
            $this->emails->create()->add($group)->render();
            self::fail('Exhausted width was accepted.');
        } catch (InvalidInputException $exception) {
            self::assertSame(ErrorCode::GroupWidthInvalid, $exception->errorCode());
        }
        self::assertStringContainsString('Independent', $this->emails->create()->group(new Text('Independent'))->render());
    }

    /**
     * @param list<mixed> $arguments
     */
    #[DataProvider('invalidGroups')]
    public function testInvalidGroupValuesAreRejected(array $arguments, ErrorCode $code): void
    {
        try {
            (new ReflectionClass(Group::class))->newInstanceArgs($arguments);
            self::fail('Invalid group was accepted.');
        } catch (InvalidInputException $exception) {
            self::assertSame($code, $exception->errorCode());
        }
    }

    /**
     * @return list<array{list<mixed>, ErrorCode}>
     */
    public static function invalidGroups(): array
    {
        return [
            [[['named' => new Text('Text')]], ErrorCode::GroupChildrenInvalid],
            [[['Text']], ErrorCode::GroupChildrenInvalid],
            [[[], null, -1], ErrorCode::ValueOutOfRange],
            [[[], null, 65], ErrorCode::ValueOutOfRange],
        ];
    }
}
