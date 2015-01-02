<?php

namespace app\models;

use Yii;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "module".
 *
 * @property integer $id
 * @property string $name
 * @property string $title
 * @property string $is_user
 */
class Module extends \yii\db\ActiveRecord
{
    const DEFAULT_MODULE_ID = 1;

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
            [['name', 'title'], 'required'],
            [['name', 'title'], 'string', 'max' => 50],
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
            'is_user' => Yii::t('module', 'User module'),
        ];
    }

    public function beforeSave($insert)
    {
        if (!$insert) {
            $fields = ['name', 'is_user'];
            foreach ($fields as $field) {
                $this->setAttribute($field, $this->getOldAttribute($field));
            }
        }
        return parent::beforeSave($insert);
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

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
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
        parent::afterSave($insert, $changedAttributes);
    }

    protected function createUserFields()
    {
        $userFields = [
            [
                'name' => 'user_id',
                'type' => 'int',
                'size' => 11,
                'is_null' => 0,
            ],
            [
                'name' => 'name',
                'type' => 'varchar',
                'size' => 50,
                'is_null' => 0
            ],
            [
                'name' => 'username',
                'type' => 'varchar',
                'size' => 50,
                'is_null' => 0
            ],
            [
                'name' => 'password',
                'type' => 'varchar',
                'size' => 32,
                'is_null' => 0
            ],
            [
                'name' => 'email',
                'type' => 'varchar',
                'size' => 50,
                'is_null' => 0
            ]
        ];
        $userLabels = (new User())->attributeLabels();
        foreach ($userFields as $fieldAttributes) {
            $field = new Field();
            $fieldAttributes += [
                'module_id' => $this->id,
                'title'     => isset($userLabels[$fieldAttributes['name']]) ? $userLabels[$fieldAttributes['name']] : ucfirst($fieldAttributes['name']),
                'input'     => Field::INPUT_INPUT,
                'is_default'=> Field::DEFAULT_FIELD
            ];
            $field->setAttributes($fieldAttributes, false);
            $field->save();
        }
    }

    public function beforeDelete()
    {
        if ($this->id == self::DEFAULT_MODULE_ID) {
            throw new ForbiddenHttpException(Yii::t('module', 'Default module cannot be deleted'));
        }
    }

    public function afterDelete ()
    {
        $sql = Yii::$app->db->queryBuilder->dropTable($this->getTableName());
        Yii::$app->db->createCommand($sql)->execute();
        @unlink($this->getClassFile());
        parent::afterDelete();
    }
}
