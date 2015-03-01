<?php


namespace app\models;

use app\traits\LogableTrait;
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

    use LogableTrait;

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

    public function beforeDelete()
    {
        if ($this->id == self::DEFAULT_ROLE_ID) {
            throw new ForbiddenHttpException(Yii::t('role', 'Default role can not be deleted'));
        }
        if (UserRole::find()->andWhere(['role_id' => $this->id])->count()) {
            throw new ForbiddenHttpException(Yii::t('role', 'This role has been used, can not be deleted'));
        }
        return true;
    }

    public function beforeSave($insert)
    {
        if (is_array($this->permission)) {
            $this->permission = json_encode($this->permission);
        }
        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        $this->permission = json_decode($this->permission, true);
    }

    public function getModuleId()
    {
        return Log::MODULE_ROLE_ID;
    }
}
