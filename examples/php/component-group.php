<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Group component')
    ->add(new Group(
        children: [
            new Heading('Grouped content', level: 2),
            new Text('Background, border and padding belong to one visual container.'),
        ],
        backgroundColor: new Color('eff6ff'),
        padding: 24,
        borderColor: new Color('2563eb'),
    ))
    ->render();
