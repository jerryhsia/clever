<?php


$config = [
    'id' => 'app',
    'basePath' => dirname(__DIR__),
    'extensions' => require(__DIR__ . '/../vendor/yiisoft/extensions.php'),
    'as AppBehavior' => [
        'class' => 'app\behaviors\AppBehavior'
    ],
    'components' => [
        'user' => [
            'identityClass' => 'app\components\WebUser',
            'enableAutoLogin' => false,
            'enableSession' => false
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
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
                    'controller' => 'app',
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
                        'GET <module_name>/<id>' => 'view',

                        'OPTIONS <any:.*>' => 'options'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'log',
                    'extraPatterns' => [
                        'GET modules' => 'modules',

                        'OPTIONS <any:.*>' => 'options'
                    ]
                ],
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>'
            ]
        ],
        'request' => [
            'cookieValidationKey' => 'app',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser'
            ]
        ],
        'session' => [
            'savePath' => __DIR__ . '/../runtime/session',
        ]
    ],
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return yii\helpers\ArrayHelper::merge(
    $config,
    require(__DIR__ . '/common.php')
);
