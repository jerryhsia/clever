<?php


namespace app\behaviors;
use Yii;
use app\models\Log;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class LogBehavior extends Behavior {

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'onAfterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'onAfterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'onAfterDelete'
        ];
    }

    public function onAfterInsert($event)
    {
        if (!Yii::$app->logService->isEnable()) return;

        $log = new Log();

        $data = $event->sender->toArray();
        unset($data['to_string']);

        $attributes = [
            'action' => Log::ACTION_INSERT,
            'data_id' => $event->sender->getPrimaryKey(),
            'module_id' => $event->sender->getModuleId(),
            'data' => $data,
        ];

        Yii::$app->logService->save($log, $attributes);
    }

    public function onAfterUpdate($event)
    {
        if (!Yii::$app->logService->isEnable()) return;

        if ($event->changedAttributes) {
            $log = new Log();

            $data = $event->sender->toArray();
            unset($data['to_string']);

            $attributes = [
                'action' => Log::ACTION_UPDATE,
                'data_id' => $event->sender->getPrimaryKey(),
                'module_id' => $event->sender->getModuleId(),
                'data' => $data,
                'changed' => $event->changedAttributes,
            ];

            Yii::$app->logService->save($log, $attributes);
        }
    }

    public function onAfterDelete($event)
    {
        if (!Yii::$app->logService->isEnable()) return;

        $log = new Log();

        $data = $event->sender->toArray();
        unset($data['to_string']);

        $attributes = [
            'action' => Log::ACTION_DELETE,
            'data_id' => $event->sender->getPrimaryKey(),
            'module_id' => $event->sender->getModuleId(),
            'data' => $data,
        ];

        Yii::$app->logService->save($log, $attributes);
    }
}
