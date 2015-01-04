<?php

namespace app\models;

use app\components\App;
use Yii;
use yii\db\ActiveRecord;
use yii\web\ForbiddenHttpException;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 */
class User extends ActiveRecord implements IdentityInterface
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
            $this->password = App::createPassword($this->password);
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

    /**
     * Finds an identity by the given ID.
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        // TODO: Implement findIdentity() method.
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|integer an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->primaryKey;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->id;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return boolean whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
}
