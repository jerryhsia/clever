<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

namespace app\components;

use app\models\Field;
use app\models\Module;

class ModuleService
{
    public function saveModule (Module $module, array $attributes)
    {
        $module->setAttributes($attributes);
        return $module->save();
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

        if (isset($filter['id'])) {
            $query->andFilterWhere(['id' => $filter['id']]);

        }

        return $query;
    }

    public function saveField (Module $module, Field $field, array $attributes)
    {
        $attributes['module_id'] = $module->id;
        $field->setAttributes($attributes);
        return $field->save();
    }
}
