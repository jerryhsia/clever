<?php

namespace app\models;

use Yii;
use yii\db\Migration;

/**
 * This is the model class for table "clever_field".
 *
 * @property integer $id
 * @property integer $module_id
 * @property string $name
 * @property string $title
 * @property string $input
 */
class Field extends \yii\db\ActiveRecord
{
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
            'inupt'     => Yii::t('field', 'Input')
        ];
    }

    public function getModule()
    {
        return $this->hasOne(Module::className(), ['id' => 'module_id']);
    }

    public function getColumnType ()
    {
        return 'varchar(50)';
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $sql = Yii::$app->db->queryBuilder->addColumn($this->module->getTableName(), $this->name, $this->getColumnType());
            Yii::$app->db->createCommand($sql)->execute();
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
