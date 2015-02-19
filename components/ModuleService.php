<?php


namespace app\components;

use Yii;
use app\models\Field;
use app\models\Module;
use yii\base\Component;

/**
 * Class ModuleService
 *
 * @package app\components
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class ModuleService extends Component
{
    const CACHE_MODULES = 'cache_modules';
    const CACHE_FIELDS  = 'cache_fields';

    /**
     * Save a module
     *
     * @param Module $module
     * @param array $attributes
     * @return bool
     */
    public function saveModule (Module $module, array $attributes)
    {
        $module->setAttributes($attributes, false);
        $result = $module->save();

        if ($result) {
            $this->clearModuleCache();
        }

        return $result;
    }

    /**
     * Get a module by id or name
     *
     * @param $idOrName
     * @return null|\yii\db\ActiveRecord
     */
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

    /**
     * Get multiple modules
     *
     * @param bool $isIndexed whether indexed the result by id
     * @return array|mixed|\yii\db\ActiveRecord[]
     */
    public function getModules($isIndexed = true)
    {
        $modules = null;

        if (Yii::$app->cache->exists(self::CACHE_MODULES)) {
            $modules = Yii::$app->cache->get(self::CACHE_MODULES);
        } else {
            $modules = Module::find()->indexBy('id')->all();
            Yii::$app->cache->set(self::CACHE_MODULES, $modules);
        }

        return $isIndexed ? $modules : Clever::removeIndex($modules);
    }

    /**
     * Clear cached module data
     */
    private function clearModuleCache()
    {
        Yii::$app->cache->delete(self::CACHE_MODULES);
    }

    /**
     * Delete a module
     *
     * @param Module $module
     * @return bool
     * @throws \Exception
     */
    public function deleteModule(Module $module)
    {
        $result = $module->delete() === false ? false : true;
        if ($result) {
            $this->clearModuleCache();
        }
        return $result;
    }

    /**
     * Get a field
     *
     * @param Module $module
     * @param $id
     * @return null
     */
    public function getField(Module $module, $id)
    {
        $fields = $this->getFields($module);

        return isset($fields[$id]) ? $fields[$id] : null;
    }

    /**
     * Get multiple fields
     *
     * @param Module $module
     * @param bool $isIndexed
     * @return Field[]
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
        return $isIndexed ? $result : Clever::removeIndex($result);
    }

    /**
     * Save a field
     *
     * @param Module $module
     * @param Field $field
     * @param array $attributes
     * @return bool|null
     * @throws \Exception
     */
    public function saveField (Module $module, Field $field, array $attributes)
    {
        $attributes['module_id'] = $module->id;
        $field->setAttributes($attributes, false);

        $result = null;
        if ($field->isNewRecord) {
            try {
                $result = $field->save();
            } catch (\Exception $e) {
                $field->delete();
                throw $e;
            }
        } else {
            $result = $field->save();
        }

        if ($result) {
            $this->clearFieldCache();
        }

        return $result;
    }

    /**
     * Batch save fields
     *
     * @param Module $module
     * @param array $array
     * @return int
     */
    public function saveFields (Module $module, array $array)
    {
        $allowFields = ['sort'];

        $result = 0;
        foreach ($array as $attributes) {
            if (!isset($attributes['id']) || !$attributes['id']) {
                continue;
            }

            $id = $attributes['id'];
            unset($attributes['id']);
            foreach ($attributes as $key => $value) {
                if (!in_array($key, $allowFields)) {
                    unset($attributes[$key]);
                }
            }

            if ($attributes) {
                $result += Field::updateAll($attributes, ['id' => $id, 'module_id' => $module->id]);
            }
        }

        if ($result > 0) {
            $this->clearFieldCache();
        }

        return $result;
    }

    /**
     * Delete a field
     *
     * @param Module $module
     * @param Field $field
     * @return bool
     * @throws \Exception
     */
    public function deleteField (Module $module, Field $field)
    {
        $result = $field->delete() === false ? false : true;

        if ($result) {
            $this->clearFieldCache();
        }

        return $result;
    }

    /**
     * Clear cached field data
     */
    private function clearFieldCache()
    {
        Yii::$app->cache->delete(self::CACHE_FIELDS);
    }
}
