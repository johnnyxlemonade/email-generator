<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Validation\InputValidator;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * An immutable rectangular data table
 */
final readonly class Table implements ComponentInterface
{
    /**
     * @var list<string|Translation|RichText>
     */
    private array $headers;

    /**
     * @var list<list<string|Translation|RichText>>
     */
    private array $rows;

    /**
     * Validates the table and detaches references in headers, rows and cells
     *
     * @param list<string|Translation|RichText> $headers
     * @param list<list<string|Translation|RichText>> $rows
     */
    public function __construct(array $headers, array $rows, private bool $compact = false)
    {
        InputValidator::textList($headers);
        if (!array_is_list($rows) || ($headers === [] && $rows === [])) {
            throw new InvalidInputException(ErrorCode::TableInvalid, 'Table needs headers or rows, with rows supplied as a list.');
        }
        $columns = $headers === [] ? null : count($headers);
        $rowSnapshot = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                throw new InvalidInputException(ErrorCode::TableInvalid, 'Table rows must be lists of text values.');
            }
            InputValidator::textList($row);
            $columns ??= count($row);
            if ($columns === 0 || count($row) !== $columns) {
                throw new InvalidInputException(ErrorCode::TableInvalid, 'Table rows must be non-empty and match the column count.');
            }
            $cells = [];
            foreach ($row as $cell) {
                $cells[] = $cell;
            }
            $rowSnapshot[] = $cells;
        }
        $headerSnapshot = [];
        foreach ($headers as $header) {
            $headerSnapshot[] = $header;
        }
        $this->headers = $headerSnapshot;
        $this->rows = $rowSnapshot;
    }

    /**
     * Returns the column headers
     *
     * @return list<string|Translation|RichText>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Returns the table rows in display order
     *
     * @return list<list<string|Translation|RichText>>
     */
    public function rows(): array
    {
        return $this->rows;
    }

    /**
     * Returns whether the table uses a compact label-value presentation
     */
    public function compact(): bool
    {
        return $this->compact;
    }
}
