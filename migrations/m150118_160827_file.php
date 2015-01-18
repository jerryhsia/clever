<?php

use yii\db\Schema;
use yii\db\Migration;

class m150118_160827_file extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%file}}', [
            'id'   => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
            'name' => 'varchar(200) NOT NULL',
            'type' => 'varchar(200) NOT NULL',
            'size' => 'varchar(200) NOT NULL',
            'path' => 'varchar(200) NOT NULL',
            'md5'  => 'varchar(32) NOT NULL',
            'PRIMARY KEY `id`(`id`)'
        ], $tableOptions);

        $this->createTable('{{%file_usage}}', [
            'file_id'   => 'int(11) UNSIGNED NOT NULL',
            'module_id' => 'int(11) UNSIGNED NOT NULL',
            'data_id'   => 'int(11) UNSIGNED NOT NULL'
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%file}}');
        $this->dropTable('{{%file_usage}}');
    }
}
