<?php


namespace app\components;

use Yii;
use app\models\Field;
use app\models\Module;
use yii\db\Migration;

/**
 * Class ModuleService
 *
 * @package app\components
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class ModuleService
{
    public function saveModule (Module $module, array $attributes)
    {
        $module->setAttributes($attributes, false);
        $result = $module->save();
        return $result;
    }

    /**
     * Search module
     *
     * @return mixed
     */
    public function searchModule(array $filter)
    {
        $query = Module::find();

        if (isset($filter['id'])) {
            $query->andFilterWhere(['id' => $filter['id']]);

        }

        return $query;
    }

    /**
     * Delete module
     *
     * @param Module $module
     */
    public function deleteModule(Module $module)
    {
        return $module->delete() === false ? false : true;
    }

    /**
     * Search field
     *
     * @return mixed
     */
    public function searchField(Module $module, array $filter)
    {
        $query = Field::find();

        $query->andFilterWhere(['module_id' => $module->id]);

        if (isset($filter['id'])) {
            $query->andFilterWhere(['id' => $filter['id']]);

        }

        return $query;
    }

    public function saveField (Module $module, Field $field, array $attributes)
    {
        $attributes['module_id'] = $module->id;
        $field->setAttributes($attributes);
        $result = $field->save();
        if ($result && $field->isNewRecord) {
            $migrate = new Migration();
            $tables = Yii::$app->db->getSchema()->getTableSchemas();
            if (!in_array($module->getTable(), $tables)) {
               // $migrate->createTable($module->getTable());
            }
        }
    }

    public function deleteField (Module $module, Field $field)
    {
        return $field->delete() === false ? false : true;
    }
}
