<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Validation\InputValidator;

/**
 * Two or more related actions displayed as one responsive visual group
 */
final readonly class ButtonGroup implements ComponentInterface
{
    /**
     * @var list<Button>
     */
    private array $buttons;

    /**
     * Validates the actions and detaches external array references
     *
     * @param list<ComponentInterface> $buttons
     */
    public function __construct(
        array $buttons,
        private int $gap = 12,
        private HorizontalAlignment $alignment = HorizontalAlignment::Left,
    ) {
        InputValidator::range($gap, 0, 64, 'Button group gap');
        if (!array_is_list($buttons) || count($buttons) < 2) {
            throw new InvalidInputException(ErrorCode::ButtonGroupButtonsInvalid, 'A button group requires a list of at least two buttons.');
        }
        $snapshot = [];
        foreach ($buttons as $button) {
            if (!$button instanceof Button) {
                throw new InvalidInputException(ErrorCode::ButtonGroupButtonsInvalid, 'Button group entries must be Button components.');
            }
            $snapshot[] = $button;
        }
        $this->buttons = $snapshot;
    }

    /**
     * Returns the actions in reading order
     *
     * @return list<Button>
     */
    public function buttons(): array
    {
        return $this->buttons;
    }

    /**
     * Returns the desktop horizontal and mobile vertical gap in pixels
     */
    public function gap(): int
    {
        return $this->gap;
    }

    /**
     * Returns the desktop horizontal alignment
     */
    public function alignment(): HorizontalAlignment
    {
        return $this->alignment;
    }
}
