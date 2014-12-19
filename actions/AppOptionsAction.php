<?php

/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

namespace app\actions;
use Yii;
use yii\rest\OptionsAction;

/**
 * Class AppOptionsAction
 *
 * @package app\actions
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class AppOptionsAction extends OptionsAction
{

    public function run($id = null)
    {
        parent::run($id);
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => 'X-Access-Token, X-Requested-With, X-HTTP-Method-Override, Content-Type, Accept',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true'
        ];
        if (!YII_DEBUG) {
            $headers['Access-Control-Max-Age'] = '36000';
        }

        $headerCollection = Yii::$app->getResponse()->getHeaders();
        foreach ($headers as $key => $value) {
            $headerCollection->set($key, $value);
        }

    }
}
