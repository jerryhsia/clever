<?php

namespace app\models;

use Yii;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "clever_field".
 *
 * @property integer $id
 * @property integer $module_id
 * @property boolean $is_default
 * @property boolean $is_null
 * @property boolean $is_list
 * @property boolean $is_search
 * @property string $name
 * @property string $title
 * @property string $input
 * @property string $type
 * @property integer $size
 * @property integer $relation_id
 * @property string $relation_type
 * @property array $option
 * @property integer $sort
 */
class Field extends \yii\db\ActiveRecord
{

    const INPUT_INPUT = 'input';
    const INPUT_TEXTAREA = 'textarea';
    const INPUT_RADIO = 'radio';
    const INPUT_CHECKBOX = 'checkbox';
    const INPUT_SELECT = 'select';
    const INPUT_MULTIPLE_SELECT = 'multiple_select';
    const INPUT_DATE = 'date';
    const INPUT_MULTIPLE_FILE = 'files';

    const RELATION_HAS_ONE = 'has_one';
    const RELATION_HAS_MANY = 'has_many';

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
            [['name', 'title', 'input'], 'string', 'max' => 50],
            ['name', 'validateName'],
            ['name', 'validateInput'],
            ['type', 'validateType'],
            ['name', 'unique', 'when' => function() {
                return $this->name && !$this->hasErrors();
            }, 'filter' => 'module_id = '.$this->module_id]
        ];
    }

    public function validateName()
    {
        if ($this->hasErrors()) return;

        if (!preg_match('/^[a-z]+$/i', $this->name) && !preg_match('/^[a-z]+[_]{1}[a-z]+$/i', $this->name)) {
            $message = Yii::t('field', '{attribute} format error', ['attribute' => $this->attributeLabels()['name']]);
            $this->addError('name', $message);
        }
    }

    public function validateInput()
    {
        if ($this->hasErrors()) return;

        if ($this->relation_id) {
            if (!$this->relation_type) {
                $message = Yii::t('field', '{attribute} required', ['attribute' => $this->attributeLabels()['relation_type']]);
                $this->addError('relation_type', $message);
            }
            switch ($this->relation_type) {
                case self::RELATION_HAS_ONE:
                    $this->input = self::INPUT_SELECT;
                    break;
                case self::RELATION_HAS_MANY:
                    $this->input = self::INPUT_MULTIPLE_SELECT;
                    break;
            }
        } else {
            $inputs = [
                self::INPUT_SELECT,
                self::INPUT_MULTIPLE_SELECT,
                self::INPUT_RADIO,
                self::INPUT_CHECKBOX
            ];
            if (in_array($this->input, $inputs) && empty($this->option)) {
                $message = Yii::t('field', '{attribute} required', ['attribute' => $this->attributeLabels()['option']]);
                $this->addError('option', $message);
            }
        }
    }

    public function validateType()
    {
        if ($this->hasErrors()) return;

        if (false && $this->relation_id) {
            if ($this->relation_type == self::RELATION_HAS_ONE) {
                $this->type = 'int';
                $this->size = '11';
            }
        } else {
            $this->type = 'varchar';
            $this->size = 200;
        }
    }

    public function fields()
    {
        return parent::fields() + [
            'can_edit' => 'canEdit',
            'is_user_field' => 'isUserField',
            'has_relation' => 'hasRelation',
            'module' => 'module',
            'relation_module' => 'relationModule'
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'        => Yii::t('field', 'ID'),
            'module_id' => Yii::t('field', 'Module ID'),
            'name'      => Yii::t('field', 'Name'),
            'title'     => Yii::t('field', 'Title'),
            'inupt'     => Yii::t('field', 'Input'),
            'type'      => Yii::t('field', 'Type'),
            'size'      => Yii::t('field', 'Size'),
            'relation_type' => Yii::t('field', 'Relation Type'),
            'option'    => Yii::t('field', 'Option')
        ];
    }

    public function getModule()
    {
        return Yii::$container->get('ModuleService')->getModule($this->module_id);
    }

    public function getRelationModule()
    {
        return Yii::$container->get('ModuleService')->getModule($this->relation_id);
    }

    public function beforeSave($insert)
    {
        if (!$insert) {
            $fields = ['name', 'type', 'input', 'relation_id', 'relation_type'];
            foreach ($fields as $field) {
                $this->setAttribute($field, $this->getOldAttribute($field));
            }
        }

        if (is_array($this->option)) {
            $this->option = implode(',', $this->option);
        } else {
            $this->option = str_replace('，', ',', $this->option);
        }

        return parent::beforeSave($insert);
    }

    public function getColumnString ()
    {
        return sprintf('%s(%d) %s', $this->type, $this->size, $this->is_null ? 'NULL' : "NOT NULL DEFAULT ''");
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

    public function afterFind()
    {
        if ($this->option) {
            $this->option = explode(',', $this->option);
        }
    }

    public function beforeDelete()
    {
        parent::beforeDelete();

        if ($this->is_default) {
            throw new ForbiddenHttpException(Yii::t('field', 'Default field cannot be deleted'));
        }
        return true;
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

    public function getIsUserField()
    {
        return in_array($this->name, ['name', 'username', 'password', 'email', 'role_ids']);
    }

    public function getHasRelation()
    {
        if ($this->module->is_user && in_array($this->name, ['role_ids'])) {
            return true;
        }
        return $this->relation_id > 0;
    }
}
