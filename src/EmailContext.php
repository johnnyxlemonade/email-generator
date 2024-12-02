<?php

namespace Lemonade\EmailGenerator;

class EmailContext
{
    public function __construct(
        public bool $includeProducts = false,
        public bool $includeShipping = false,
        public bool $includePayment = false,
        public bool $includePickupPoint = false,
        public bool $includeAttachments = false,
        public bool $includeSummary = false
    ) {}
}