<?php


namespace app\controllers;
use app\models\LoginForm;
use Yii;

/**
 * Class UserController
 *
 * @package app\controllers
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class UserController extends RestController
{

    public $allowGuest = true;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    /**
     * POST /users/authentication
     *
     * @return LoginForm|bool
     * @throws \yii\base\InvalidConfigException
     */
    public function actionLogin()
    {
        $user = Yii::$app->user->getIdentity();
        if ($user) {
            return $user;
        } else {
            $model = new LoginForm();
            $model->load(Yii::$app->request->getBodyParams(), '');

            if ($token = Yii::$app->userService->login($model)) {
                return array_merge($model->getUser()->toArray(), [
                    'access_token' => $token
                ]);
            } else {
                return $model;
            }
        }
    }

    public function actionIndex()
    {
        return [];
    }

}
