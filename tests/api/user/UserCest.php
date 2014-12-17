<?php

namespace app\tests\api\user;

use ApiGuy;


class UserCest
{

    public function createUser(ApiGuy $i)
    {
        $user = [
            'name' => 'Jerry',
            'email' => 'xiajie9916@gmail.com',
            'username' => 'Jerry',
            'password' => '123456',
            'role_id' => 1
        ];
        $i->wantTo('Create an user');
        $i->sendPOST('/users', $user);
        $result = $i->grabJsonResponse();
        $i->seeEquals($user['username'], $result['username']);
    }

    public function searchUser(ApiGuy $i)
    {
        $i->wantTo('Search users');
        $i->sendGET('/users?username=Jerry&expand=roles');
        $result = $i->grabJsonResponse();
        $i->seeEquals('Jerry', $result[0]['username']);
    }

    public function login(ApiGuy $i)
    {
        $form = [
            'identity' => 'admin',
            'password' => '123456'
        ];
        $i->wantTo('Login an user');
        $i->sendPOST('/users/login', $form);
        $result = $i->grabJsonResponse();
        $i->seeEquals($form['identity'], $result['username']);
    }

}
