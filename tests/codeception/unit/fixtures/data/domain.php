<?php

return [
    'first' => [
        'domain_id' => 1,
        'domain_createtime' => date('Y-m-d H:i:s'),
        'domain_name' => 'first.ru',
        'domain_status' => 2, // blocked
        'domain_authkey' => Yii::$app->security->generateRandomString(32) . time(),
        'domain_authkey_updated' => date('Y-m-d H:i:s'),
    ],

    'second' => [
        'domain_id' => 2,
        'domain_createtime' => date('Y-m-d H:i:s'),
        'domain_name' => 'second.ru',
        'domain_status' => 1, // active
        'domain_authkey' => Yii::$app->security->generateRandomString(32) . time(),
        'domain_authkey_updated' => date('Y-m-d H:i:s'),
    ],
];
