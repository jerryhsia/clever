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
 * @property string $input
 * @property string $type
 * @property string $size
 * @property string $is_null
 */
class Field extends \yii\db\ActiveRecord
{

    const INPUT_INPUT = 'input';
    const INPUT_TEXTAREA = 'textarea';
    const INPUT_RADIO = 'radio';
    const INPUT_CHECKBOX = 'checkbox';
    const INPUT_SELECT = 'select';
    const INPUT_DATE = 'date';
    const INPUT_FILE = 'file';
    const INPUT_MULTIPLE_FILE = 'multiple_file';

    const DEFAULT_FIELD = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%field}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module_id', 'name', 'title', 'input'], 'required'],
            [['module_id'], 'integer'],
            ['size', 'filter', 'filter' => function() {
                return $this->size ? $this->size : 200;
            }],
            ['type', 'filter', 'filter' => function() {
                return $this->type ? $this->type : self::INPUT_INPUT;
            }],
            [['name', 'title', 'input'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'        => Yii::t('field', 'ID'),
            'module_id' => Yii::t('field', 'Module ID'),
            'name'      => Yii::t('field', 'Name'),
            'title'     => Yii::t('field', 'Title'),
            'inupt'     => Yii::t('field', 'Input'),
            'type'      => Yii::t('field', 'Type'),
            'size'      => Yii::t('field', 'Size')
        ];
    }

    public function getModule()
    {
        return $this->hasOne(Module::className(), ['id' => 'module_id']);
    }

    public function beforeSave($insert)
    {
        if (!$insert) {
            $fields = ['name', 'type', 'input'];
            foreach ($fields as $field) {
                $this->setAttribute($field, $this->getOldAttribute($field));
            }
        }
        return parent::beforeSave($insert);
    }

    public function getColumnString ()
    {
        return sprintf('%s(%d) %s', $this->type, $this->size, $this->is_null ? 'NULL' : 'NOT NULL');
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            if ($this->name != 'id') {
                $sql = Yii::$app->db->queryBuilder->addColumn($this->module->getTableName(), $this->name, $this->getColumnString());
                Yii::$app->db->createCommand($sql)->execute();
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        $sql = Yii::$app->db->queryBuilder->dropColumn($this->module->getTableName(), $this->name);
        Yii::$app->db->createCommand($sql)->execute();
        parent::afterDelete();
    }
}
