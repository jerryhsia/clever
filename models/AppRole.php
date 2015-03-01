<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clever_app_role".
 *
 * @property integer $app_id
 * @property integer $role_id
 */
class AppRole extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%app_role}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'role_id'], 'required'],
            [['app_id', 'role_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'app_id' => Yii::t('app', 'App ID'),
            'role_id' => Yii::t('app', 'Role ID'),
        ];
    }
}
