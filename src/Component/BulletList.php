<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Validation\InputValidator;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * An immutable ordered or unordered list of text values
 */
final readonly class BulletList implements ComponentInterface
{
    /**
     * @var list<string|Translation|RichText>
     */
    private array $items;

    /**
     * Validates list items and detaches external PHP references
     *
     * @param list<string|Translation|RichText> $items
     */
    public function __construct(array $items, private bool $ordered = false)
    {
        InputValidator::textList($items);
        $snapshot = [];
        foreach ($items as $item) {
            $snapshot[] = $item;
        }
        $this->items = $snapshot;
    }

    /**
     * Returns the list items in display order
     *
     * @return list<string|Translation|RichText>
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * Returns whether the list uses numbering
     */
    public function ordered(): bool
    {
        return $this->ordered;
    }
}
