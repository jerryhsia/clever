<?php


Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$config = [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\commands'
];

return yii\helpers\ArrayHelper::merge(
    $config,
    require(__DIR__ . '/common.php')
);
