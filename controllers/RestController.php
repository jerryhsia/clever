<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

namespace app\controllers;
use Yii;
use yii\rest\Controller;


/**
 * Class RestController
 *
 * @package common\components
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class RestController extends Controller
{
    public function actions()
    {
        return [
            'options' => [
                'class' => 'app\actions\OptionsAction',
            ],
        ];
    }

    public function accessRules()
    {
        return [];
    }
}
