<?php


namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * RolePermission model
 *
 * @property string $id
 * @property string $role_id
 * @property string $module_id
 * @property array $permission
 */
class RolePermission extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%role_permission}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'module_id'], 'required']
        ];
    }

    public function beforeSave($insert)
    {
        if (!is_array($this->permission) || !$this->permission) {
            $this->permission = [
                'data_permission' => [
                    'create' => false,
                    'update' => false,
                    'delete' => false,
                    'index'  => false,
                    'view'   => false,
                ],
                'data_condition' => '',
                'field_permisson' => []
            ];
        }
        $this->permission = json_encode($this->permission, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        $this->permission = json_decode($this->permission, true);
    }
}
