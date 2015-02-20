<?php


namespace app\controllers;

use Yii;
use app\models\Role;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class RoleController
 *
 * @package app\controllers
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class RoleController extends RestController
{
    public function __construct($id, $module, $config = [])
    {
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
        $role = Yii::$app->roleService->getRole($id);
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
        return Yii::$app->roleService->getRoles(false);
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
        Yii::$app->roleService->save($role, $array);
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
        Yii::$app->roleService->save($role, $array);
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
        return Yii::$app->roleService->delete($role);
    }
}
