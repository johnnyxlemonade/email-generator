<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Validation\InputValidator;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * A compact status label with a predefined color variant
 */
final readonly class Badge implements ComponentInterface
{
    /**
     * Validates the literal or translated status label
     */
    public function __construct(
        private string|Translation $label,
        private BadgeVariant $variant = BadgeVariant::Neutral,
    ) {
        InputValidator::text($label);
    }

    /**
     * Returns the status label
     */
    public function label(): string|Translation
    {
        return $this->label;
    }

    /**
     * Returns the predefined status appearance
     */
    public function variant(): BadgeVariant
    {
        return $this->variant;
    }
}
