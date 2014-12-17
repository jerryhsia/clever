<?php

use app\migrations\BaseMigration;

class m141213_170509_module extends BaseMigration
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
            'role_ids' => 'varchar(100) NOT NULL',
            'PRIMARY KEY `id`(`id`)'
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%module}}');
    }
}
