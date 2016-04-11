<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

use yii\helpers\ArrayHelper;
use app\models\User;

$sfParamLocal = __DIR__ . DIRECTORY_SEPARATOR . 'params-local.php';

$params = ArrayHelper::merge(
    require(__DIR__ . '/params.php'),
    file_exists($sfParamLocal) ? require($sfParamLocal) : []
);


$configComm = [
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],

    'modules' => [
        '1' => [
            'class' => 'app\modules\api1\Module',
        ],
    ],

    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'charset' => 'utf8',
        ],

//        'authManager' => [
//            'class' => 'yii\rbac\PhpManager',
//            'defaultRoles' => [ 'person', 'org', 'mod', 'admin', ],
//        ],


        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'cache' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['1/mail', '1/domain'],
                    'pluralize' => false,
                    'except' => ['delete'],
//                    'extraPatterns' => [
//                        'GET' => 'title',
//                        'GET field/{id}' => 'field',
//                    ],
                ],

                '<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>' => '<_c>/<_a>',
                '<_c:[\w\-]+>/<id:\d+>' => '<_c>/view',
                '<_c:[\w\-]+>/<_a:[\w\-]+>' => '<_c>/<_a>',
                '<_c:[\w\-]+>' => '<_c>/index',
            ],
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/views/mail',
            'htmlLayout' => false,
            'useFileTransport' => true,
        ],

        'cache' => [
            'class' => 'yii\caching\DummyCache',
        ],
        'log' => [
            'class' => 'yii\log\Dispatcher',
        ],
    ],
    'params' => $params,
];

return $configComm;
