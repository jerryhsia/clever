<?php


namespace app\components;
use app\models\User;
use yii\base\Component;

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

    public function getUser($id)
    {
        return $this->search(['id' => $id])->one();
    }

    public function search($filters)
    {
        $query = User::find();

        if (isset($filters['id'])) {
            $query->andFilterWhere(['id' => $filters['id']]);
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

    public function loadByIdentity($identity)
    {
        return $this->search(['username' => $identity])->one();
    }
}
