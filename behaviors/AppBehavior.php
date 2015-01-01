<?php


namespace app\behaviors;
use Yii;
use yii\base\Behavior;
use yii\web\Application;


/**
 * Class AppBehavior
 *
 * @package app\behaviors
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class AppBehavior extends Behavior
{
    public function events()
    {
        return [
            Application::EVENT_BEFORE_REQUEST => 'onBeforeRequest',
            Application::EVENT_BEFORE_ACTION  => 'onBeforeAction',
        ];
    }

    public function onBeforeRequest($event)
    {

    }

    public function onBeforeAction($event)
    {
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow'             => 'GET, POST, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
        ];

        if (!YII_DEBUG) {
            $headers['Access-Control-Max-Age'] = 24 * 3600;
        }

        $headerCollection = Yii::$app->response->getHeaders();
        foreach ($headers as $key => $value) {
            $headerCollection->set($key, $value);
        }
    }
}
