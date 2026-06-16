<?php

return [
    /*
    | Recipient for booking operations emails (new booking, cancellation/refund).
    | Not hardcoded into mailables so it can be changed per environment.
    */
    'admin_email' => env('BOOKING_ADMIN_EMAIL', 'siguldasskati@gmail.com'),
];
