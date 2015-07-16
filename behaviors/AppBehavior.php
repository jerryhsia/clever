<?php


namespace app\behaviors;
use Yii;
use yii\base\Behavior;
use yii\web\Application;


/**
 * Class AppBehavior
 *
 * @package app\behaviors
 * @author Jerry Hsia<jerry9916@qq.com>
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
        $lang = Yii::$app->request->getQueryParam('_lang');
        if (strpos($lang, '-') !== false) {
            $lang = explode('-', $lang);
            $lang[1] = strtoupper($lang[1]);
            $lang = implode('-', $lang);
        } else {
            $lang = Yii::$app->request->getHeaders()->get('Accept-Language');
        }

        if (in_array($lang, ['zh-CN', 'en-US'])) {
            Yii::$app->language = $lang;
        } else {
            Yii::$app->language = 'en-US';
        }
    }

    public function onBeforeAction($event)
    {
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow'             => 'GET, POST, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Expose-Headers'    => 'X-Access-Token, X-Pagination-Total-Count, X-Pagination-Page-Count, X-Pagination-Per-Page, X-Pagination-Current-Page, Link'
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
