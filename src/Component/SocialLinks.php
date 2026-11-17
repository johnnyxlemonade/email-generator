<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\CompositionInterface;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\SocialLink;

/**
 * An arbitrary collection of labeled social profile links
 */
final readonly class SocialLinks implements CompositionInterface
{
    /**
     * @var list<SocialLink>
     */
    private array $links;

    /**
     * Validates the collection and detaches it from external array references
     *
     * @param list<SocialLink> $links
     */
    public function __construct(array $links)
    {
        if (!array_is_list($links)) {
            throw new InvalidInputException(ErrorCode::SocialLinksInvalid, 'Social links must be a list.');
        }

        $snapshot = [];

        foreach ($links as $link) {
            if (!$link instanceof SocialLink) {
                throw new InvalidInputException(
                    ErrorCode::SocialLinksInvalid,
                    'Social link entries must be SocialLink objects.',
                );
            }

            $snapshot[] = $link;
        }

        $this->links = $snapshot;
    }

    /**
     * Returns profile links in display order
     *
     * @return list<SocialLink>
     */
    public function links(): array
    {
        return $this->links;
    }

    /**
     * Composes safe text links separated by mobile-friendly wrapping spaces
     *
     * @return iterable<Text>
     */
    public function components(): iterable
    {
        if ($this->links === []) {
            return;
        }

        $parts = [];

        foreach ($this->links as $index => $link) {
            if ($index > 0) {
                $parts[] = "\u{00A0}· ";
            }

            $parts[] = TextRun::link($link->label(), $link->url());
        }

        yield new Text(new RichText(...$parts));
    }
}
