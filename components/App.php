<?php

namespace app\components;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class App
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class App
{

    public static function createPassword($password)
    {
        return md5(sha1($password.'_app'));
    }

    public static function dump($data)
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }

    public static function copyErrors(ActiveRecord $fromModel, ActiveRecord $toModel)
    {
        foreach ($fromModel->getErrors() as $field => $errors) {
            foreach ($errors as $error) {
                $toModel->addError($field, $error);
            }
        }
    }
}
