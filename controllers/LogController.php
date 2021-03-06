<?php


namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * Class LogController
 *
 * @package app\controllers
 * @author Jerry Hsia<jerry9916@qq.com>
 */
class LogController extends RestController
{
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    /**
     * Load a log
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    private function load($id)
    {
        $log = Yii::$app->logService->search(['id' => $id])->one();
        if (!$log) {
            throw new NotFoundHttpException(Yii::t('log', 'Log not found'));
        }
        return $log;
    }

    /**
     * GET /logs
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $params = Yii::$app->request->getQueryParams();
        $query = Yii::$app->logService->search($params);
        $query->with(['user']);
        $query->orderBy(['created_at' => SORT_DESC]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->getQueryParam('per_page')
            ],
            'sort' => [
                'enableMultiSort' => true
            ]
        ]);
    }

    /**
     * Delete /logs/:id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->getQueryParam('id');

        $log = $this->load($id);
        return Yii::$app->logService->delete($log);
    }

    public function actionModules()
    {
        return Yii::$app->logService->getModules();
    }
}
