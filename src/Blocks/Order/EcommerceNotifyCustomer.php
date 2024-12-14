<?php

namespace Lemonade\EmailGenerator\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\AbstractBlock;
use Lemonade\EmailGenerator\Blocks\StaticBlock;
use Lemonade\EmailGenerator\Context\ContextData;

class EcommerceNotifyCustomer extends StaticBlock
{

    /**
     * Constructor for `EcommerceNotifyCustomer`.
     * Uses the parent `StaticBlock` constructor to set the template name.
     */
    public function __construct()
    {
        // Pass the template name to the parent constructor
        parent::__construct("EcommerceNotifyCustomer");
    }

}
