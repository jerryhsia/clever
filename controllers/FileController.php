<?php

namespace app\controllers;

use Yii;
use yii\web\UploadedFile;

/**
 * Class FileController
 *
 * @package app\controllers
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class FileController extends RestController
{

    public $fileService;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        echo Yii::getAlias('@webroot');
    }

    public function actionCreate()
    {
        $uploadedFile = UploadedFile::getInstanceByName('file');
        return Yii::$app->fileService->save($uploadedFile);
    }
}
