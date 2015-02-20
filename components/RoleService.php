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

        return $isIndexed ? $roles : Clever::removeIndex($roles);
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
}
