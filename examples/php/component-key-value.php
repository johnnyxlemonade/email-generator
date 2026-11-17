<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'KeyValue composition')
    ->add(KeyValue::listing(
        new KeyValue('Account', 'Alex'),
        new KeyValue('Plan', new RichText(TextRun::strong('Team'))),
        new KeyValue('Renewal', 'January 15'),
    ))
    ->render();
