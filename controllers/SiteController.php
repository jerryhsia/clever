<?php
namespace app\controllers;

use app\components\Clever;
use jerryhsia\JsonExporter;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\Controller;


/**
 * Class SiteController
 *
 * @package app\controllers
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class SiteController extends Controller
{
    public function behaviors()
    {
        return [

        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'test' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        exit('Access error');
    }

    public function actionLogin()
    {
        exit('Access error');
    }

}
