<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */
namespace app\tests\_helpers;

use Codeception\Module\REST;
use yii\helpers\Json;
use PHPUnit_Framework_Assert as Asserts;

require __DIR__ . '/../api/_bootstrap.php';

/**
 * Class ApiHelper
 *
 * @package app\tests\_helpers
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class ApiHelper extends REST
{

    public function grabJsonResponse()
    {
        return Json::decode($this->response, true);
    }

    public function seeEquals($expected, $actual, $message = '')
    {
        Asserts::assertEquals($expected, $actual, $message);
    }

    public function seeArrayHasKey($key, $array, $message = '')
    {
        Asserts::assertArrayHasKey($key, $array, $message);
    }
}
