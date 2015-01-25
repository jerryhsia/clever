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
        $str = '{id}-{name}';
        preg_match_all('/\{(.*?)\}/i', $str, $arr3);
        var_dump($arr3);
        $arr = ['id' => 1, 'name' => 'Jerry'];
        $arr2 = [];
        foreach ($arr as $k => $v) {
            $arr2[$k] = '{'.$k.'}';
        }
        echo str_replace($arr2, $arr, $str);
    }

    public function actionCreate()
    {
        $uploadedFile = UploadedFile::getInstanceByName('file');
        return Yii::$app->fileService->save($uploadedFile);
    }
}
