<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clever_file".
 *
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property string $size
 * @property string $path
 * @property string $url
 * @property string $md5
 */
class File extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type', 'size'], 'required'],
            ['type', 'validateType'],
            ['size', 'validateSize']
        ];
    }

    public function fields()
    {
        return array_merge(parent::fields(), [
            'suffix' => 'suffix',
            'url' => 'url',
            'is_image' => 'isImage'
        ]);
    }

    public function validateType ()
    {
        if ($this->hasErrors()) return;

        $settingService = Yii::$container->get('SettingService');
        $allowSuffixs = $settingService->get('allow_file_suffix');
        if (!in_array($this->suffix, $allowSuffixs)) {
            $message = 'File suffix should be in {suffixs}';
            $this->addError('type', Yii::t('file', $message, ['suffixs' => '('.implode(',', $allowSuffixs).')']));
        }
    }

    public function validateSize ()
    {
        if ($this->hasErrors()) return;

        $settingService = Yii::$container->get('SettingService');
        $allowSize = $settingService->get('allow_file_size');
        $maxSize = intval(ini_get('upload_max_filesize'));
        if ($allowSize) {
            $allowSize  = $allowSize < $maxSize ? $allowSize : $maxSize;
            if ($this->size > ($allowSize*1024*1024)) {
                $message = 'File size should smaller than {size}';
                $this->addError('size', Yii::t('file', $message, ['size' => $allowSize.'M']));
            }
        }
    }

    public function getSuffix()
    {
        return strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
    }


    public function beforeSave($insert)
    {
        $this->md5 = md5(uniqid().rand(1000, 9999));
        $this->path = $this->getPath();

        return parent::beforeSave($insert);
    }

    public function getUrl()
    {
        return $this->getPath();
    }

    public function getIsImage()
    {
        return strpos($this->type, 'image') !== false;
    }

    public function getPath()
    {
        return sprintf('%s/%s/%s', date('Y'), date('m'), $this->md5.'.'.$this->getSuffix());
    }

    public function getSavePath()
    {
        return sprintf('%s/web/uploads/%s', Yii::$app->getBasePath(), $this->getPath());
    }

    public function afterDelete()
    {
        @unlink($this->getSavePath());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('file', 'ID'),
            'name' => Yii::t('file', 'Name'),
            'type' => Yii::t('file', 'Type'),
            'size' => Yii::t('file', 'Size'),
            'path' => Yii::t('file', 'Path'),
            'md5' => Yii::t('file', 'Md5'),
        ];
    }
}
