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


    public function getTable ()
    {
        return Yii::$app->db->tablePrefix.'data_'.$this->name;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $sql = Yii::$app->db->queryBuilder->createTable($this->getTable(), [
                'id' => 'int(11)     UNSIGNED NOT NULL AUTO_INCREMENT',
                'PRIMARY KEY `id`(`id`)'
            ]);
            Yii::$app->db->createCommand($sql)->execute();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete ()
    {
        $sql = Yii::$app->db->queryBuilder->dropTable($this->getTable());
        Yii::$app->db->createCommand($sql)->execute();
        parent::afterDelete();
    }
}
