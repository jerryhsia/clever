<?php

namespace app\filters;

use yii\filters\auth\QueryParamAuth;
use yii;

/**
 * Class AuthFilter
 *
 * @package app\filters
 * @author Jerry Hsia<xiajie9916@gmail.com>
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

        if (is_string($accessToken)) {
            $identity = $user->loginByAccessToken($accessToken, get_class($this));
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