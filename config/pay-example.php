<?php
return [
    'exchange-api-url' => 'https://developers.paysera.com/tasks/api/currency-exchange-rates',
    //'exchange-api-url' => 'https://kobacik.com/payment.json',

    'base-currency' => 'EUR',

    'operation-types' => [
        'deposit',
        'withdraw'
    ],

    'deposit-fee' => 0.03,
    'free-charge-amount' => 1000,
    'free-charge-time-per-week' => 3,

    'withdrawn' => [
        'business'  => 0.5,
        'private'   => 0.3
    ],
];
