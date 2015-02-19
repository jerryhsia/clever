<?php

namespace app\components;
use app\models\App;
use Yii;
use yii\base\Component;
use yii\web\IdentityInterface;

class WebUser extends Component implements IdentityInterface
{
    public $type;

    public $id;

    public $accessToken;

    public $duration;

    public $model;

    const TYPE_USER = 'user';

    const TYPE_APP = 'app';

    public static function login(WebUser $webUser)
    {

        Yii::$app->cache->set($webUser->accessToken, $webUser, $webUser->duration);

        return true;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if (!Yii::$app->cache->exists($token)) {
            if ($type == self::TYPE_APP) {
                $app = App::find()->andWhere(['access_token' => $token])->one();

                if ($app) {
                    $webUser = new WebUser();
                    $webUser->type = WebUser::TYPE_APP;
                    $webUser->id = $app->id;
                    $webUser->model = $app;
                    $webUser->accessToken = $app->access_token;

                    self::login($webUser);
                }
            }
        }
        return Yii::$app->cache->get($token);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->type.'-'.$this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }
}
