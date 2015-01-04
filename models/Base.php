<?php

namespace app\models;
use app\components\App;
use Yii;
use app\models\User;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

abstract class Base extends ActiveRecord
{
    public $module = null;

    public function fields()
    {
        $fields = parent::fields();

        if ($this->isUser()) {
            $fields['roles'] = 'roles';
        }

        return $fields;
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function isUser()
    {
        return ($this->hasAttribute('user_id') && $this->hasAttribute('username'));
    }

    private static $_roleMap = false;

    public function getRoles()
    {
        if (self::$_roleMap === false) {
            self::$_roleMap = ArrayHelper::index(Yii::$container->get('RoleService')->getAll(), 'id');
        }
        $result = [];
        foreach ($this->role_ids as $roleId) {
            if (isset(self::$_roleMap[$roleId])) {
                $result[] = self::$_roleMap[$roleId];
            }
        }
        return $result;
    }

    public function beforeSave($insert)
    {
        if ($this->isUser()) {
            if (is_array($this->role_ids)) {
                $this->role_ids = implode(',', $this->role_ids);
            }
            $this->role_ids = $this->role_ids ? $this->role_ids : '';

            $userService = Yii::$container->get('UserService');
            $userAttributes = $this->getAttributes();
            $userAttributes['module_id'] = $this->module->id;
            $userAttributes['data_id'] = 0;
            unset($userAttributes['user_id'], $userAttributes['id']);

            $user = null;
            if ($insert) {
                $user = new User();
            } else {
                $user = $userService->findById($this->user_id);
            }

            if ($userService->save($user, $userAttributes)) {
                $this->user_id = $user->id;
                $this->password = $user->password;

                UserRole::deleteAll(['user_id' => $user->id]);
                foreach (explode(',', $this->role_ids) as $roleId) {
                    $userRole = new UserRole();
                    $userRole->setAttributes(['user_id' => $user->id, 'role_id' => $roleId], false);
                    $userRole->save();
                }
            } else {
                App::copyErrors($user, $this);
                return false;
            }
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->refresh();
        if ($this->isUser()) {
            if ($insert) {
                User::updateAll(['data_id' => $this->id], ['id' => $this->user_id]);
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterFind()
    {
        if ($this->isUser()) {
            $roleIds = [];
            if ($this->role_ids) {
                $roleIds = explode(',', $this->role_ids);
            }
            foreach ($roleIds as $key => $value) {
                $roleIds[$key] = intval($value);
            }
            $this->role_ids = $roleIds;
        }
    }

    public function beforeDelete()
    {
        if ($this->isUser() && in_array($this->user_id, [1])) {
            throw new ForbiddenHttpException(Yii::t('user', 'Super user cannot be deleted'));
        }
        return true;
    }

    public function afterDelete()
    {
        if ($this->isUser()) {
            User::deleteAll(['id' => $this->user_id]);
            UserRole::deleteAll(['user_id' => $this->user_id]);
        }
    }
}
