<?php

use yii\db\Migration;

class m141213_170509_module extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%module}}', [
            'id'       => 'int(11)     UNSIGNED NOT NULL AUTO_INCREMENT',
            'name'     => 'varchar(50) NOT NULL',
            'title'    => 'varchar(50) NOT NULL',
            'is_user'  => 'tinyint(1)  NOT NULL DEFAULT 0',
            'PRIMARY KEY `id`(`id`)'
        ], $tableOptions);

        $this->createTable('{{%field}}', [
            'id'        => 'int(11)     UNSIGNED NOT NULL AUTO_INCREMENT',
            'module_id' => 'int(11)     UNSIGNED NOT NULL',
            'is_default'=> 'tinyint(1)  NOT NULL DEFAULT 0',
            'is_null'   => 'tinyint(1)  NOT NULL DEFAULT 1',
            'name'      => 'varchar(50) NOT NULL',
            'title'     => 'varchar(50) NOT NULL',
            'input'     => 'varchar(50) NOT NULL',
            'type'      => 'varchar(50) NOT NULL',
            'size'      => 'int(11)     NOT NULL DEFAULT 200',
            'PRIMARY KEY `id`(`id`)'
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%module}}');
    }
}
