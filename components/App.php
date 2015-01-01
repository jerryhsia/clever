<?php

namespace app\components;
use Yii;

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
}
