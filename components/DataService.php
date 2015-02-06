<?php

namespace app\components;

use app\models\Base;
use app\models\Field;
use jerryhsia\ExcelExporter;
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
        $fields = Yii::$app->moduleService->getFields($module);
        $fields = ArrayHelper::index($fields, 'name');
        if ($mode == 'detail') {
            $relationFields = [];
            foreach ($fields as $field) {
                if ($field->getHasRelation()) {
                    $relationFields[] = $field->name;
                }
            }

            $datas = $query->all();
            foreach ($datas as $index => $data) {
                $data = $data->toArray($exportFields);
                foreach ($relationFields as $name) {
                    if ($fields[$name]->getIsMultiple()) {
                       $data[$name] = [];
                        foreach ($data[$fields[$name]->getModelField()] as $model) {
                            $data[$name][] = $model->getToString();
                        }
                    } else {
                        $data[$name] = $data[$fields[$name]->getModelField()]->getToString();
                    }
                    unset($data[$fields[$name]->getModelField()]);
                }
                $datas[$index] = $data;
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
            $fieldsMap = [];
            $multipleFields = [];
            foreach ($exportFields as $field) {
                $fieldsMap[$field] = $fields[$field]->title;
                if ($fields[$field]->getIsMultiple()) {
                    $multipleFields[] = $fields[$field]->name;
                }
            }

            if ($multipleFields) {
                foreach ($datas as $key => $row) {
                    foreach ($multipleFields as $field) {
                        $datas[$key][$field] = implode(',', $row[$field]);
                    }
                }
            }

            $exporter = new ExcelExporter([
                'filename' => $module->name.'_'.time().rand(1000, 9999).'.'.$format,
                'fields' => $fieldsMap,
                'data' => $datas
            ]);
            $exporter->send();
        }
    }
}
