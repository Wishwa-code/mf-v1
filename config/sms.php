<?php

return [
    'provider' => env('SMS_PROVIDER', 'Dialog'), // Dialog | Hutch
    'mask'     => env('SMS_MASK', 'ASIPiya'),
    'dialog'   => [
        'username' => env('DIALOG_USERNAME', 'ASIPIYA'),
        'password' => env('DIALOG_PASSWORD', 'Dialog@123'),
    ],
    'hutch'    => [
        'username' => env('HUTCH_USERNAME', 'finance.asipiya@gmail.com'),
        'password' => env('HUTCH_PASSWORD', 'Asipiya@hutch123'),
    ],
];
