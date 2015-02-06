<?php

use yii\db\Migration;

class m150206_151048_log extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%log}}', [
            'id'   => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
            'action' => 'smallint(1) NOT NULL',
            'module_id' => 'smallint(1) NOT NULL',
            'data' => 'text NOT NULL',
            'changed' => 'text NULL',
            'created_by'  => 'int(11) NOT NULL',
            'created_at'  => 'int(11) NOT NULL',
            'created_ip'  => 'varchar(30) NOT NULL',
            'PRIMARY KEY `id`(`id`)'
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%log}}');
    }
}

