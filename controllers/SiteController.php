<?php
namespace app\controllers;

use app\components\App;
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
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->getIdentity();
        App::dump($user);
        exit;
    }

    public function actionLogin()
    {
        echo '<form method="post" action="/users/authentication">
    <input type="text" name="identity">
    <input type="password" name="password">
    <input type="hidden" name="remember" value="1">
    <input type="submit">
</form>';
        exit;
    }

}
