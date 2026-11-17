<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Alert;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\AlertVariant;
use Lemonade\EmailGenerator\ValueObject\Translation;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Alert composition')
    ->add(
        new Alert(new Translation('emails.alert_info'), variant: AlertVariant::Info),
        new Alert('Your workspace was created successfully.', 'Ready to start', AlertVariant::Success),
        new Alert('The invitation expires in 24 hours.', 'Invitation expiring', AlertVariant::Warning),
        new Alert('We could not save the billing details.', 'Update required', AlertVariant::Error),
    )
    ->render();
