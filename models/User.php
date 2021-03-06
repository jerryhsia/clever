<?php

namespace app\models;

use app\components\Clever;
use Codeception\Command\SelfUpdate;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 */
class User extends ActiveRecord
{
    const SUPER_USER_ID = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'email', 'username'], 'required'],
            ['password', 'required', 'when' => function() {
                return $this->isNewRecord;
            }],
            [['name', 'email'], 'string', 'max' => 50],
            ['username', 'unique', 'when' => function() {
                return !empty($this->username);
            }],
            ['email', 'unique', 'when' => function() {
                return !empty($this->email);
            }]
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->password) {
            $this->password = Clever::createPassword($this->password);
        } else {
            $this->password = $this->getOldAttribute('password');
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', 'ID'),
            'user_id' => Yii::t('user', 'User ID'),
            'name' => Yii::t('user', 'Name'),
            'email' => Yii::t('user', 'Email'),
            'username' => Yii::t('user', 'Username'),
            'password' => Yii::t('user', 'Password'),
            'roles' => Yii::t('user', 'Roles'),
            'role_ids' => Yii::t('user', 'Roles')
        ];
    }
}
