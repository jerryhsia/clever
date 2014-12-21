<?php

namespace app\models;

use Yii;
use yii\db\Migration;

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
            'role_ids' => Yii::t('module', 'Role Ids'),
        ];
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
            $sql = Yii::$app->db->queryBuilder->createTable($this->getTableName(), [
                'id' => 'int(11)     UNSIGNED NOT NULL AUTO_INCREMENT',
                'PRIMARY KEY `id`(`id`)'
            ]);
            Yii::$app->db->createCommand($sql)->execute();
            $this->createClassFile();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete ()
    {
        $sql = Yii::$app->db->queryBuilder->dropTable($this->getTableName());
        Yii::$app->db->createCommand($sql)->execute();
        @unlink($this->getClassFile());
        parent::afterDelete();
    }
}
