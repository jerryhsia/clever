<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "module".
 *
 * @property integer $id
 * @property string $name
 * @property string $title
 * @property array $role_ids
 */
class Module extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%module}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'title', 'role_ids'], 'required'],
            [['name', 'title'], 'string', 'max' => 50],
            [['role_ids'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('module', 'ID'),
            'name' => Yii::t('module', 'Name'),
            'title' => Yii::t('module', 'Title'),
            'role_ids' => Yii::t('module', 'Role Ids'),
        ];
    }
}
