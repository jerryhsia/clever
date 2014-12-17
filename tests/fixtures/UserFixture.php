<?php
namespace app\tests\fixtures;
use app\components\App;
use app\models\User;
use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = 'app\models\User';

    public function getData() {
        return [
            [
                'id'  => User::SUPER_USER_ID,
                'name'     => 'Admin',
                'email'    => 'admin@admin.com',
                'username' => 'admin',
                'password' => App::createPassword('123456')
            ]
        ];
    }
}
