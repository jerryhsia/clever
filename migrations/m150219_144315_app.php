<?php

use yii\db\Migration;

class m150219_144315_app extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%app}}', [
            'id'   => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
            'name' => 'varchar(50) NOT NULL',
            'access_token' => 'varchar(32) NOT NULL',
            'PRIMARY KEY `id`(`id`)'
        ], $tableOptions);
    }

    public function down()
    {
        echo "m150219_144315_app cannot be reverted.\n";

        return false;
    }
}
