<?php

it('exposes a booking admin email from config', function () {
    config()->set('booking.admin_email', 'ops@example.com');

    expect(config('booking.admin_email'))->toBe('ops@example.com');
});

it('falls back to the default admin email', function () {
    expect(config('booking.admin_email'))->not->toBeEmpty();
});
