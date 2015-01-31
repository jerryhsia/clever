<?php


namespace app\controllers;

use app\components\App;
use app\models\Field;
use Yii;
use app\models\Module;
use yii\web\NotFoundHttpException;

/**
 * Class ModuleController
 *
 * @package app\controllers
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class ModuleController extends RestController
{

    public function accessRules()
    {
        return [
            [
                'allow' => true,
                //'roles' => ['@'],
            ]
        ];
    }

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    /**
     * Load a module
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    private function loadModule($id)
    {
        $module = Yii::$app->moduleService->getModule($id);
        if (!$module) {
            throw new NotFoundHttpException(Yii::t('module', 'Module not found'));
        }
        return $module;
    }

    /**
     * GET /modules
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return Yii::$app->moduleService->getModules(false);
    }

    /**
     * POST /modules
     *
     * @return Module
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        $array = Yii::$app->request->getBodyParams();
        $module = new Module();
        Yii::$app->moduleService->saveModule($module, $array);
        return $module;
    }

    /**
     * PUT /modules/<id>
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->getQueryParam('id');
        $array = Yii::$app->request->getBodyParams();

        $module = $this->loadModule($id);
        Yii::$app->moduleService->saveModule($module, $array);
        return $module;
    }

    /**
     * Delete /modules/<id>
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->getQueryParam('id');

        $module = $this->loadModule($id);
        return Yii::$app->moduleService->deleteModule($module);
    }


    /**
     * Load a field
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    private function loadField(Module $module, $id)
    {
        $field = Yii::$app->moduleService->getField($module, $id);
        if (!$field) {
            throw new NotFoundHttpException(Yii::t('module', 'Field not found'));
        }
        return $field;
    }

    /**
     * GET /modules/<id>/fields
     *
     * @return mixed
     */
    public function actionFieldIndex()
    {
        $moduleId = Yii::$app->request->getQueryParam('id');
        $module = $this->loadModule($moduleId);

        $result = Yii::$app->moduleService->getFields($module, false);
        return $result;
    }

    /**
     * POST /modules/<id>/fields
     *
     * @return Field
     * @throws \yii\base\InvalidConfigException
     */
    public function actionFieldCreate()
    {
        $moduleId = Yii::$app->request->getQueryParam('id');
        $module = $this->loadModule($moduleId);
        $attributes = Yii::$app->request->getBodyParams();
        $field = new Field();
        Yii::$app->moduleService->saveField($module, $field, $attributes);
        return $field;
    }

    /**
     * PUT /modules/<id>/fields/<field_id>
     *
     * @return Field
     * @throws \yii\base\InvalidConfigException
     */
    public function actionFieldUpdate()
    {
        $moduleId = Yii::$app->request->getQueryParam('id');
        $fieldId = Yii::$app->request->getQueryParam('field_id');
        $module = $this->loadModule($moduleId);
        $field = $this->loadField($module, $fieldId);
        $attributes = Yii::$app->request->getBodyParams();
        Yii::$app->moduleService->saveField($module, $field, $attributes);
        return $field;
    }

    /**
     * PUT /modules/<id>/fields
     *
     * @return Integer
     * @throws \yii\base\InvalidConfigException
     */
    public function actionFieldBatchUpdate()
    {
        $moduleId = Yii::$app->request->getQueryParam('id');
        $module = $this->loadModule($moduleId);
        $attributes = Yii::$app->request->getBodyParams();

        return Yii::$app->moduleService->saveFields($module, $attributes);
    }

    /**
     * DELETE /modules/<id>/fields/<field_id>
     *
     * @return Field
     * @throws \yii\base\InvalidConfigException
     */
    public function actionFieldDelete()
    {
        $moduleId = Yii::$app->request->getQueryParam('id');
        $fieldId = Yii::$app->request->getQueryParam('field_id');
        $module = $this->loadModule($moduleId);
        $field = $this->loadField($module, $fieldId);
        return Yii::$app->moduleService->deleteField($module, $field);
    }
}
