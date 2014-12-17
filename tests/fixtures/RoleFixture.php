<?php
namespace app\tests\fixtures;
use app\models\Role;
use yii\test\ActiveFixture;

class RoleFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Role';

    public function getData() {
        return [
            [
                'id' => Role::SUPER_ROLE_ID,
                'name' => 'Super role'
            ]
        ];
    }
}
