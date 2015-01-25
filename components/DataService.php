<?php

namespace app\components;

use app\models\Base;
use app\models\Field;
use Yii;
use app\models\Module;
use yii\base\Component;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class DataService extends Component
{
    /**
     * Save a data
     *
     * @param Module $module
     * @param Base $model
     * @param array $attributes
     * @return bool
     * @throws \yii\db\Exception
     */
    public function save(Module $module, Base $model, array $attributes)
    {
        $transaction = Yii::$app->db->beginTransaction();

        $model->setAttributes($attributes, false);
        $result = $model->save();
        if ($result) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }
        return $result;
    }

    /**
     * Search data
     *
     * @param Module $module
     * @param array $filters
     * @return \yii\db\ActiveQuery
     */
    public function search(Module $module, array $filters = [])
    {
        $className = $module->getFullClassName();

        /** @var \yii\db\ActiveQuery $query */
        $query = $className::find();

        if ($filters) {
            $moduleService = Yii::$app->moduleService;
            $fields = $moduleService->getFields($module, false);
            $fields = ArrayHelper::index($fields, 'name');

            foreach ($filters as $key => $value) {
                if ($key == 'id') continue;
                if (isset($fields[$key]) && !empty($value) && ($field = $fields[$key]) && $field->is_search) {
                    if ($field->getIsMultiple()) {
                        $value = $value ? explode(',', $value): [];
                        foreach ($value as $v) {
                            $query->andWhere(sprintf("FIND_IN_SET('%s', %s)", $v, $key));
                        }
                    } else {
                        if (in_array($field->input, [Field::INPUT_RADIO, Field::INPUT_SELECT, Field::INPUT_DATE])) {
                            $query->andWhere([$key => $value]);
                        } else {
                            $query->andWhere(['like', $key, '%'.$value.'%', false]);
                        }
                    }
                }
            }

            if (isset($filters['id'])) {
                $query->andFilterWhere(['id' => $filters['id']]);
            }

            if (isset($filters['keyword']) && $filters['keyword']) {
                $query->andWhere("name like '%".$filters['keyword']."%'");
            }
        }

        return $query;
    }

    /**
     * Delete a data
     *
     * @param Module $module
     * @param ActiveRecord $model
     * @return bool
     * @throws \Exception
     * @throws \yii\db\Exception
     */
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
