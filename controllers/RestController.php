<?php


namespace app\controllers;
use app\filters\AuthFilter;
use yii\filters\AccessControl;
use yii\rest\Controller;


/**
 * Class RestController
 *
 * @package common\components
 * @author Jerry Hsia<jerry9916@qq.com>
 */
class RestController extends Controller
{

    protected $allowGuest = false;

    public function accessRules()
    {
        return [
            [
                'allow' => true
            ]
        ];
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
            'access' => [
                'class' => AccessControl::className(),
                'rules' => array_merge([
                    [
                        'allow' => true,
                        'actions' => ['options']
                    ],
                ], $this->accessRules())
            ],
            'authenticator' => [
                'class' => AuthFilter::className(),
                'allowGuest' => $this->allowGuest
            ]
        ]);
    }

}
