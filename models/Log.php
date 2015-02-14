<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

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
class Log extends ActiveRecord
{

    const ACTION_INSERT = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';

    const MODULE_ROLE_ID = -1;
    const MODULE_MODULE_ID = -2;
    const MODULE_FIELD_ID = -3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%log}}';
    }

    public function fields()
    {
        return array_merge(parent::fields(), [
            'user' => 'user',
            'module' => 'module',
        ]);
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
        $this->created_at = date('Y-m-d H:i:s');
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
            'id' => Yii::t('log', 'ID'),
            'action' => Yii::t('log', 'Action'),
            'module_id' => Yii::t('log', 'Module ID'),
            'data' => Yii::t('log', 'Data'),
            'changed' => Yii::t('log', 'Changed'),
            'created_by' => Yii::t('log', 'Created By'),
            'created_at' => Yii::t('log', 'Created At'),
            'created_ip' => Yii::t('log', 'Created Ip'),
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    private static $_modules = false;

    public function getModule()
    {
        if (self::$_modules === false) {
            $modules = Yii::$app->logService->getModules();
            $modules = ArrayHelper::index($modules, 'id');
            self::$_modules = $modules;
        }

        if (isset(self::$_modules[$this->module_id])) {
            return self::$_modules[$this->module_id];
        } else {
            return null;
        }
    }
}
