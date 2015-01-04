<?php

namespace app\models;
use app\models\User;
use yii\db\ActiveRecord;

abstract class Base extends ActiveRecord
{
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function isUser()
    {
        return $this->hasAttribute('user_id') && $this->hasAttribute('username');
    }

    public function beforeSave($insert)
    {
        if ($this->isUser()) {
            if (is_array($this->role_ids)) {
                $this->role_ids = implode(',', $this->role_ids);
            }
            $this->role_ids = $this->role_ids ? $this->role_ids : '';
        }
        return parent::beforeSave($insert);
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
}
