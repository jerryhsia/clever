<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

namespace app\controllers;

use app\components\Consts;
use Yii;
use app\models\Role;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class RoleController
 *
 * @package app\controllers
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class RoleController extends Controller
{
    /**
     * @var
     */
    public $roleService;

    public function __construct($id, $module, $config = [])
    {
        $this->roleService = Yii::$container->get('RoleService');
        parent::__construct($id, $module, $config);
    }

    /**
     * Load a role
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    private function load($id)
    {
        $role = $this->roleService->search(['id' => $id])->one();
        if (!$role) {
            throw new NotFoundHttpException(Yii::t('role', 'Role not found'));
        }
        return $role;
    }

    /**
     * GET /roles
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $params = Yii::$app->request->getQueryParams();
        return $this->roleService->search($params)->all();
    }

    /**
     * POST /roles
     *
     * @return Role
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        $array = Yii::$app->request->getBodyParams();
        $role = new Role();
        $this->roleService->save($role, $array);
        return $role;
    }

    /**
     * PUT /roles/:id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->getQueryParam('id');
        $array = Yii::$app->request->getBodyParams();

        $role = $this->load($id);
        $this->roleService->save($role, $array);
        return $role;
    }

    /**
     * Delete /roles/:id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->getQueryParam('id');

        $role = $this->load($id);
        return $this->roleService->delete($role);
    }
}
