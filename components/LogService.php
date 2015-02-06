<?php


namespace app\components;
use app\models\Log;
use Yii;
use yii\base\Component;

/**
 * Class LogService
 *
 * @package app\components
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class LogService extends Component
{

    public $enable = true;

    public function save(Log $log, $attributes)
    {
        $log->setAttributes($attributes, false);
        return $log->save();
    }
}
