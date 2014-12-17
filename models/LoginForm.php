<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

namespace app\models;

use app\components\App;
use Yii;
use yii\base\Model;

/**
 * Class LoginForm
 *
 * @package app\models
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class LoginForm extends Model
{
    public $identity;
    public $password;
    public $remember = false;

    private $user = false;

    public function rules()
    {
        return [
            [['identity', 'password'], 'required'],
            ['password', 'validateUser'],
        ];
    }

    public function validateUser()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user) {
                $this->addError('identity', Yii::t('user', 'User not found'));
            }
            if ($user && ($user->password != App::createPassword($this->password))) {
                $this->addError('password', Yii::t('user', 'Incorrect password'));
            }
        }
    }

    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->remember ? 7 * 24 * 3600 : 0);
        } else {
            return false;
        }
    }

    public function getUser()
    {
        if ($this->user === false) {
            $this->user = Yii::$container->get('UserService')->loadByIdentity($this->identity);
        }
        return $this->user;
    }
}
