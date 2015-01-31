<?php


namespace app\components;
use app\models\LoginForm;
use app\models\User;
use yii\base\Component;
use Yii;

/**
 * Class UserService
 *
 * @package app\components
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class UserService extends Component
{

    public function save(User $user, array $attributes)
    {
        $user->setAttributes($attributes, false);
        return $user->save();
    }

    public function getUserByIdentity($identity)
    {
        return $this->getUsers(['identity' => $identity])->one();
    }

    public function getUser($id)
    {
        return $this->getUsers(['id' => $id])->one();
    }

    public function getUsers($filters)
    {
        $query = User::find();

        if (isset($filters['id'])) {
            $query->andFilterWhere(['id' => $filters['id']]);
        }

        if (isset($filters['identity']) && strlen($filters['identity'])) {
            $where[] = 'or';
            $where[] = sprintf("username = '%s'", $filters['identity']);
            $where[] = sprintf("email = '%s'", $filters['identity']);
            $query->andWhere($where);
        }

        if (isset($filters['name'])) {
            $query->andWhere(['like', 'name', '%'.$filters['name'].'%', false]);
        }

        if (isset($filters['username'])) {
            $query->andWhere(['like', 'username', '%'.$filters['username'].'%', false]);
        }

        if (isset($filters['email'])) {
            $query->andWhere(['like', 'email', '%'.$filters['email'].'%', false]);
        }

        return $query;
    }

    public function login(LoginForm $loginForm)
    {
        if ($loginForm->validate()) {
            $user = $loginForm->getUser();

            $time = $loginForm->remember ? 7 * 24 * 3600 : 0;
            Yii::$app->user->login($user, $time);
            $accessToken = md5(uniqid().$loginForm->identity);
            Yii::$app->cache->set('user_'.$accessToken, $user->id, $time);
            return $accessToken;
        } else {
            return false;
        }
    }

    public function getIdByAccessToken($accessToken)
    {
        return Yii::$app->cache->get('user_'.$accessToken);
    }
}
