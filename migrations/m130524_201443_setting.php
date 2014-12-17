<?php
/**
 * @link http://www.haojie.me
 * @copyright Copyright (c) 2014 Haojie studio.
 */

use app\migrations\BaseMigration;

class m130524_201443_setting extends BaseMigration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%setting}}', [
            'name'   => 'varchar(20) NOT NULL',
            'value'  => 'varchar(50) NULL',
            'type'   => 'varchar(15) NOT NULL DEFAULT \'string\'',
            'PRIMARY KEY `name`(`name`)'
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%setting}}');
    }
}
