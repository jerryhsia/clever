<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

namespace app\models;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * Setting model
 *
 * @property string $name
 * @property string $type
 * @property string $value
 */
class Setting extends ActiveRecord
{

    const TYPE_STRING  = 'string';
    const TYPE_ARRAY   = 'array';
    const TYPE_INTEGER = 'integer';
    const TYPE_DOUBLE  = 'double';
    const TYPE_BOOLEAN = 'boolean';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%setting}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name'  => Yii::t('setting', 'Name'),
            'type'  => Yii::t('setting', 'Type'),
            'value' => Yii::t('setting', 'Value')
        ];
    }

    public function beforeSave($insert) {
        switch (gettype($this->value)) {
            case self::TYPE_ARRAY:
                $this->type  = self::TYPE_ARRAY;
                $this->value = Json::encode($this->value);
                break;
            case self::TYPE_INTEGER:
                $this->type  = self::TYPE_INTEGER;
                break;
            case self::TYPE_DOUBLE:
                $this->type  = self::TYPE_DOUBLE;
                break;
            case self::TYPE_BOOLEAN:
                $this->type  = self::TYPE_BOOLEAN;
                $this->value = $this->value ? 1 : 0;
                break;
            default:
                $this->type  = self::TYPE_STRING;
        }

        return parent::beforeSave($insert);
    }

    public function afterFind() {
        switch ($this->type) {
            case self::TYPE_ARRAY:
                $this->value = Json::decode($this->value);
                break;
            case self::TYPE_INTEGER:
                $this->value = intval($this->value);
                break;
            case self::TYPE_DOUBLE:
                $this->value = doubleval($this->value);
                break;
            case self::TYPE_BOOLEAN:
                $this->value = $this->value ? true : false;
                break;
        }
    }
}
