<?php

namespace app\components;

use app\models\Base;
use Yii;
use app\models\Module;
use app\models\User;
use yii\db\ActiveRecord;

class DataService
{
    public function save(Module $module, Base $model, array $attributes)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $model->module = $module;
        $model->setAttributes($attributes, false);
        $result = $model->save();
        if ($result) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }
        return $result;
    }

    public function search(Module $module, array $filters = [])
    {
        $className = $module->getFullClassName();
        $query = $className::find();
        return $query;
    }

    public function delete(Module $module, ActiveRecord $model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $result = $model->delete() === false ? false : true;
        if ($result) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }
        return $result;
    }
}
