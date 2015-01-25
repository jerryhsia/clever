<?php

namespace app\components;

use Yii;
use app\models\Setting;
use yii\base\Component;
use yii\helpers\ArrayHelper;

class SettingService extends Component
{
    private static $_cacheKey = 'settings';

    /**
     * Get all settings
     *
     * @return array|mixed|null
     */
    public function getAll() {
        $cache = Yii::$app->cache;
        $settings = null;
        if ($cache->exists(self::$_cacheKey)) {
            $settings = $cache->get(self::$_cacheKey);
        } else {
            $all = Setting::find()->all();
            $all = ArrayHelper::index($all, 'name');

            $rs = [];
            foreach ($all as $setting) {
                $rs[$setting->name] = $setting->value;
            }
            $settings = $rs;
            $cache->set(self::$_cacheKey, $rs);
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
     * Set a setting
     *
     * @param $name
     * @param null $value
     * @return Setting|array|null|\yii\db\ActiveRecord
     */
    public function set($name, $value = null)
    {
        $setting = Setting::find()->where(['name' => $name])->one();
        if (!$setting) {
            $setting = new Setting();
        }
        $setting->setAttributes(['name' => $name, 'value' => $value], false);
        if ($setting->save()) {
            $this->clearCache();
        }
        return $setting;
    }

    /**
     * Delete a setting
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
     * Clear setting data
     */
    private function clearCache()
    {
        Yii::$app->cache->delete(self::$_cacheKey);
    }
}
