<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clever_field".
 *
 * @property integer $id
 * @property integer $module_id
 * @property string $name
 * @property string $title
 */
class Field extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'clever_field';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module_id', 'name', 'title'], 'required'],
            [['module_id'], 'integer'],
            [['name', 'title'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('field', 'ID'),
            'module_id' => Yii::t('field', 'Module ID'),
            'name' => Yii::t('field', 'Name'),
            'title' => Yii::t('field', 'Title'),
        ];
    }
}
