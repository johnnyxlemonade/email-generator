<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
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
     * @var array<int, HorizontalAlignment>
     */
    private array $columnAlignments;

    /**
     * Validates the table and detaches references in headers, rows and cells
     *
     * @param list<string|Translation|RichText> $headers
     * @param list<list<string|Translation|RichText>> $rows
     * @param array<int, HorizontalAlignment> $columnAlignments
     */
    public function __construct(
        array $headers,
        array $rows,
        private bool $compact = false,
        private HorizontalAlignment $alignment = HorizontalAlignment::Left,
        array $columnAlignments = [],
    ) {
        $this->headers = self::snapshotHeaders($headers);
        $this->rows = self::snapshotRows($rows, count($this->headers));

        $columnCount = count($this->headers);
        if ($columnCount === 0) {
            $columnCount = count($this->rows[0] ?? throw new InvalidInputException(
                ErrorCode::TableInvalid,
                'Table needs at least one column.',
            ));
        }

        $this->columnAlignments = self::snapshotAlignments($columnAlignments, $columnCount);
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

    /**
     * Returns the column override or the table-wide alignment
     */
    public function alignmentForColumn(int $column): HorizontalAlignment
    {
        return $this->columnAlignments[$column] ?? $this->alignment;
    }

    /**
     * Validates and copies headers without retaining PHP references
     *
     * @param array<mixed> $headers
     * @return list<string|Translation|RichText>
     */
    private static function snapshotHeaders(array $headers): array
    {
        InputValidator::textList($headers);

        $snapshot = [];

        foreach ($headers as $header) {
            $snapshot[] = $header;
        }

        return $snapshot;
    }

    /**
     * Validates rectangular rows and copies every cell without retaining PHP references
     *
     * @param array<mixed> $rows
     * @return list<list<string|Translation|RichText>>
     */
    private static function snapshotRows(array $rows, int $headerCount): array
    {
        if (!array_is_list($rows) || ($headerCount === 0 && $rows === [])) {
            throw new InvalidInputException(ErrorCode::TableInvalid, 'Table needs headers or rows, with rows supplied as a list.');
        }

        $columnCount = $headerCount === 0 ? null : $headerCount;
        $snapshot = [];

        foreach ($rows as $row) {
            if (!is_array($row)) {
                throw new InvalidInputException(ErrorCode::TableInvalid, 'Table rows must be lists of text values.');
            }

            InputValidator::textList($row);
            $columnCount ??= count($row);

            if ($columnCount === 0 || count($row) !== $columnCount) {
                throw new InvalidInputException(ErrorCode::TableInvalid, 'Table rows must be non-empty and match the column count.');
            }

            $cells = [];

            foreach ($row as $cell) {
                $cells[] = $cell;
            }

            $snapshot[] = $cells;
        }

        return $snapshot;
    }

    /**
     * Validates column indexes and copies alignment overrides
     *
     * @param array<mixed> $alignments
     * @return array<int, HorizontalAlignment>
     */
    private static function snapshotAlignments(array $alignments, int $columnCount): array
    {
        $snapshot = [];

        foreach ($alignments as $column => $alignment) {
            if (
                !is_int($column)
                || $column < 0
                || $column >= $columnCount
                || !$alignment instanceof HorizontalAlignment
            ) {
                throw new InvalidInputException(ErrorCode::TableInvalid, 'Table column alignments must reference existing columns.');
            }

            $snapshot[$column] = $alignment;
        }

        return $snapshot;
    }
}
