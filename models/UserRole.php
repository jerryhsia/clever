<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Role model
 *
 * @property string $user_id
 * @property string $role_id
 */
class UserRole extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_role}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'role_id'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('role', 'User ID'),
            'role_id' => Yii::t('role', 'Role ID')
        ];
    }
}
