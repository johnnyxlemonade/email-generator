<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Component\Steps;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\ButtonGroupAlignment;
use Lemonade\EmailGenerator\Enum\ButtonVariant;

/** @var EmailGenerator $emails */

return $emails->create(
    locale: 'en',
    title: 'Order progress',
    preheader: 'Your order is being prepared.',
)->heading('Order progress')
    ->text('Track the order and choose what you would like to do next.')
    ->add(new Steps(['Order received', 'Payment confirmed', 'Preparing shipment', 'Delivered'], currentStep: 3))
    ->add(new ButtonGroup(
        buttons: [
            new Button('View order', 'https://example.com/orders/123'),
            new Button('Contact support', 'https://example.com/support?order=123', ButtonVariant::Secondary),
        ],
        gap: 12,
        alignment: ButtonGroupAlignment::Center,
    ))
    ->render();
