<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

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
            'Access-Control-Allow-Methods'     => 'GET, POST, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Expose-Headers'    => 'X-Access-Token, X-Pagination-Total-Count, X-Pagination-Page-Count, X-Pagination-Per-Page, X-Pagination-Current-Page, Link'
        ];

        $headerCollection = Yii::$app->response->getHeaders();
        foreach ($headers as $key => $value) {
            $headerCollection->set($key, $value);
        }
    }
}
