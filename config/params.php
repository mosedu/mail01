<?php

return [
    'adminEmail' => 'admin@example.com',
    'hostname' => 'mail01.dev',

    'servers' => [
        'internal' => [
            'mailer' => [
                'class' => 'yii\swiftmailer\Mailer',
                'useFileTransport' => false,
                'transport' => [
                    'class' => 'Swift_SmtpTransport',
                    'host' => '10.128.1.14',
                    'username' => 'eventreg@educom.ru',
                    'password' => 'eventreg@educom.ru',
                    'port' => '25',
//            'port' => '587',
//            'encryption' => 'tls',
                ],
//            'viewPath' => '@app/views/mail',
            ],
            'from' => 'eventreg@educom.ru',
            'errorto' => 'eventmsg@educom.ru',
        ],
    ],
];
