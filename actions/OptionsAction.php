<?php



namespace app\actions;
use Yii;

/**
 * Class OptionsAction
 *
 * @package app\actions
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class OptionsAction extends \yii\rest\OptionsAction
{

    public function run($id = null)
    {
        parent::run($id);
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Headers'     => 'X-Access-Token, X-Requested-With, X-HTTP-Method-Override, Content-Type, Accept',
            'Access-Control-Allow-Methods'     => 'GET, POST, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Expose-Headers'    => 'X-Access-Token, X-Pagination-Total-Count, X-Pagination-Page-Count, X-Pagination-Per-Page, X-Pagination-Current-Page, Link'
        ];

        if (!YII_DEBUG) {
            $headers['Access-Control-Max-Age'] = 24 * 3600;
        }

        $headerCollection = Yii::$app->getResponse()->getHeaders();
        foreach ($headers as $key => $value) {
            $headerCollection->set($key, $value);
        }
    }
}
