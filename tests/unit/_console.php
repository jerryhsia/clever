<?php

return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../config/console.php'),
    require(__DIR__ . '/../_config.php'),
    [
        'components' => [
            'db' => [
                'dsn' => 'mysql:host=127.0.0.1;dbname=clever_unit',
            ],
            'cache' => [
                'class' => \yii\caching\DummyCache::className(),
            ]
        ],
    ]
);
