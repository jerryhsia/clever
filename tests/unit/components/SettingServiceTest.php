<?php


namespace app\tests\unit\components;
use Codeception\TestCase\Test;
use Yii;

/**
 * Class SettingServiceTest
 *
 * @package app\tests\unit\components
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class SettingServiceTest extends Test
{
    public function testSettingService()
    {
        $settingService = Yii::$container->get('SettingService');

        $settings = [
            'name'   => 'Jerry',
            'age'    => 24.5,
            'weight' => 69.5,
            'like'   => ['read', 'game', 'tour'],
            'is_rich'=> false
        ];

        foreach ($settings as $name => $value) {
            $settingService->set($name, $value);
            $this->assertEquals($value, $settingService->get($name));
        }
    }
}

