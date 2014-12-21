<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

namespace app\controllers;

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

    /**
     * @var
     */
    public $moduleService;

    public function __construct($id, $module, $config = [])
    {
        $this->moduleService = Yii::$container->get('ModuleService');
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
        $module = $this->moduleService->searchModule(['id' => $id])->one();
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
        $params = Yii::$app->request->getQueryParams();
        return $this->moduleService->searchModule($params)->all();
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
        $this->moduleService->saveModule($module, $array);
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
        $this->moduleService->saveModule($module, $array);
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
        return $this->moduleService->deleteModule($module);
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
        $field = $this->moduleService->searchField($module, ['id' => $id])->one();
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
        $params = Yii::$app->request->getQueryParams();
        unset($params['id']);

        $result = $this->moduleService->searchField($module, $params)->all();
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
        $this->moduleService->saveField($module, $field, $attributes);
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
        $this->moduleService->saveField($module, $field, $attributes);
        return $field;
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
        return $this->moduleService->deleteField($module, $field);
    }
}
