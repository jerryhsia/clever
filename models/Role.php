<?php


namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Role model
 *
 * @property string $id
 * @property string $name
 */
class Role extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%role}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('role', 'ID'),
            'name' => Yii::t('role', 'Name')
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['is_super'] = 'isSuper';

        return $fields;
    }

    public function getIsSuper()
    {
        return in_array($this->id, [1]);
    }
}
