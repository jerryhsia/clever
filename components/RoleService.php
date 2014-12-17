<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

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
        return $role->save() === false ? false : true;
    }

    /**
     * Search role
     *
     * @return mixed
     */
    public function search(array $filter)
    {
        $query = Role::find()->andFilterWhere($filter);
        return $query;
    }

    /**
     * Delete role
     *
     * @param Role $role
     */
    public function delete(Role $role)
    {
        if ($role->id == Role::SUPER_ROLE_ID) {
            throw new ForbiddenHttpException(Yii::t('role', 'Super role can\'t be deleted'));
        }
        return $role->delete() === false ? false : true;
    }
}
