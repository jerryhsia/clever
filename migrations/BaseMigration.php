<?php
/**
 * @link http://www.haojie.me
 * @license Copyright (c) 2014 Haojie studio.
 */

namespace app\migrations;
use yii\db\Migration;

class BaseMigration extends Migration {

    protected function getCommonFields()
    {
        return [
            'created_at'   => 'timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'created_user' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'created_ip'   => 'varchar(30) NULL',
            'updated_at'   => 'timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_user' => 'int(11) UNSIGNED NOT NULL DEFAULT 0',
            'updated_ip'   => 'varchar(30) NULL',
        ];
    }
}
