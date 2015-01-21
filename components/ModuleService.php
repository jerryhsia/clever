<?php


namespace app\components;

use Yii;
use app\models\Field;
use app\models\Module;
use yii\db\Migration;
use yii\helpers\ArrayHelper;

/**
 * Class ModuleService
 *
 * @package app\components
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class ModuleService
{
    const CACHE_MODULES = 'cache_modules';
    const CACHE_FIELDS  = 'cache_fields';

    public function saveModule (Module $module, array $attributes)
    {
        $module->setAttributes($attributes, false);
        $result = $module->save();

        if ($result) {
            $this->clearModuleCache();
        }

        return $result;
    }

    public function getModule($idOrName)
    {
        $modules = $this->getModules();

        if (is_numeric($idOrName)) {
            return isset($modules[$idOrName]) ? $modules[$idOrName] : null;
        } else {
            foreach ($modules as $id => $module) {
                if ($module->getAttribute('name') == $idOrName) {
                    return $module;
                }
            }
        }

        return null;
    }

    public function getModules($isIndexed = true)
    {
        $modules = null;

        if (Yii::$app->cache->exists(self::CACHE_MODULES)) {
            $modules = Yii::$app->cache->get(self::CACHE_MODULES);
        } else {
            $modules = Module::find()->indexBy('id')->all();
            Yii::$app->cache->set(self::CACHE_MODULES, $modules);
        }

        return $isIndexed ? $modules : App::removeIndex($modules);
    }

    private function clearModuleCache()
    {
        Yii::$app->cache->delete(self::CACHE_MODULES);
    }

    /**
     * Delete module
     *
     * @param Module $module
     */
    public function deleteModule(Module $module)
    {
        $result = $module->delete() === false ? false : true;
        if ($result) {
            $this->clearModuleCache();
        }
        return $result;
    }

    private function clearFieldCache()
    {
        Yii::$app->cache->delete(self::CACHE_FIELDS);
    }

    public function getField(Module $module, $id)
    {
        $fields = $this->getFields($module);

        return isset($fields[$id]) ? $fields[$id] : null;
    }

    /**
     * Search field
     *
     * @return mixed
     */
    public function getFields(Module $module, $isIndexed = true)
    {
        $fields = null;

        if (Yii::$app->cache->exists(self::CACHE_FIELDS)) {
            $fields = Yii::$app->cache->get(self::CACHE_FIELDS);
        } else {
            $query = Field::find();
            $query->orderBy(['sort' => SORT_ASC, 'id' => SORT_ASC]);
            $result = $query->all();

            $fields = [];
            foreach ($result as $field) {
                $fields[$field->module_id][$field->id] = $field;
            }

            Yii::$app->cache->set(self::CACHE_FIELDS, $fields);
        }

        $result = isset($fields[$module->id]) ? $fields[$module->id] : [];
        return $isIndexed ? $result : App::removeIndex($result);
    }

    public function saveField (Module $module, Field $field, array $attributes)
    {
        $attributes['module_id'] = $module->id;
        $field->setAttributes($attributes, false);

        $result = $field->save();

        if ($result) {
            $this->clearFieldCache();
        }

        return $result;
    }

    public function batchSaveField (Module $module, array $attributes)
    {
        $allowFields = ['sort'];

        $result = 0;
        foreach ($attributes as $attribute) {
            if (!isset($attribute['id']) || !$attribute['id']) {
                continue;
            }
            $id = $attribute['id'];
            unset($attribute['id']);
            $result += Field::updateAll($attribute, ['id' => $id]);
        }

        if ($result > 0) {
            $this->clearFieldCache();
        }

        return $result;
    }

    public function deleteField (Module $module, Field $field)
    {
        $result = $field->delete() === false ? false : true;

        if ($result) {
            $this->clearFieldCache();
        }

        return $result;
    }
}
