<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic',
    'language' => 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
//    'modules' => [
//        'adminmode' => [
//            'class' => 'app\modules\adminmode\Module',
//            'defaultRoute' => '/adminmode/index',
//        ],
//    ],
    'defaultRoute' => 'task/index',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'ZgDZ53epxLTXFZjMIgwFCNjc7Am1DylH',
            'baseUrl' => '',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'authTimeout' => 60 * 60 * 12 , // 12 часов
            'enableAutoLogin' => false,
        ],
        'errorHandler' => [
            //'errorAction' => 'site/error',
            'errorAction' => 'task/index',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
            'class' => 'Swift_SmtpTransport',
            'host' => 'smtp.yandex.ru',
            'username' => 'fivtin@yandex.ru',
            'password' => '',
            'port' => '465',
            'encryption' => 'ssl',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
 
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules'=> [
                '/ajax' => 'ajax',
                '/note/remove/<nid:\d+>' => '/note/remove',
                '/note/read/<nid:\d+>' => '/note/read',
                '/table/<did:\d>/<month:\d{2}>/<year:\d{4}>' => 'table/index',
                //'/table/<month:\d{2}>/<year:\d{4}>' => 'table/index',
                '/support/execute/<execute:\d+>/<prompt:[\pL\s\d\"\-\,\.\:\(\)\_\=\&\;]+>' => 'support/index',
                '/support/remove/<remove:\d+>' => 'support/index',
                '/support/execute/<execute:\d+>' => 'support/index',
                '/support/tv/<param:\d+>' => '/support/tv',
                '/support/internet/<param:\d+>' => '/support/internet',
                '/support/stat/<mode:tv|internet>' => '/support/stat',
                //'/support' => '/support/index',
//                '/site/contact-form' => '/site/contact',
//                '/site/captcha' => '/site/captcha',
//                //'site/login' => 'task/login',
//                '/adminmode' => '/adminmode/default/index',
//                '/adminmode/login' => '/adminmode/default/login',
//                'site/<page:[\w\-]+>/<param:\d+>' => 'site/index',
//                'site/<page:[\w\-]+>' => 'site/index',
                'report/<eid:\d+>/<start:\d{4}-\d{2}-\d{2}>/<finish:\d{4}-\d{2}-\d{2}>' => 'report/personal',
                'report/<eid:\d+>' => 'report/personal',
                '/report' => 'report/index',
                '/<date:\d{4}-\d{2}-\d{2}>' => '/task/index/',
//                '/<date1:\d{4}-\d{2}-\d{2}>/<date2:\d{4}-\d{2}-\d{2}>' => '/task/index/',
                //'<action>' => 'post/<action>',
                'task/<id:new>/<date:\d{4}-\d{2}-\d{2}>' => 'task/view',
                'task/<id:new>/<copy:\d+>' => 'task/view',
                'task/<id:new>' => 'task/view',
                'task/<id:\d+>' => 'task/view',
                //'task/<action:\w+>\-<direct:\w+>' => '/task/index/',
                'task/<action:next|prev|set>\-<direct:month|year|today|reset>' => '/task/index/',
                //'/<action:update|delete>\/<id:\d+>' => '/task/index/',
                //'page/<page:\d+>' => 'post/index',
                '/' => 'task/index',
            ],
        ],
     ],
     'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
//        'allowedIPs' => ['127.0.0.1', '192.168.129.6', '192.168.129.1', '192.168.142.8', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.129.6', '192.168.129.1'],
    ];
}

return $config;
