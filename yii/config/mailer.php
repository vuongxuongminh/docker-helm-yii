<?php

return [
    'class' => 'yii\swiftmailer\Mailer',
    // send all mails to a file by default. You have to set
    // 'useFileTransport' to false and configure a transport
    // for the mailer to send real emails.
    'useFileTransport' => false,
    'transport' => [
        'class' => 'Swift_SmtpTransport',
        'host' => $_SERVER['MAILER_HOST'],
        'port' => $_SERVER['MAILER_PORT'],
        'username' => $_SERVER['MAILER_USERNAME'],
        'password' => $_SERVER['MAILER_PASSWORD'],
    ],
];