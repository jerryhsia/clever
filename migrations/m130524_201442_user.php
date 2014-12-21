<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

use yii\db\Migration;

class m130524_201442_user extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%role}}', [
            'id'   => 'smallint(1) UNSIGNED NOT NULL AUTO_INCREMENT',
            'name' => 'varchar(50) NOT NULL',
            'PRIMARY KEY `id`(`id`)'
        ], $tableOptions);

        $this->createTable('{{%user}}', [
            'id'       => 'int(11)     UNSIGNED NOT NULL AUTO_INCREMENT',
            'name'     => 'varchar(50) NOT NULL',
            'username' => 'varchar(50) NOT NULL',
            'password' => 'varchar(32) NOT NULL',
            'email'    => 'varchar(50) NOT NULL',
            'PRIMARY KEY `id`(`id`)'
        ], $tableOptions);

        $this->createTable('{{%user_role}}', [
            'user_id'  => 'int(11) UNSIGNED NOT NULL',
            'role_id'  => 'int(11) UNSIGNED NOT NULL'
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%role}}');
        $this->dropTable('{{%user}}');
        $this->dropTable('{{%user_role}}');
    }
}
