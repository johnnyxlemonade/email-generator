<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Validation\InputValidator;

/**
 * Two equal content columns that stack on narrow screens
 */
final readonly class Columns implements ComponentInterface
{
    /**
     * @var list<ComponentInterface>
     */
    private array $first;

    /**
     * @var list<ComponentInterface>
     */
    private array $second;

    /**
     * Validates both columns and detaches external array references
     *
     * @param list<ComponentInterface> $first
     * @param list<ComponentInterface> $second
     */
    public function __construct(array $first, array $second, private int $gap = 16)
    {
        InputValidator::range($gap, 0, 64, 'Columns gap');
        $this->first = self::snapshot($first);
        $this->second = self::snapshot($second);
    }

    /**
     * Returns the first column in reading order
     *
     * @return list<ComponentInterface>
     */
    public function first(): array
    {
        return $this->first;
    }

    /**
     * Returns the second column in reading order
     *
     * @return list<ComponentInterface>
     */
    public function second(): array
    {
        return $this->second;
    }

    /**
     * Returns the horizontal desktop and vertical mobile gap in pixels
     */
    public function gap(): int
    {
        return $this->gap;
    }

    /**
     * Copies a validated column without retaining PHP references
     *
     * @param list<ComponentInterface> $children
     * @return list<ComponentInterface>
     */
    private static function snapshot(array $children): array
    {
        if (!array_is_list($children)) {
            throw new InvalidInputException(ErrorCode::ColumnsChildrenInvalid, 'Column children must be a list of components.');
        }
        $snapshot = [];
        foreach ($children as $child) {
            if (!$child instanceof ComponentInterface) {
                throw new InvalidInputException(ErrorCode::ColumnsChildrenInvalid, 'Column children must implement ComponentInterface.');
            }
            $snapshot[] = $child;
        }

        return $snapshot;
    }
}
