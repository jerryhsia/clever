<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

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
            'is_user'  => 'tinyint(1)  NOT NULL DEFAULT 0',
            'role_ids' => 'varchar(100)    NULL',
            'PRIMARY KEY `id`(`id`)'
        ], $tableOptions);

        $this->createTable('{{%field}}', [
            'id'        => 'int(11)     UNSIGNED NOT NULL AUTO_INCREMENT',
            'module_id' => 'int(11)     UNSIGNED NOT NULL',
            'name'      => 'varchar(50) NOT NULL',
            'title'     => 'varchar(50) NOT NULL',
            'PRIMARY KEY `id`(`id`)'
        ], $tableOptions);

        $this->insert('{{%module}}', [
            'id'       => 1,
            'name'     => 'manager',
            'title'    => 'Manager',
            'is_user'  => 1,
            'role_ids' => '1'
        ]);

    }

    public function down()
    {
        $this->dropTable('{{%module}}');
    }
}
