<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

namespace app\components;

use Yii;
use app\models\Setting;
use yii\helpers\ArrayHelper;

class SettingService
{

    private $cacheKey = 'settings';

    /**
     * Get all settings
     *
     * @return mixed|null
     */
    public function getAll() {
        $cache = Yii::$app->cache;
        $settings = null;
        if ($cache->exists($this->cacheKey)) {
            $settings = $cache->get($this->cacheKey);
        } else {
            $all = Setting::find()->all();
            $all = ArrayHelper::index($all, 'name');

            $rs = [];
            foreach ($all as $setting) {
                $rs[$setting->name] = $setting->value;
            }
            $settings = $rs;
            $cache->set($this->cacheKey, $rs);
        }
        return $settings;
    }

    /**
     * Get setting by name
     *
     * @param $name
     * @param null $default
     * @return null
     */
    public function get($name, $default = null)
    {
        $all = $this->getAll();
        return isset($all[$name]) ? $all[$name] : $default;
    }

    /**
     * Set setting
     *
     * @param $name
     * @param null $value
     * @return Setting
     */
    public function set($name, $value = null)
    {
        $setting = Setting::find()->where(['name' => $name])->one();
        if (!$setting) {
            $setting = new Setting();
        }
        $setting->setAttributes(['name' => $name, 'value' => $value], false);
        if ($setting->save() !== false) {
            $this->clearCache();
        }
        return $setting;
    }

    /**
     * Delete setting by name
     *
     * @param $name
     */
    public function delete($name)
    {
        if (Setting::deleteAll(['name' => $name])) {
            $this->clearCache();
        }
    }

    /**
     * Clear settings cache
     */
    private function clearCache()
    {
        Yii::$app->cache->delete($this->cacheKey);
    }
}
