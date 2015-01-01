<?php


namespace app\components;

use app\models\Data;
use app\models\Module;

class DataService
{
    public function save(Module $module, $model, array $attributes)
    {
        $model->setAttributes($attributes, false);
        $model->save();
        return $model;
    }

    public function search(Module $module, array $filters = [])
    {
        $className = $module->getFullClassName();
        $query = $className::find();
        return $query;
    }

    public function delete(Module $module, $model)
    {
        return $model->delete() === false ? false : true;
    }
}
