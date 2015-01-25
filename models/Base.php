<?php

namespace app\models;
use app\components\App;
use Yii;
use yii\db\ActiveRecord;
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
        return Yii::$app->moduleService->getModule($name);
    }

    public function getFields()
    {
        return Yii::$app->moduleService->getFields($this->getModule());
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
        $roles = Yii::$app->roleService->getRoles();

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

            $userService = Yii::$app->userService;
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
            $value = $this->getAttribute($field->name);
            if ($field->getIsMultiple()) {
                if (is_array($value)) {
                    $value = implode(',', $value);
                    $this->setAttribute($field->name, $value);
                }
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

        $fileService = Yii::$app->fileService;
        foreach ($this->getFields() as $field) {
            if ($field->input == Field::INPUT_FILE) {
                if (!$insert && isset($changedAttributes[$field['name']])) {
                    $fileService->detach($changedAttributes[$field['name']], $field->module_id, $this->id, $field->id);
                }
                if ($insert || (!$insert && isset($changedAttributes[$field['name']]))) {
                    $fileService->attach($this->getAttribute($field->name), $field->module_id, $this->id, $field->id);
                }
            }

            if ($field->input == Field::INPUT_MULTIPLE_FILE) {
                $old = [];
                $new = [];
                if (!$insert && isset($changedAttributes[$field['name']])) {
                    $old = $changedAttributes[$field['name']] ? explode(',', $changedAttributes[$field['name']]) : [];
                }
                if ($insert || (!$insert && isset($changedAttributes[$field['name']]))) {
                    $new = $this->getAttribute($field->name);
                }

                $remove = array_diff($old, $new);
                $add = array_diff($new, $old);
                foreach ($add as $fileId) {
                    $fileService->attach($fileId, $field->module_id, $this->id, $field->id);
                }
                foreach ($remove as $fileId) {
                    $fileService->detach($fileId, $field->module_id, $this->id, $field->id);
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

        $dataService = Yii::$app->dataService;
        $fileService = Yii::$app->fileService;
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
                $arr[$field->getModelField()] = $this->getAttribute($field->name) ? $fileService->getFiles(['id' => $this->getAttribute($field->name)]) : [];
            }
        }

        return $arr;
    }

    public function getToString()
    {
        $fields = $this->getModule()->getToStringFields();
        $arr = [];
        $data = [];
        foreach ($fields as $field) {
            $arr[$field] = sprintf('{%s}', $field);
            $data[$field] = $this->getAttribute($field);
        }

        return str_replace($arr, $data, $this->getModule()->to_string);
    }
}
