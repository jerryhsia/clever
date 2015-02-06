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

    public function onAfterUpdate($event)
    {
        if ($event->changedAttributes) {
            $log = new Log();

            $attributes = [
                'action' => Log::ACTION_INSERT,
                'module_id' => $event->sender->getModuleId(),
                'data' => $event->sender->toArray(),
                'changed' => $event->changedAttributes,
            ];

            Yii::$app->logService->save($log, $attributes);
        }
    }
}
