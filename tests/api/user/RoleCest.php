<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

namespace app\tests\api\user;

use ApiGuy;

/**
 * Class RoleCest
 *
 * @package app\tests\api\user
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class RoleCest
{

    public function createRole(ApiGuy $i)
    {
        $role = [
            'name' => 'Normal manager'
        ];
        $i->wantTo('Create a role');
        $i->sendPOST('/roles', $role);
        $rs = $i->grabJsonResponse();
        $i->seeEquals($role['name'], $rs['name']);
    }

    public function searchRole(ApiGuy $i)
    {
        $i->wantTo('Search role');

        $params = [
            'id' => 1
        ];
        $i->sendGET('/roles?'.http_build_query($params));
        $rs = $i->grabJsonResponse();
        $i->seeEquals(1, $rs[0]['id']);
    }

    public function updateRole(ApiGuy $i)
    {
        $params = [
            'name'    => 'Normal manager 2',
            '_method' => 'PUT'
        ];
        $i->wantTo('Update a role');
        $i->sendPOST('/roles/2', $params);
        $rs = $i->grabJsonResponse();
        $i->seeEquals($params['name'], $rs['name']);
    }

    public function deleteRole(ApiGuy $i)
    {
        $i->wantTo('Delete role');
        $i->sendPOST('/roles/2', ['_method' => 'DELETE']);
        $i->seeEquals(true, boolval($i->grabResponse()));
    }
}
