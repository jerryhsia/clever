<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

namespace app\commands;

use Yii;
use yii\console\Controller;

/**
 * Class AppController
 *
 * @package app\commands
 * @author Jerry Hsia<xiajie9916@gmail.com>
 */
class AppController extends Controller
{
    public function actionClear()
    {
        $tables = Yii::$app->db->getSchema()->getTableNames();
        foreach ($tables as $table) {
            $sql = Yii::$app->db->queryBuilder->dropTable($table);
            Yii::$app->db->createCommand($sql)->execute();
            $this->stdout("Deleted {$table}\n");
        }
    }

}
