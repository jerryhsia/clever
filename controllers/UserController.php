<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

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
    public $userService;

    public function __construct($id, $module, $config = [])
    {
        $this->userService = Yii::$container->get('UserService');
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
        return $this->userService->search($params)->all();
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
        $this->userService->save($user, $attributes);
        return $user;
    }

    /**
     * POST /users/login
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
