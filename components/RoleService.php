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
    private static $_roles = false;

    public function getAll()
    {
        if (self::$_roles === false) {
            self::$_roles = Role::find()->all();
        }
        return self::$_roles;
    }

    public function findById($id)
    {
        foreach ($this->getAll() as $role) {
            if ($role->id == $id) {
                return $role;
            }
        }
        return null;
    }

    private function clearCache()
    {
        self::$_roles = false;
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
