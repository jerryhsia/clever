<?php


namespace app\components;
use app\models\File;
use Yii;
use yii\caching\Cache;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

/**
 * Class FileService
 *
 * @package app\components
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class FileService
{
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
}
