<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clever_field".
 *
 * @property integer $id
 * @property integer $module_id
 * @property string $is_default
 * @property string $is_null
 * @property string $is_list
 * @property string $is_search
 * @property string $name
 * @property string $title
 * @property string $input
 * @property string $type
 * @property string $size

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
            ['input', 'filter', 'filter' => function() {
                return $this->input ? $this->input : self::INPUT_INPUT;
            }],
            ['type', 'filter', 'filter' => function() {
                return $this->type ? $this->type : 'varchar';
            }],
            [['name', 'title', 'input'], 'string', 'max' => 50],
            ['name', 'validateName'],
            ['name', 'unique', 'when' => function() {
                return $this->name && !$this->hasErrors();
            }]
        ];
    }

    public function validateName()
    {
        if (!$this->hasErrors() &&
            !preg_match('/^[a-z]+$/i', $this->name) &&
            !preg_match('/^[a-z]+[_]{1}[a-z]+$/i', $this->name)
        ) {
            $this->addError('name',
                Yii::t('field', '{attribute} format error',
                    ['attribute' => Yii::t('field', 'Name')])
            );
        }
    }

    public function fields()
    {
        return parent::fields() + [
            'can_edit' => 'canEdit',
            'can_delete' => 'canDelete',
            'is_user_field' => 'isUserField'
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
        return sprintf('%s(%d) %s', $this->type, $this->size, $this->is_null ? 'NOT NULL' : 'NOT NULL');
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            if ($this->name != 'id') {
                $sql = Yii::$app->db->queryBuilder->addColumn($this->module->getTableName(), $this->name, $this->getColumnString());
                Yii::$app->db->createCommand($sql)->execute();
            }
        } else {
            if (isset($changedAttributes['type']) || isset($changedAttributes['size'])) {
                $sql = Yii::$app->db->queryBuilder->alterColumn($this->module->getTableName(), $this->name, $this->getColumnString());
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

    public function getCanEdit()
    {
        $fields = ['id'];
        if ($this->module->is_user) {
            $fields = array_merge($fields, ['user_id']);
        }
        return !in_array($this->name, $fields);
    }

    public function getCanDelete()
    {
        if ($this->is_default) {
            return false;
        }
        $fields = ['id'];
        if ($this->module->is_user) {
            $fields = array_merge($fields, ['user_id', 'name', 'username', 'password', 'email']);
        }
        return !in_array($this->name, $fields);
    }

    public function getIsUserField()
    {
        return in_array($this->name, ['name', 'username', 'password', 'email']);
    }
}
