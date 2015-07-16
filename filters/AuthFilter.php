<?php

namespace app\filters;

use app\components\WebUser;
use yii\filters\auth\QueryParamAuth;
use yii;

/**
 * Class AuthFilter
 *
 * @package app\filters
 * @author Jerry Hsia<jerry9916@qq.com>
 */
class AuthFilter extends QueryParamAuth
{
    public $allowGuest = false;

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        if ($request->isOptions || YII_ENV_TEST) {
            return true;
        }

        $accessToken = $request->getHeaders()->get('x-' . $this->tokenParam, $request->get($this->tokenParam));
        $type = $request->getQueryParam('app_id') ? WebUser::TYPE_APP : WebUser::TYPE_APP;

        if (is_string($accessToken)) {
            $identity = $user->loginByAccessToken($accessToken, $type);
            if ($identity) {
                return $identity;
            }
        }

        return null;
    }

    public function beforeAction($action)
    {
        $response = $this->response ? : Yii::$app->getResponse();

        $identity = $this->authenticate(
            $this->user ? : Yii::$app->getUser(),
            $this->request ? : Yii::$app->getRequest(),
            $response
        );

        if ($identity === null && !$this->allowGuest) {
            $this->challenge($response);
            $this->handleFailure($response);
            return false;
        }

        return true;
    }
}
