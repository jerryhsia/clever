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
    public function fields()
    {
        $fields = parent::fields();

        $fields['to_string'] = 'toString';

        return $fields;
    }

    public function extraFields()
    {
        $fields = parent::extraFields();

        if ($this->isUser()) {
            $fields['roles'] = 'roles';
        }

        return $fields;
    }

    public function getModule()
    {
        $name = end(explode("\\Data", $this->className()));
        $name = strtolower(preg_replace('/((?<=[A-Z])(?=[A-Z]))/', '_', $name));
        return Yii::$container->get('ModuleService')->getModule($name);
    }

    public function getFields()
    {
        return Yii::$container->get('ModuleService')->getFields($this->getModule());
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function isUser()
    {
        return ($this->hasAttribute('user_id') && $this->hasAttribute('username'));
    }

    public function getRoles()
    {
        $roles = Yii::$container->get('RoleService')->getRoles();

        $result = [];
        foreach ($this->role_ids as $roleId) {
            if (isset($roles[$roleId])) {
                $result[] = $roles[$roleId];
            }
        }
        return $result;
    }

    public function beforeSave($insert)
    {
        if ($this->isUser()) {

            $userService = Yii::$container->get('UserService');
            $userAttributes = $this->getAttributes();
            $userAttributes['module_id'] = $this->module->id;
            $userAttributes['data_id'] = 0;
            unset($userAttributes['user_id'], $userAttributes['id']);

            $user = null;
            if ($insert) {
                $user = new User();
            } else {
                $user = $userService->getUser($this->user_id);
            }

            if ($userService->save($user, $userAttributes)) {
                $this->user_id = $user->id;
                $this->password = $user->password;

                UserRole::deleteAll(['user_id' => $user->id]);
                foreach ($this->role_ids as $roleId) {
                    $userRole = new UserRole();
                    $userRole->setAttributes(['user_id' => $user->id, 'role_id' => $roleId], false);
                    $userRole->save();
                }
            } else {
                App::copyErrors($user, $this);
                return false;
            }
        }

        foreach ($this->getFields() as $field) {
            if ($field->getIsMultiple()) {
                $this->setAttribute($field->name, implode(',', $this->getAttribute($field->name)));
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

        foreach ($this->getFields() as $field) {
            if ($field->input == Field::INPUT_FILE) {
                $fileService = Yii::$container->get('FileService');
                if (!$insert && isset($changedAttributes[$field['name']])) {
                    $fileService->detach($changedAttributes[$field['name']], $field->module_id, $this->id, $field->id);
                }
                if ($insert || (!$insert && isset($changedAttributes[$field['name']]))) {
                    $fileService->attach($this->getAttribute($field->name), $field->module_id, $this->id, $field->id);
                }
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function afterFind()
    {
        foreach ($this->getFields() as $field) {
            if ($field->getIsMultiple()) {
                $arr = $this->getAttribute($field->name) ? explode(',', $this->getAttribute($field->name)) : [];
                $this->setAttribute($field->name, $arr);
            }
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

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $arr = parent::toArray($fields, $expand, $recursive);

        if ($this->isUser()) {
            $arr['role_ids_models'] = $this->roles;
        }

        $dataService = Yii::$container->get('DataService');
        $fileService = Yii::$container->get('FileService');
        foreach ($this->getFields() as $field) {
            if ($field->relation_id) {
                $query = $dataService->search($field->relationModule, ['id' => $this->getAttribute($field->name)]);
                if ($field->relation_type == Field::RELATION_HAS_ONE) {
                    $arr[$field->getModelField()] = $query->one();
                } else if ($field->relation_type == Field::RELATION_HAS_MANY) {
                    $arr[$field->getModelField()] = $query->all();
                }
            }

            if ($field->input == Field::INPUT_FILE) {
                $arr[$field->getModelField()] = $fileService->getFile($this->getAttribute($field->name));
            }

            if ($field->input == Field::INPUT_MULTIPLE_FILE) {
                $arr[$field->getModelField()] = $fileService->getFiles(['id' => $this->getAttribute($field->name)]);
            }
        }

        return $arr;
    }

    public function getToString()
    {
        return $this->name;
    }
}
