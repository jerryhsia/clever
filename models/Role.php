<?php


namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\ForbiddenHttpException;

/**
 * Role model
 *
 * @property string $id
 * @property string $name
 * @property array $permission
 */
class Role extends ActiveRecord
{

    const DEFAULT_ROLE_ID = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%role}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('role', 'ID'),
            'name' => Yii::t('role', 'Name')
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['to_string'] = 'toString';

        return $fields;
    }

    public function getToString()
    {
        return $this->name;
    }

    public function beforeSave($insert)
    {
        $this->permission = is_array($this->permission) && $this->permission ? json_encode($this->permission, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : json_encode([
            'data_permission' => ['list' => false, 'create' => false, 'update' => false, 'view' => false, 'delete' => false],
            'data_condition' => '',
            'field_permission' => ['id' => ['create' => false, 'update' => false, 'view' => false]],
        ]);
        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        $this->permission = empty($this->permission) ? [] : json_decode($this->permission, true);
    }

    public function beforeDelete() {
        if ($this->id == self::DEFAULT_ROLE_ID) {
            throw new ForbiddenHttpException(Yii::t('role', 'Default role can\'t be deleted'));
        }
        return true;
    }
}
