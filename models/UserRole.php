<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class UserRole extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_role}}';
    }
}
