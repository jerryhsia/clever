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

        if (isset($filter['name'])) {
            $query->andFilterWhere(['name' => $filter['name']]);
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

        $query->orderBy(['sort' => SORT_ASC, 'id' => SORT_ASC]);

        return $query;
    }

    public function saveField (Module $module, Field $field, array $attributes)
    {
        $attributes['module_id'] = $module->id;
        $field->setAttributes($attributes, false);
        return $field->save();
    }

    public function deleteField (Module $module, Field $field)
    {
        return $field->delete() === false ? false : true;
    }
}
