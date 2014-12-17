<?php
namespace app\tests\fixtures;
use app\models\Role;
use app\models\User;
use yii\test\ActiveFixture;

class UserRoleFixture extends ActiveFixture
{
    public $modelClass = 'app\models\UserRole';

    public function getData() {
        return [
            [
                'user_id' => User::SUPER_USER_ID,
                'role_id' => Role::SUPER_ROLE_ID
            ]
        ];
    }
}
