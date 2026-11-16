<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Tests\Integration;

use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Tests\Support\EmailTestCase;
use Lemonade\EmailGenerator\ValueObject\Translation;
use Lemonade\Framework\Container\Container;
use Lemonade\Framework\Core\CliKernelFactory;

/**
 * Exercises immutable documents through the framework's public email service
 */
final class ImmutableDocumentTest extends EmailTestCase
{
    public function testPublicFluentApiKeepsBranchesIndependentAfterBootstrap(): void
    {
        $container = new Container();
        (new CliKernelFactory($container))->create(self::context())->bootstrap();
        $emails = $container->get(EmailGenerator::class);
        $base = $emails->create(locale: 'cs')->heading(new Translation('emails.welcome', ['name' => 'Alex']));
        $before = $base->render();
        $registration = $base->text('Registration');
        $reminder = $base->text('Reminder');
        $registration = $registration->button('Activate', 'https://example.com/activate');

        $expected = $emails->create(locale: 'cs')
            ->heading(new Translation('emails.welcome', ['name' => 'Alex']))
            ->text('Registration')
            ->button('Activate', 'https://example.com/activate')
            ->render();

        self::assertSame($before, $base->render());
        self::assertSame($expected, $registration->render());
        self::assertStringContainsString('Vítejte, Alex!', $registration->render());
        self::assertStringContainsString('Reminder', $reminder->render());
        self::assertStringNotContainsString('Registration', $reminder->render());
        self::assertStringNotContainsString('Activate', $reminder->render());
    }
}
