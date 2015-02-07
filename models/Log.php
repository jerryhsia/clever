<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "log".
 *
 * @property integer $id
 * @property string $action
 * @property string $module_id
 * @property string $data
 * @property string $changed
 * @property integer $created_by
 * @property integer $created_at
 * @property string $created_ip
 */
class Log extends \yii\db\ActiveRecord
{

    const ACTION_INSERT = 1;
    const ACTION_UPDATE = 2;
    const ACTION_DELETE = 3;

    const ROLE_MODULE_ID = -1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['action', 'module_id', 'data'], 'required']
        ];
    }

    public function beforeSave($insert)
    {
        $this->created_by = Yii::$app->user->getId() ? Yii::$app->user->getId() : 0;
        $this->created_at = time();
        $this->created_ip = Yii::$app->request->getUserIP() ? Yii::$app->request->getUserIP() : '127.0.0.1';
        $this->data = $this->data ? json_encode($this->data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) : '';
        $this->changed = $this->changed ? json_encode($this->changed, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) : '';

        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        $this->data = $this->data ? json_decode($this->data, true) : [];
        $this->changed = $this->changed ? json_decode($this->changed, true) : [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'action' => Yii::t('app', 'Action'),
            'module_id' => Yii::t('app', 'Module ID'),
            'data' => Yii::t('app', 'Data'),
            'changed' => Yii::t('app', 'Changed'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_ip' => Yii::t('app', 'Created Ip'),
        ];
    }
}
