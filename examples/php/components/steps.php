<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Steps;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\StepsProgress;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Steps component')
    ->add(new Steps(['Created', 'Processing', 'Delivered'], currentStep: 2))
    ->add(new Steps(
        ['Created', 'Processing', 'Delivered'],
        currentStep: null,
        progress: StepsProgress::Completed,
    ))
    ->render();
