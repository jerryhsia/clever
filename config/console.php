<?php


Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => __DIR__ . '/../messages',
                ]
            ],
        ]
    ],
    'params' => require(__DIR__ . '/params.php')
];

Yii::$container->setSingleton('UserService', 'app\components\UserService');
Yii::$container->setSingleton('RoleService', 'app\components\RoleService');
Yii::$container->setSingleton('SettingService', 'app\components\SettingService');
Yii::$container->setSingleton('ModuleService', 'app\components\ModuleService');
Yii::$container->setSingleton('DataService', 'app\components\DataService');

return $config;
