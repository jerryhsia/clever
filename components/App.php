<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */
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
}
