<?php


namespace app\controllers;
use yii\rest\Controller;


/**
 * Class RestController
 *
 * @package common\components
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class RestController extends Controller
{
    public function accessRules()
    {
        return [];
    }

    public function actions()
    {
        return [
            'options' => [
                'class' => 'app\actions\OptionsAction'
            ]
        ];
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            /*'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['POST', 'PUT', 'DELETE'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 0,
                ],

            ],*/
        ]);
    }

}
