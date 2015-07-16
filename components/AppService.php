<?php


namespace app\components;

use Yii;
use app\models\App;
use yii\base\Component;

/**
 * Class AppService
 *
 * @package app\components
 * @author Jerry Hsia<jerry9916@qq.com>
 */
class AppService extends Component
{
    const CACHE_APPS = 'cache_apps';

    /**
     * Save a app
     *
     * @param App $app
     * @param array $attributes
     * @return bool
     */
    public function saveApp (App $app, array $attributes)
    {
        $app->setAttributes($attributes, false);
        $app->tempRoleIds = isset($attributes['role_ids']) ? $attributes['role_ids'] : [];

        $result = $app->save();

        if ($result) {
            $this->clearAppCache();
        }

        return $result;
    }

    /**
     * Get a app by id or name
     *
     * @param $idOrName
     * @return null|\yii\db\ActiveRecord
     */
    public function getApp($idOrName)
    {
        $apps = $this->getApps();

        if (is_numeric($idOrName)) {
            return isset($apps[$idOrName]) ? $apps[$idOrName] : null;
        } else {
            foreach ($apps as $id => $app) {
                if ($app->getAttribute('name') == $idOrName) {
                    return $app;
                }
            }
        }

        return null;
    }

    /**
     * Get multiple apps
     *
     * @param bool $isIndexed whether indexed the result by id
     * @return array|mixed|\yii\db\ActiveRecord[]
     */
    public function getApps($isIndexed = true)
    {
        $apps = null;

        if (Yii::$app->cache->exists(self::CACHE_APPS)) {
            $apps = Yii::$app->cache->get(self::CACHE_APPS);
        } else {
            $apps = App::find()->indexBy('id')->all();
            Yii::$app->cache->set(self::CACHE_APPS, $apps);
        }

        return $isIndexed ? $apps : Clever::removeIndex($apps);
    }

    /**
     * Clear cached app data
     */
    private function clearAppCache()
    {
        Yii::$app->cache->delete(self::CACHE_APPS);
    }

    /**
     * Delete a app
     *
     * @param App $app
     * @return bool
     * @throws \Exception
     */
    public function deleteApp(App $app)
    {
        $result = $app->delete() === false ? false : true;
        if ($result) {
            $this->clearAppCache();
        }
        return $result;
    }
}
