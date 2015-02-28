<?php


namespace app\controllers;

use Yii;
use app\models\App;
use yii\web\NotFoundHttpException;

/**
 * Class AppController
 *
 * @package app\controllers
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class AppController extends RestController
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

    public function __construct($id, $app, $config = [])
    {
        parent::__construct($id, $app, $config);
    }

    /**
     * Load a app
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    private function loadApp($id)
    {
        $app = Yii::$app->appService->getApp($id);
        if (!$app) {
            throw new NotFoundHttpException(Yii::t('app', 'App not found'));
        }
        return $app;
    }

    /**
     * GET /apps
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return Yii::$app->appService->getApps(false);
    }

    /**
     * POST /apps
     *
     * @return App
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        $array = Yii::$app->request->getBodyParams();
        $app = new App();
        Yii::$app->appService->saveApp($app, $array);
        return $app;
    }

    /**
     * PUT /apps/<id>
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->getQueryParam('id');
        $array = Yii::$app->request->getBodyParams();

        $app = $this->loadApp($id);
        Yii::$app->appService->saveApp($app, $array);
        return $app;
    }

    /**
     * Delete /apps/<id>
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->getQueryParam('id');

        $app = $this->loadApp($id);
        return Yii::$app->appService->deleteApp($app);
    }
}
