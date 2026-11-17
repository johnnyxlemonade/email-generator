<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Quote;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Quote composition')
    ->add(new Quote(
        content: new RichText('Small improvements make a ', TextRun::strong('lasting difference'), '.'),
        attribution: 'The Lemonade team',
    ))
    ->render();
