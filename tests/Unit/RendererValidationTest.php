<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Unit;

use Lemonade\EmailGenerator\Component\{Columns, Group, Steps, Text};
use Lemonade\EmailGenerator\Configuration\EmailConfig;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Exception\CompositionException;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Verifies dispatcher and available-width failures without asserting markup
 */
final class RendererValidationTest extends EmailTestCase
{
    public function testUnsupportedLeafIsRejectedByTheDispatcher(): void
    {
        $renderer = new ComponentRenderer(new RenderContext($this->translator(), new EmailConfig(), 'en'));

        try {
            $renderer->render(new class implements ComponentInterface {});
            self::fail('Unsupported component was rendered.');
        } catch (CompositionException $exception) {
            self::assertSame(ErrorCode::ComponentUnsupported, $exception->errorCode());
        }
    }

    #[DataProvider('exhaustedWidths')]
    public function testComponentsRejectExhaustedAvailableWidth(ComponentInterface $component, int $width, ErrorCode $code): void
    {
        $renderer = new ComponentRenderer(new RenderContext($this->translator(), new EmailConfig(), 'en'), $width);

        try {
            $renderer->render($component);
            self::fail('Exhausted width was accepted.');
        } catch (InvalidInputException $exception) {
            self::assertSame($code, $exception->errorCode());
        }
    }

    /**
     * @return iterable<string, array{ComponentInterface, int, ErrorCode}>
     */
    public static function exhaustedWidths(): iterable
    {
        yield 'group' => [new Group([new Text('Content')], padding: 1), 1, ErrorCode::GroupWidthInvalid];
        yield 'columns' => [new Columns([new Text('First')], [new Text('Second')], gap: 0), 1, ErrorCode::ColumnsWidthInvalid];
        yield 'steps' => [new Steps(['One', 'Two']), 1, ErrorCode::StepsWidthInvalid];
    }
}
