<?php

use App\Mail\ContactUsMail;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

it('creates mail with correct properties', function () {
    $mail = new ContactUsMail(
        'Jānis',
        'Bērziņš',
        '+371 28123456',
        'janis.berzins@example.com',
        'Vai ir pieejamas mājas vasaras sezonā?',
        '192.168.1.1'
    );

    expect($mail->firstName)->toBe('Jānis');
    expect($mail->lastName)->toBe('Bērziņš');
    expect($mail->phoneNumber)->toBe('+371 28123456');
    expect($mail->email)->toBe('janis.berzins@example.com');
    expect($mail->question)->toBe('Vai ir pieejamas mājas vasaras sezonā?');
    expect($mail->ipAddress)->toBe('192.168.1.1');
});

it('creates mail with empty question', function () {
    $mail = new ContactUsMail(
        'Anna',
        'Kļava',
        '29876543',
        'anna.klava@example.com',
        '',
        '127.0.0.1'
    );

    expect($mail->question)->toBe('');
});

it('has correct envelope configuration', function () {
    $mail = new ContactUsMail(
        'Jānis',
        'Bērziņš',
        '+371 28123456',
        'janis.berzins@example.com',
        'Test question',
        '192.168.1.1'
    );

    $envelope = $mail->envelope();

    expect($envelope->subject)->toBe('Jauns kontaktu ziņojums no mājas lapas');
    expect($envelope->replyTo[0]->address)->toBe('janis.berzins@example.com');
});

it('uses correct view and passes correct data', function () {
    $mail = new ContactUsMail(
        'Jānis',
        'Bērziņš',
        '+371 28123456',
        'janis.berzins@example.com',
        'Test question',
        '192.168.1.1'
    );

    $content = $mail->content();

    expect($content->view)->toBe('mail.contact-us-mail');
    expect($content->with)->toHaveKeys([
        'firstName',
        'lastName',
        'phoneNumber',
        'email',
        'question',
        'ipAddress',
    ]);
});

it('renders email content correctly', function () {
    $mail = new ContactUsMail(
        'Jānis',
        'Bērziņš',
        '+371 28123456',
        'janis.berzins@example.com',
        'Test question',
        '192.168.1.1'
    );

    $rendered = $mail->render();

    expect($rendered)->toContain('Jānis');
    expect($rendered)->toContain('Bērziņš');
    expect($rendered)->toContain('+371 28123456');
    expect($rendered)->toContain('janis.berzins@example.com');
    expect($rendered)->toContain('Test question');
    expect($rendered)->toContain('192.168.1.1');
    expect($rendered)->toContain('Datu apstrādes piekrišana');
});

it('renders email content with Latvian characters', function () {
    $mail = new ContactUsMail(
        'Jānis-Mārtiņš',
        'Bērziņš-Kļava',
        '+371 28123456',
        'janis@example.com',
        'Interesē informācija par māju nomas cenām augustā.',
        '192.168.1.1'
    );

    $rendered = $mail->render();

    expect($rendered)->toContain('Jānis-Mārtiņš');
    expect($rendered)->toContain('Bērziņš-Kļava');
    expect($rendered)->toContain('Interesē informācija par māju nomas cenām augustā.');
});

it('renders email content without question', function () {
    $mail = new ContactUsMail(
        'Anna',
        'Kļava',
        '29876543',
        'anna.klava@example.com',
        '',
        '127.0.0.1'
    );

    $rendered = $mail->render();

    expect($rendered)->toContain('Anna');
    expect($rendered)->toContain('Kļava');
    expect($rendered)->toContain('29876543');
    expect($rendered)->toContain('anna.klava@example.com');
    expect($rendered)->toContain('127.0.0.1');
});

it('includes action buttons in email', function () {
    $mail = new ContactUsMail(
        'Jānis',
        'Bērziņš',
        '+371 28123456',
        'janis.berzins@example.com',
        'Test question',
        '192.168.1.1'
    );

    $rendered = $mail->render();

    expect($rendered)->toContain('mailto:janis.berzins@example.com');
    expect($rendered)->toContain('tel:+371 28123456');
    expect($rendered)->toContain('Atbildēt klientam');
    expect($rendered)->toContain('Zvanīt klientam');
});

it('includes company branding in email', function () {
    $mail = new ContactUsMail(
        'Jānis',
        'Bērziņš',
        '+371 28123456',
        'janis.berzins@example.com',
        'Test question',
        '192.168.1.1'
    );

    $rendered = $mail->render();

    expect($rendered)->toContain('Siguldas Skati');
    expect($rendered)->toContain('Siguldas Skati Logo');
});

it('includes GDPR compliance information', function () {
    $mail = new ContactUsMail(
        'Jānis',
        'Bērziņš',
        '+371 28123456',
        'janis.berzins@example.com',
        'Test question',
        '192.168.1.1'
    );

    $rendered = $mail->render();

    expect($rendered)->toContain('GDPR');
    expect($rendered)->toContain('privātuma politiku');
    expect($rendered)->toContain('Piekrišanas laiks');
    expect($rendered)->toContain('IP adrese');
});
