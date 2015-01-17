<?php


namespace app\components;
use Yii;
use app\models\Role;
use yii\web\ForbiddenHttpException;

/**
 * Class RoleService
 *
 * @package app\components
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class RoleService
{

    const CACHE_ROLES = 'cache_roles';

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

    public function getRole($id)
    {
        $roles = $this->getRoles();
        return isset($roles[$id]) ? $roles[$id] : null;
    }

    private function clearCache()
    {
        Yii::$app->cache->delete(self::CACHE_ROLES);
    }

    /**
     * Save role
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
     * Delete role
     *
     * @param Role $role
     */
    public function delete(Role $role)
    {
        if ($role->is_super) {
            throw new ForbiddenHttpException(Yii::t('role', 'Super role can\'t be deleted'));
        }
        $result = $role->delete() === false ? false : true;
        if ($result) {
            $this->clearCache();
        }
        return $result;
    }
}
