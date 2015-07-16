<?php


namespace app\commands;

use app\models\Module;
use app\models\Role;
use Yii;
use yii\console\Controller;

/**
 * Class AppController
 *
 * @package app\commands
 * @author Jerry Hsia<jerry9916@qq.com>
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

        $modelsDir = Yii::getAlias('@app').DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR;
        $dirHandle = opendir($modelsDir);
        while ($file = readdir($dirHandle)) {
            if (substr($file, 0, 4) == 'Data') {
                @unlink($modelsDir.$file);
            }
        }
        closedir($dirHandle);
    }

    public function actionInit()
    {
        Yii::$app->logService->disable();

        $roleService = Yii::$app->roleService;
        $moduleService = Yii::$app->moduleService;
        $dataService = Yii::$app->dataService;

        $role = new Role();
        $attributes = [
            'id' => 1,
            'name' => 'Super role'
        ];
        $roleService->save($role, $attributes);
        $role = new Role();
        $attributes = [
            'id' => 2,
            'name' => 'Normal role'
        ];
        $roleService->save($role, $attributes);

        $module = new Module();
        $attributes = [
            'id'       => 1,
            'name'     => 'manager',
            'title'    => 'Manager',
            'is_user'  => 1
        ];
        $moduleService->saveModule($module, $attributes);

        $class = $module->getFullClassName();
        $model = new $class();
        $attributes = [
            'id'       => 1,
            'name'     => 'Admin',
            'email'    => 'admin@admin.com',
            'username' => 'admin',
            'password' => 'admin',
            'role_ids' => [1]
        ];
        $dataService->save($module, $model, $attributes);

        $this->actionSet();
    }

    public function actionSet()
    {
        foreach (Yii::$app->params as $key => $value) {
            Yii::$app->settingService->set($key, $value);
        }
    }
}
