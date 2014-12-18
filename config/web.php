<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

$config = [
    'id' => 'app',
    'basePath' => dirname(__DIR__),
    'extensions' => require(__DIR__ . '/../vendor/yiisoft/extensions.php'),
    'bootstrap' => ['log'],
    'language' => 'en-US',
    'sourceLanguage' => 'en',
    'as AppBehavior' => [
        'class' => 'app\behaviors\AppBehavior'
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
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
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'user',
                    'extraPatterns' => [
                        'POST authentication' => 'login',
                        'DELETE authentication'=> 'logout',
                        'OPTIONS authentication' => 'options'
                    ]
                ],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'role'],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'module',
                    'extraPatterns' => [
                        'GET {id}/fields' => 'field-index',
                        'POST {id}/fields' => 'field-create',
                        'PUT {id}/fields/{field_id}' => 'field-update'
                    ]
                ]
            ]
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => __DIR__ . '/../messages',
                ]
            ],
        ],
        'request' => [
            'enableCookieValidation' => true,
            'enableCsrfValidation' => false,
            'cookieValidationKey' => 'app',
        ]
    ],
    'params' => require(__DIR__ . '/params.php'),
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

Yii::$container->setSingleton('UserService', 'app\components\UserService');
Yii::$container->setSingleton('RoleService', 'app\components\RoleService');
Yii::$container->setSingleton('SettingService', 'app\components\SettingService');
Yii::$container->setSingleton('ModuleService', 'app\components\ModuleService');

return $config;
