<?php


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
    public $remember = true;

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

    public function getUser()
    {
        if ($this->user === false) {
            $this->user = Yii::$app->userService->getUserByIdentity($this->identity);
        }
        return $this->user;
    }
}
