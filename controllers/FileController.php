<?php

namespace app\controllers;

use app\components\App;
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
        $this->fileService = Yii::$container->get('FileService');
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        echo Yii::getAlias('@webroot');
    }

    public function actionCreate()
    {
        $uploadedFile = UploadedFile::getInstanceByName('file');
        return $this->fileService->save($uploadedFile);
    }
}
