<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clever_file_usage".
 *
 * @property integer $file_id
 * @property integer $type
 * @property integer $data_id
 * @property integer $field_id
 */
class FileUsage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file_usage}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_id', 'type', 'data_id'], 'required'],
            [['file_id', 'type', 'data_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file_id' => Yii::t('file', 'File ID'),
            'type' => Yii::t('file', 'Type'),
            'data_id' => Yii::t('file', 'Data ID'),
            'field_id' => Yii::t('file', 'Field ID'),
        ];
    }
}
