<?php

return [
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache'
            /*'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => 'localhost',
                'port' => 6379
            ]*/
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.163.com',
                'username' => 'nldy9916@163.com',
                'password' => 'jerry9916',
                'port' => '465',
                'encryption' => 'ssl',
            ]
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
        'db' => [
            'class'       => 'yii\db\Connection',
            'dsn'         => 'mysql:host=127.0.0.1;dbname=clever',
            'username'    => 'root',
            'password'    => '123456',
            'charset'     => 'utf8',
            'tablePrefix' => 'clever_'
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => __DIR__ . '/../messages',
                ]
            ],
        ],
        'roleService' => [
            'class' => 'app\components\RoleService',
        ],
        'userService' => [
            'class' => 'app\components\UserService',
        ],
        'dataService' => [
            'class' => 'app\components\DataService',
        ],
        'fileService' => [
            'class' => 'app\components\FileService',
        ],
        'moduleService' => [
            'class' => 'app\components\ModuleService',
        ],
        'appService' => [
            'class' => 'app\components\AppService',
        ],
        'settingService' => [
            'class' => 'app\components\SettingService',
        ],
        'logService' => [
            'class' => 'app\components\logService',
        ]
    ],
    'params' => require(__DIR__ . '/params.php'),
];
