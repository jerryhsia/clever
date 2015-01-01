<?php


namespace app\controllers;

use app\models\Field;
use Yii;
use app\models\Module;
use yii\web\NotFoundHttpException;

/**
 * Class DataController
 *
 * @package app\controllers
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class DataController extends RestController
{

    public $moduleService;

    public $dataService;

    public function __construct($id, $module, $config = [])
    {
        $this->moduleService = Yii::$container->get('ModuleService');
        $this->dataService = Yii::$container->get('DataService');
        parent::__construct($id, $module, $config);
    }

    /**
     * Load a module
     *
     * @param $moduleName
     * @return mixed
     * @throws NotFoundHttpException
     */
    private function loadModule($moduleName)
    {
        $module = $this->moduleService->searchModule(['name' => $moduleName])->one();
        if (!$module) {
            throw new NotFoundHttpException(Yii::t('module', 'Module not found'));
        }
        return $module;
    }

    /**
     * Load a data
     *
     * @param $module
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    private function loadData($module, $id)
    {
        $data = $this->dataService->search($module)->andWhere(['id' => $id])->one();
        if (!$data) {
            throw new NotFoundHttpException(Yii::t('data', 'Data not found'));
        }
        return $data;
    }

    /**
     * GET /datas/<module_name>
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $moduleName = Yii::$app->request->getQueryParam('module_name');
        $module = $this->loadModule($moduleName);
        $params = Yii::$app->request->getQueryParams();
        return $this->dataService->search($module, $params)->all();
    }

    /**
     * POST /datas/<module_name>
     *
     * @return Module
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        $moduleName = Yii::$app->request->getQueryParam('module_name');
        $module = $this->loadModule($moduleName);
        $array = Yii::$app->request->getBodyParams();

        $className = $module->getFullClassName();
        $model = new $className();
        return $this->dataService->save($module, $model, $array);
    }

    /**
     * PUT /datas/<module_name>/<id>
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate()
    {
        $moduleName = Yii::$app->request->getQueryParam('module_name');
        $array = Yii::$app->request->getBodyParams();
        $id = Yii::$app->request->getQueryParam('id');

        $module = $this->loadModule($moduleName);
        $model = $this->loadData($module, $id);
        $this->dataService->save($module, $model, $array);
        return $model;
    }

    /**
     * Delete /datas/<module_name>/<id>
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete()
    {
        $moduleName = Yii::$app->request->getQueryParam('module_name');
        $id = Yii::$app->request->getQueryParam('id');

        $module = $this->loadModule($moduleName);
        $model = $this->loadData($module, $id);
        return $this->dataService->delete($module, $model);
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
