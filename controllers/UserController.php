<?php


namespace app\controllers;
use app\models\LoginForm;
use Yii;
use app\models\User;

/**
 * Class UserController
 *
 * @package app\controllers
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class UserController extends RestController
{
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    /**
     * GET /users
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $params = Yii::$app->request->getQueryParams();
        return Yii::$app->userService->search($params)->all();
    }

    /**
     * POST /users
     *
     * @return User
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        $attributes = Yii::$app->request->getBodyParams();
        $user = new User();
        Yii::$app->userService->save($user, $attributes);
        return $user;
    }

    /**
     * POST /users/authentication
     *
     * @return LoginForm|bool
     * @throws \yii\base\InvalidConfigException
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->getBodyParams(), '');

        if (!$model->login()) {
            return $model;
        }

        return $model->getUser();
    }

}
