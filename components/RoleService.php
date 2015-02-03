<?php


namespace app\components;
use app\models\RolePermission;
use Yii;
use app\models\Role;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

/**
 * Class RoleService
 *
 * @package app\components
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class RoleService extends Component
{

    const CACHE_ROLES = 'cache_roles';

    /**
     * Get multiple roles
     *
     * @param bool $isIndexed
     * @return array|mixed|\yii\db\ActiveRecord[]
     */
    public function getRoles($isIndexed = true)
    {
        $roles = null;

        if (Yii::$app->cache->exists(self::CACHE_ROLES)) {
            $roles = Yii::$app->cache->get(self::CACHE_ROLES);
        } else {
            $roles = Role::find()->indexBy('id')->all();
            Yii::$app->cache->set(self::CACHE_ROLES, $roles);
        }

        return $isIndexed ? $roles : App::removeIndex($roles);
    }

    /**
     * Get a role
     *
     * @param $id
     * @return null|\yii\db\ActiveRecord
     */
    public function getRole($id)
    {
        $roles = $this->getRoles();
        return isset($roles[$id]) ? $roles[$id] : null;
    }

    /**
     * Clear cached role data
     */
    private function clearCache()
    {
        Yii::$app->cache->delete(self::CACHE_ROLES);
    }

    /**
     * Save a role
     *
     * @param Role $role
     * @param array $attributes
     * @return bool
     */
    public function save(Role $role, array $attributes)
    {
        $role->setAttributes($attributes, false);
        $result = $role->save();
        if ($result) {
            $this->clearCache();
        }
        return $result;
    }

    /**
     * Delete a role
     *
     * @param Role $role
     * @return bool
     * @throws ForbiddenHttpException
     * @throws \Exception
     */
    public function delete(Role $role)
    {
        $result = $role->delete() === false ? false : true;
        if ($result) {
            $this->clearCache();
        }
        return $result;
    }

    /**
     * Get Permissions
     *
     * @param Role $role
     * @param array $filters
     * @return array|mixed|\yii\db\ActiveRecord|\yii\db\ActiveRecord[]
     */
    public function getPermissions(Role $role, $moduleId = null)
    {
        $cacheKey = 'permissions';
        $permissions = [];

        if (Yii::$app->cache->exists($cacheKey)) {
            $permissions = Yii::$app->cache->get($cacheKey);
        } else {
            $result = RolePermission::find()->all();
            $temp = [];
            foreach ($result as $row) {
                $temp[$row->role_id][$row->module_id] = $row;
            }
            $permissions = $temp;
            Yii::$app->cache->set($cacheKey, $permissions);
        }

        $result = [];
        if (isset($permissions[$role->id])) {
            $result = $permissions[$role->id];
        }

        if (!$moduleId) {
            return $result;
        }

        if (isset($result[$moduleId])) {
            return $result[$moduleId];
        } else {
            return null;
        }
    }

    /**
     * Save Permission
     *
     * @param Role $role
     * @param array $attributes
     * @return RolePermission
     */
    public function savePermission(Role $role, array $attributes)
    {
        $model = $this->getPermissions($role, ['module_id' => $attributes['module_id']])->one();
        if (!$model) {
            $model = new RolePermission();
        }
        $model->setAttributes($attributes, false);

        $model->save();

        return $model;
    }
}
