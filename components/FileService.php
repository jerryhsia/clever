<?php


namespace app\components;
use app\models\File;
use app\models\FileUsage;
use Yii;
use yii\base\Component;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

/**
 * Class FileService
 *
 * @package app\components
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class FileService extends Component
{
    /**
     * Get a file
     *
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getFile($id)
    {
        return File::find()->andWhere(['id' => $id])->one();
    }

    /**
     * Get multiple files
     *
     * @param $filters
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getFiles($filters)
    {
        $query = File::find();

        if (isset($filters['id'])) {
            $query->andFilterWhere(['id' => $filters['id']]);
        }

        return $query->all();
    }

    /**
     * Save an uploaded file
     *
     * @param UploadedFile $uploadedFile
     * @return File
     * @throws \Exception
     */
    public function save(UploadedFile $uploadedFile)
    {
        $file = new File();
        $file->setAttribute('name', $uploadedFile->name);
        $file->setAttribute('size', $uploadedFile->size);
        $file->setAttribute('type', $uploadedFile->type);

        $result = $file->save();

        if ($result) {
            $uploadPath = Yii::getAlias('@app').'/web/uploads';
            try {
                if (!is_writable($uploadPath)) {
                    throw new ForbiddenHttpException(Yii::t('app', 'File upload path is not writeable'));
                }
                $yearPath = $uploadPath.'/'.date('Y');
                $monthPath = $yearPath.'/'.date('m');

                $result1 = true;
                $result2 = true;
                if (!file_exists($yearPath)) {
                    $result1 = mkdir($yearPath, 0777);
                }

                if (!file_exists($monthPath)) {
                   $result2 =  mkdir($monthPath, 0777);
                }

                if (!$result1 || !$result2) {
                    throw new ForbiddenHttpException(Yii::t('app', 'File saved failed'));
                }

                if (!copy($uploadedFile->tempName, $file->getSavePath())) {
                    throw new ForbiddenHttpException(Yii::t('app', 'File saved failed'));
                }
            } catch (\Exception $e) {
                $file->delete();
                @unlink($uploadedFile->tempName);
                throw $e;
            }
        }

        @unlink($uploadedFile->tempName);
        return $file;
    }

    /**
     * Attach a file to data
     *
     * @param $fileId
     * @param $type
     * @param $dataId
     * @param null $fieldId
     */
    public function attach($fileId, $type, $dataId, $fieldId = null)
    {
        $fileUsage = new FileUsage();
        $fileUsage->file_id = $fileId;
        $fileUsage->type = $type;
        $fileUsage->data_id = $dataId;
        $fileUsage->field_id = $fieldId;

        $fileUsage->save();
    }

    /**
     * Romove a file from data
     *
     * @param $fileId
     * @param $type
     * @param $dataId
     * @param null $fieldId
     * @throws \Exception
     */
    public function detach($fileId, $type, $dataId, $fieldId = null)
    {
        $where['file_id'] = $fileId;
        $where['type'] = $type;
        $where['data_id'] = $dataId;

        if ($fieldId) {
            $where['field_id'] = $fieldId;
        }

        if (FileUsage::deleteAll($where)) {
            if (FileUsage::find()->andWhere(['file_id' => $fileId])->count() == 0) {
                $file = self::getFile($fileId);
                if ($file) $file->delete();
            }
        }
    }
}
