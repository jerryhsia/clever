<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "module".
 *
 * @property integer $id
 * @property string $name
 * @property string $title
 * @property string $is_user
 * @property string $to_string
 */
class Module extends ActiveRecord
{
    const DEFAULT_MODULE_ID = 1;

    public static function tableName()
    {
        return '{{%module}}';
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('module', 'ID'),
            'name' => Yii::t('module', 'Name'),
            'title' => Yii::t('module', 'Title'),
            'is_user' => Yii::t('module', 'Is User module'),
            'to_string' => Yii::t('module', 'To String')
        ];
    }

    public function rules()
    {
        return [
            [['name', 'title'], 'required'],
            [['name', 'title'], 'string', 'max' => 50],
            ['name', 'filter', 'filter' => 'strtolower'],
            ['name', 'validateName']
        ];
    }

    public function validateName()
    {
        $this->name = strtolower($this->name);

        if (!$this->hasErrors() &&
            !preg_match('/^[a-z]+$/i', $this->name) &&
            !preg_match('/^[a-z]+[_]{1}[a-z]+$/i', $this->name)
        ) {
            $this->addError('name',
                Yii::t('module', '{attribute} format error',
                    ['attribute' => Yii::t('module', 'Name')])
            );
        }
    }

    public function beforeSave($insert)
    {
        if (!$insert) {
            $fields = ['name', 'is_user'];
            foreach ($fields as $field) {
                $this->setAttribute($field, $this->getOldAttribute($field));
            }
        }
        if (empty($this->to_string)) {
            $this->to_string = '{id}';
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $this->createDefaultFields();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeDelete()
    {
        parent::beforeDelete();
        if ($this->id == self::DEFAULT_MODULE_ID) {
            throw new ForbiddenHttpException(Yii::t('module', 'Default module cannot be deleted'));
        }
        return true;
    }

    public function afterDelete ()
    {
        $sql = Yii::$app->db->queryBuilder->dropTable($this->getTableName());
        Yii::$app->db->createCommand($sql)->execute();
        @unlink($this->getClassFile());
        parent::afterDelete();
    }

    public function getTableName ()
    {
        return Yii::$app->db->tablePrefix.'data_'.$this->name;
    }

    public function getClassName()
    {
        $arr = explode('_', $this->name);
        foreach ($arr as $k => $v) {
            $arr[$k] = ucfirst($v);
        }
        return 'Data'.implode('', $arr);
    }

    public function getFullClassName() {
        return sprintf('app\\models\\%s', $this->getClassName());
    }

    protected function getClassFile ()
    {
        $className = $this->getClassName();
        return Yii::getAlias('@app').DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.$className.'.php';
    }

    protected function createClassFile()
    {
        $tempFile   = Yii::getAlias('@app').DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'class.txt';
        $content = file_get_contents($tempFile);
        $content = str_replace('{class_name}', $this->getClassName(), $content);
        $content = str_replace('{table_name}', $this->getTableName(), $content);
        $fp = fopen($this->getClassFile(), 'w+');
        fwrite($fp, $content);
        fclose($fp);
    }

    protected function createDefaultFields()
    {
        define('CREATE_DEFAULT_FIELDS', true);
        $fields = [
            'id' => 'int(11)     UNSIGNED NOT NULL AUTO_INCREMENT',
            'PRIMARY KEY `id`(`id`)'
        ];
        $sql = Yii::$app->db->queryBuilder->createTable($this->getTableName(), $fields);
        Yii::$app->db->createCommand($sql)->execute();
        $this->createClassFile();

        $field = new Field();
        $field->setAttributes([
            'module_id' => $this->id,
            'is_default' => 1,
            'is_null' => 0,
            'is_list' => 1,
            'name' => 'id',
            'title' => 'ID',
            'input' => Field::INPUT_INPUT,
            'type'  => 'int',
            'size'  => 11
        ], false);
        $field->save();

        if ($this->is_user) {
            $this->createUserFields();
        }
    }

    protected function createUserFields()
    {
        $userFields = [
            [
                'name' => 'user_id',
                'type' => 'int',
                'size' => 11,
                'is_list' => 1
            ],
            [
                'name' => 'name',
                'type' => 'varchar',
                'size' => 50,
                'is_null' => 0,
                'is_list' => 1,
                'is_search' => 1,
            ],
            [
                'name' => 'username',
                'type' => 'varchar',
                'size' => 50,
                'is_list' => 1,
                'is_search' => 1
            ],
            [
                'name' => 'password',
                'type' => 'varchar',
                'size' => 32,
            ],
            [
                'name' => 'email',
                'type' => 'varchar',
                'size' => 50,
                'is_list' => 1,
                'is_search' => 1
            ],
            [
                'name' => 'role_ids',
                'type' => 'varchar',
                'size' => 50,
                'is_list' => 1,
                'is_search' => 1,
                'input' => Field::INPUT_MULTIPLE_SELECT
            ]
        ];
        $userLabels = (new User())->attributeLabels();
        foreach ($userFields as $fieldAttributes) {
            $field = new Field();
            $fieldAttributes = array_merge([
                'module_id' => $this->id,
                'title'     => isset($userLabels[$fieldAttributes['name']]) ? $userLabels[$fieldAttributes['name']] : ucfirst($fieldAttributes['name']),
                'input'     => Field::INPUT_INPUT,
                'is_default'=> 1
            ], $fieldAttributes);
            $field->setAttributes($fieldAttributes, false);
            $field->save();
        }
    }

    private static $_toStringFields = [];

    public function getToStringFields ()
    {
        if (!isset(self::$_toStringFields[$this->id])) {
            preg_match_all('/\{(.*?)\}/i', $this->to_string, $arr);
            self::$_toStringFields[$this->id] = $arr[1];
        }

        return self::$_toStringFields[$this->id];
    }

}
