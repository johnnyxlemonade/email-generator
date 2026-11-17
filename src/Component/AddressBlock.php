<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\CompositionInterface;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Address;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * Internationally neutral postal and contact address composition
 */
final readonly class AddressBlock implements CompositionInterface
{
    /**
     * Creates a presentation from validated address data
     */
    public function __construct(private Address $address) {}

    /**
     * Returns the address data used by this composition
     */
    public function address(): Address
    {
        return $this->address;
    }

    /**
     * Composes the complete address as one compact text block
     *
     * @return iterable<Text>
     */
    public function components(): iterable
    {
        $parts = [
            TextRun::strong($this->address->name()),
            "\n",
            $this->address->street(),
            "\n",
            $this->address->city(),
            "\n",
            $this->address->postalCode(),
            "\n",
            $this->address->country(),
        ];
        foreach ([
            [new Translation('email_generator.address_block.company_id'), $this->address->companyId()],
            [new Translation('email_generator.address_block.tax_id'), $this->address->taxId()],
            [new Translation('email_generator.address_block.email'), $this->address->email()],
            [new Translation('email_generator.address_block.phone'), $this->address->phone()],
        ] as [$label, $value]) {
            if ($value !== null) {
                array_push($parts, "\n", $label, ': ', $value);
            }
        }

        yield new Text(new RichText(...$parts));
    }
}
