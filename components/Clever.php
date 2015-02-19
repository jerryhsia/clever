<?php

namespace app\components;
use Yii;
use yii\db\ActiveRecord;

class Clever
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

    public static function removeIndex($array)
    {
        $result = [];
        foreach ($array as $row) {
            $result[] = $row;
        }
        return $result;
    }

    public static function getRootUrl()
    {
        if (php_sapi_name() == 'cli') {
            return '/';
        } else {
            $base = str_replace('/index.php', '', $_SERVER['PHP_SELF']);
            $protocol = 'http';
            $host = $_SERVER['SERVER_NAME'];
            $port = $_SERVER['SERVER_PORT'] == '80' ? '': ':'.$_SERVER['SERVER_PORT'];
            return sprintf('%s://%s%s%s', $protocol, $host, $port, $base);
        }
    }
}
