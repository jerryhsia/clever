<?php

namespace app\traits;

trait LogTrait {

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'logBehavior' => [
                'class' => 'app\behaviors\LogBehavior'
            ]
        ]);
    }

    abstract function getModuleId();

}
