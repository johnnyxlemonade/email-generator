<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Text;

use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * Combines immutable text runs with supported inline formatting and links
 */
final readonly class RichText
{
    /**
     * @var list<TextRun>
     */
    private array $runs;

    /**
     * Converts literal and translated parts to immutable text runs in input order
     */
    public function __construct(string|Translation|TextRun ...$parts)
    {
        $runs = [];

        foreach ($parts as $part) {
            $runs[] = $part instanceof TextRun ? $part : new TextRun($part);
        }

        $this->runs = $runs;
    }

    /**
     * Returns the inline runs in display order
     *
     * @return list<TextRun>
     */
    public function runs(): array
    {
        return $this->runs;
    }
}
