<?php


namespace app\components;
use app\models\Log;
use Yii;
use yii\base\Component;

/**
 * Class LogService
 *
 * @package app\components
 * @author Jerry Hsia<jerry9916@qq.com>
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
            ['id' => Log::MODULE_ROLE_ID, 'name' => Yii::t('app', 'Role')],
            ['id' => Log::MODULE_MODULE_ID, 'name' => Yii::t('app', 'Module')],
            ['id' => Log::MODULE_FIELD_ID, 'name' => Yii::t('app', 'Field')]
        ];

        $modules = Yii::$app->moduleService->getModules();
        foreach ($modules as $module) {
            $data[] = ['id' => $module->id, 'name' => $module->name];
        }

        return $data;
    }

    public function delete(Log $log)
    {
        return $log->delete() === false ? false : true;
    }
}
