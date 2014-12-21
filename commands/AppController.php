<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

namespace app\commands;

use app\components\App;
use app\models\Module;
use app\models\Role;
use app\models\User;
use Yii;
use yii\console\Controller;
use yii\db\Migration;

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

    public function actionInit()
    {
        $migration = new Migration();

        $migration->insert('{{%role}}', [
            'id' => Role::SUPER_ROLE_ID,
            'name' => 'Super role'
        ]);

        $migration->insert('{{%user}}', [
            'id'       => User::SUPER_USER_ID,
            'name'     => 'Admin',
            'email'    => 'admin@admin.com',
            'username' => 'admin',
            'password' => App::createPassword('123456')
        ]);

        $migration->insert('{{%user_role}}', [
            'user_id'  => User::SUPER_USER_ID,
            'role_id'  => Role::SUPER_ROLE_ID
        ]);

        $moduleService = Yii::$container->get('ModuleService');
        $module = new Module();
        $attributes = [
            'id'       => 1,
            'name'     => 'manager',
            'title'    => 'Manager',
            'is_user'  => 1,
            'role_ids' => '1'
        ];
        $rs = $moduleService->saveModule($module, $attributes);
        var_dump($rs);
    }
}
