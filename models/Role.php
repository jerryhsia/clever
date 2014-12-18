<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

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
    const SUPER_ROLE_ID = 1;

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
}
