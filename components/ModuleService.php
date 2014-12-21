<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

namespace app\components;

use app\models\Field;
use app\models\Module;

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
        return $field->save();
    }

    public function deleteField (Module $module, Field $field)
    {
        return $field->delete() === false ? false : true;
    }
}
