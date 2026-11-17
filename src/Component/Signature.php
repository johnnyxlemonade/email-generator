<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\CompositionInterface;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Contact;
use Lemonade\EmailGenerator\ValueObject\ImageSource;

/**
 * Contact signature composed from responsive columns and formatted text
 */
final readonly class Signature implements CompositionInterface
{
    /**
     * Creates a presentation from validated contact and image data
     */
    public function __construct(private Contact $contact, private ?ImageSource $image = null) {}

    /**
     * Returns the contact data used by this composition
     */
    public function contact(): Contact
    {
        return $this->contact;
    }

    /**
     * Returns the optional image data used by this composition
     */
    public function image(): ?ImageSource
    {
        return $this->image;
    }

    /**
     * Composes a text signature or responsive image and text columns
     *
     * @return iterable<Text|Columns>
     */
    public function components(): iterable
    {
        $details = new Text($this->formattedDetails());
        if ($this->image === null) {
            yield $details;

            return;
        }

        yield new Columns(
            first: [new Image($this->image->url(), $this->image->alt(), width: 112)],
            second: [$details],
            gap: 16,
        );
    }

    /**
     * Builds one compact text block without empty optional lines
     */
    private function formattedDetails(): RichText
    {
        $parts = [TextRun::strong($this->contact->name())];
        foreach ([$this->contact->position(), $this->contact->organization(), $this->contact->email(), $this->contact->phone()] as $value) {
            if ($value !== null) {
                array_push($parts, "\n", $value);
            }
        }

        return new RichText(...$parts);
    }
}
