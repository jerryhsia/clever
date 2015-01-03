<?php


namespace app\components;

use Yii;
use app\models\Module;
use app\models\User;
use yii\db\ActiveRecord;

class DataService
{
    public function save(Module $module, ActiveRecord $model, array $attributes)
    {
        if ($module->is_user) {
            $userService = Yii::$container->get('UserService');
            $userAttributes = $attributes;
            unset($userAttributes['user_id'], $userAttributes['id']);
            $user = null;
            if ($model->isNewRecord) {
                $user = new User();
            } else {
                $user = $userService->findById($model->user_id);
            }

            if ($userService->save($user, $userAttributes)) {
                $attributes['user_id'] = $user->id;
                $attributes['password'] = $user->password;
            } else {
                App::copyErrors($user, $model);
                return false;
            }
        }
        $model->setAttributes($attributes, false);
        return $model->save();
    }

    public function search(Module $module, array $filters = [])
    {
        $className = $module->getFullClassName();
        $query = $className::find();
        return $query;
    }

    public function delete(Module $module, ActiveRecord $model)
    {
        if ($module->is_user) {
            $model->user->delete();
        }
        return $model->delete() === false ? false : true;
    }
}
