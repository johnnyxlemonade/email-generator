<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Columns component')
    ->add(new Columns(
        first: [new Heading('First column', level: 2), new Text('Stacks first on mobile.')],
        second: [new Heading('Second column', level: 2), new Text('Uses an equal desktop width.')],
        gap: 24,
    ))
    ->render();
