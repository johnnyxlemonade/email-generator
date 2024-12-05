<?php

namespace Lemonade\EmailGenerator\Blocks\Informational;

use Lemonade\EmailGenerator\Blocks\StaticBlock;

class StaticBlockGreetingFooter extends StaticBlock
{
    /**
     * Constructor for `StaticBlockGreetingFooter`.
     * Uses the parent `StaticBlock` constructor to set the template name.
     */
    public function __construct()
    {
        // Pass the template name to the parent constructor
        parent::__construct("StaticBlockGreetingFooter");
    }
}
