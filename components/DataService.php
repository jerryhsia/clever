<?php

namespace app\components;

use app\models\Base;
use app\models\Field;
use jerryhsia\JsonExporter;
use Yii;
use app\models\Module;
use yii\base\Component;
use yii\data\Sort;
use yii\db\ActiveQuery;
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

    public function export(Module $module, ActiveQuery $query)
    {
        $format = strtolower(Yii::$app->request->getQueryParam('format'));
        $mode = Yii::$app->request->getQueryParam('mode', 'origin');
        $exportFields = Yii::$app->request->getQueryParam('fields', '');
        $exportFields = explode(',', $exportFields);

        $sort = new Sort(['attributes' => $exportFields]);
        $query->orderBy($sort->getAttributeOrders());

        $datas = [];
        if ($mode == 'detail') {
            $fields = Yii::$app->moduleService->getFields($module);
            $fields = ArrayHelper::index($fields, 'name');
            $relationFields = [];
            foreach ($fields as $field) {
                if ($field->getHasRelation()) {
                    $relationFields[] = $field->name;
                }
            }

            $i = 0;
            foreach ($query->all() as $data) {
                $datas[$i] = $data->toArray();
                foreach ($relationFields as $key) {
                    if ($datas[$i][$fields[$key]->getModelField()]) {
                        $datas[$i][$key] = [];
                        foreach ($datas[$i][$fields[$key]->getModelField()] as $model) {
                            $datas[$i][$key][] = $model->getToString();
                        }
                    } else {
                        $datas[$i][$key] = $datas[$i][$fields[$key]->getModelField()]->getToString();
                    }
                    unset($datas[$i][$fields[$key]->getModelField()]);
                }
                $i++;
            }
        } else {
            $datas = ArrayHelper::toArray($query->all(), $exportFields);
        }

        if ($format == 'json') {
            $exporter = new JsonExporter([
                'filename' => $module->name.'_'.time().rand(1000, 9999).'.json',
                'fields' => $exportFields,
                'data' => $datas,
            ]);
            $exporter->send();
        } else if ($format == 'xls' || $format == 'xlsx') {

        }
    }
}
