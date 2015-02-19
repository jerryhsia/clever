<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%app}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $access_token
 */
class App extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%app}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'access_token'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['access_token'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'access_token' => Yii::t('app', 'Access Token'),
        ];
    }

    public function beforeSave($insert)
    {
        $this->access_token = md5(uniqid());
        return parent::beforeSave($insert);
    }
}
