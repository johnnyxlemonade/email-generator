<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\ValueObject\AttachmentItem;

/**
 * An ordered collection of attachment metadata for compact rendering
 */
final readonly class AttachmentList implements ComponentInterface
{
    /**
     * @var list<AttachmentItem>
     */
    private array $items;

    /**
     * Validates the ordered item collection and detaches external references
     *
     * @param list<AttachmentItem> $items
     */
    public function __construct(array $items)
    {
        if (!array_is_list($items)) {
            throw new InvalidInputException(ErrorCode::AttachmentListInvalid, 'Attachments must be a list.');
        }

        $snapshot = [];

        foreach ($items as $item) {
            if (!$item instanceof AttachmentItem) {
                throw new InvalidInputException(
                    ErrorCode::AttachmentListInvalid,
                    'Attachment entries must be AttachmentItem objects.',
                );
            }

            $snapshot[] = $item;
        }

        $this->items = $snapshot;
    }

    /**
     * Returns attachment metadata in display order
     *
     * @return list<AttachmentItem>
     */
    public function items(): array
    {
        return $this->items;
    }
}
