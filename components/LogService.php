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

    protected $enable = true;

    public function save(Log $log, $attributes)
    {
        $log->setAttributes($attributes, false);
        return $log->save();
    }

    public function enable()
    {
        $this->enable = true;
    }

    public function disable()
    {
        $this->enable = false;
    }

    public function isEnable()
    {
        return $this->enable;
    }

    public function search($filters = [])
    {
        $query = Log::find();

        if (isset($filters['id'])) {
            $query->andFilterWhere(['id' => $filters['id']]);
        }

        if (isset($filters['module_id'])) {
            $query->andFilterWhere(['module_id' => $filters['module_id']]);
        }

        if (isset($filters['data_id'])) {
            $query->andFilterWhere(['data_id' => $filters['data_id']]);
        }

        if (isset($filters['action'])) {
            $query->andFilterWhere(['action' => $filters['action']]);
        }

        return $query;
    }

    public function getModules()
    {
        $data = [
            ['id' => Log::ROLE_MODULE_ID, ],
        ];
    }

    public function delete(Log $log)
    {
        return $log->delete() === false ? false : true;
    }
}
