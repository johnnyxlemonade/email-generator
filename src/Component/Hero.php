<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\CompositionInterface;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Validation\InputValidator;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\ImageSource;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * An introductory visual section composed from standard email primitives
 */
final readonly class Hero implements CompositionInterface
{
    /**
     * Validates the heading and description while retaining optional media and action
     */
    public function __construct(
        private string|Translation|RichText $heading,
        private string|Translation|RichText $description,
        private ?ImageSource $image = null,
        private ?Button $button = null,
    ) {
        InputValidator::content($heading);
        InputValidator::content($description);
    }

    /**
     * Returns the literal, translated or formatted heading
     */
    public function heading(): string|Translation|RichText
    {
        return $this->heading;
    }

    /**
     * Returns the literal, translated or formatted description
     */
    public function description(): string|Translation|RichText
    {
        return $this->description;
    }

    /**
     * Returns the optional hero image source
     */
    public function image(): ?ImageSource
    {
        return $this->image;
    }

    /**
     * Returns the optional call-to-action button
     */
    public function button(): ?Button
    {
        return $this->button;
    }

    /**
     * Composes a responsive image, heading, description and optional action
     *
     * @return iterable<Group>
     */
    public function components(): iterable
    {
        $children = [];

        if ($this->image !== null) {
            $children[] = new Image($this->image->url(), $this->image->alt());
        }

        $children[] = new Heading($this->heading);
        $children[] = new Text($this->description);

        if ($this->button !== null) {
            $children[] = $this->button;
        }

        yield new Group(
            children: $children,
            backgroundColor: new Color('f8fafc'),
            padding: 24,
            borderColor: new Color('e2e8f0'),
        );
    }
}
