<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\ValueObject\Address;

/**
 * Internationally neutral postal and contact address
 */
final readonly class AddressBlock implements ComponentInterface
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
}
