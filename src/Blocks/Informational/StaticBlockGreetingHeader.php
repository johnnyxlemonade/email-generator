<?php

namespace Lemonade\EmailGenerator\Blocks\Informational;

use Lemonade\EmailGenerator\Blocks\StaticBlock;

class StaticBlockGreetingHeader extends StaticBlock
{
    /**
     * Constructor for `StaticBlockGreetingHeader`.
     * Uses the parent `StaticBlock` constructor to set the template name.
     */
    public function __construct()
    {
        // Pass the template name to the parent constructor
        parent::__construct("StaticBlockGreetingHeader");
    }
}

