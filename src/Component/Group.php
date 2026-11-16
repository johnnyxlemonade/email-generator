<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Validation\InputValidator;
use Lemonade\EmailGenerator\ValueObject\Color;

/**
 * A visual container whose children stay together throughout expansion and rendering
 */
final readonly class Group implements ComponentInterface
{
    /**
     * Validates child components and the padding around the visual group
     *
     * @param list<ComponentInterface> $children
     */
    public function __construct(
        private array $children = [],
        private ?Color $backgroundColor = null,
        private int $padding = 16,
        private ?Color $borderColor = null,
    ) {
        InputValidator::range($padding, 0, 64, 'Group padding');
        if (!array_is_list($children)) {
            throw new InvalidInputException(ErrorCode::GroupChildrenInvalid, 'Group children must be a list of components.');
        }
        foreach ($children as $child) {
            if (!$child instanceof ComponentInterface) {
                throw new InvalidInputException(ErrorCode::GroupChildrenInvalid, 'Group children must implement ComponentInterface.');
            }
        }
    }

    /**
     * Returns the child components in display order
     *
     * @return list<ComponentInterface>
     */
    public function children(): array
    {
        return $this->children;
    }

    /**
     * Returns the optional group background color
     */
    public function backgroundColor(): ?Color
    {
        return $this->backgroundColor;
    }

    /**
     * Returns the padding on each side in pixels
     */
    public function padding(): int
    {
        return $this->padding;
    }

    /**
     * Returns the optional color of the one-pixel group border
     */
    public function borderColor(): ?Color
    {
        return $this->borderColor;
    }
}
