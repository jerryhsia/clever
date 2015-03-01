<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%app}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $access_token
 */
class App extends \yii\db\ActiveRecord
{

    public $tempRoleIds = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%app}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['access_token'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'access_token' => Yii::t('app', 'Access Token'),
        ];
    }

    public function fields()
    {
        return array_merge(parent::fields(), [
            'role_ids' => 'roleIds',
            'role_ids_models' => 'roles'
        ]);
    }

    public function beforeSave($insert)
    {
        $this->access_token = md5(uniqid());
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        AppRole::deleteAll(['app_id' => $this->id]);

        if (is_array($this->tempRoleIds)) {
            foreach ($this->tempRoleIds as $roleId) {
                $appRole = new AppRole();
                $appRole->setAttributes([
                    'app_id' => $this->id,
                    'role_id' => $roleId
                ]);

                $appRole->save();
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    private $_role_ids_cache = false;

    public function getRoleIds()
    {
        if ($this->_role_ids_cache === false) {
            $result = AppRole::find()->where(['app_id' => $this->id])->all();
            $this->_role_ids_cache = ArrayHelper::getColumn($result, 'role_id');
        }
        return $this->_role_ids_cache;
    }

    public function getRoles()
    {
        $result = [];
        $roles = Yii::$app->roleService->getRoles(true);

        foreach ($this->getRoleIds() as $roleId) {
            if (isset($roles[$roleId])) {
                $result[] = $roles[$roleId];
            }
        }

        return $result;
    }
}
