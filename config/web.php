<?php


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
            //'class' => 'yii\caching\FileCache',
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => 'localhost',
                'port' => 6379,
                'database' => 1,
            ]
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
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

                        'OPTIONS <any:.*>' => 'options'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'role',
                    'extraPatterns' => [
                        'OPTIONS <any:.*>' => 'options'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'file',
                    'extraPatterns' => [
                        'OPTIONS <any:.*>' => 'options'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'module',
                    'extraPatterns' => [
                        'GET <id>/fields' => 'field-index',
                        'POST <id>/fields' => 'field-create',
                        'PUT <id>/fields/<field_id>' => 'field-update',
                        'PUT <id>/fields' => 'field-batch-update',
                        'DELETE <id>/fields/<field_id>' => 'field-delete',

                        'OPTIONS <any:.*>' => 'options'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'data',
                    'patterns' => [
                        'GET <module_name>' => 'index',
                        'POST <module_name>' => 'create',
                        'PUT <module_name>/<id>' => 'update',
                        'DELETE <module_name>/<id>' => 'delete',

                        'OPTIONS <any:.*>' => 'options'
                    ]
                ],
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>'
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
            //'enableCookieValidation' => true,
            //'enableCsrfValidation' => false,
            'cookieValidationKey' => 'app',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser'
            ]
        ],
        'session' => [
            'savePath' => __DIR__ . '/../runtime/session',
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

$config['components'] = array_merge($config['components'], require(__DIR__ . '/service.php'));

return $config;
