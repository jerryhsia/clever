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
 * @author Jerry Hsia<jerry9916@qq.com>
 */
class UserService extends Component
{

    public function save(User $user, array $attributes)
    {
        $user->setAttributes($attributes, false);
        return $user->save();
    }

    public function getUserByAccount($account)
    {
        return $this->getUsers(['Account' => $account])->one();
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

        if (isset($filters['account']) && strlen($filters['account'])) {
            $where[] = 'or';
            $where[] = sprintf("username = '%s'", $filters['account']);
            $where[] = sprintf("email = '%s'", $filters['account']);
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
            $userModel = $loginForm->getUser();

            $webUser = new WebUser();
            $webUser->type = WebUser::TYPE_USER;
            $webUser->id = $userModel->id;
            $webUser->model = $userModel;
            $webUser->duration = $loginForm->remember ? 7 * 24 * 3600 : 0;
            $webUser->accessToken = md5(uniqid());

            WebUser::login($webUser);
            return $webUser->accessToken;
        } else {
            return false;
        }
    }


}
