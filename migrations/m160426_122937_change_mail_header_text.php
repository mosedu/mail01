<?php

use yii\db\Schema;
use app\components\Migration;

class m160426_122937_change_mail_header_text extends Migration
{
    public function up()
    {

        $this->renameColumn(
            '{{%mail_header}}',
            'mail_text',
            'mhead_headers'
        );

        $this->refreshCache();
    }

    public function down()
    {
        $this->renameColumn(
            '{{%mail_header}}',
            'mhead_headers',
            'mail_text'
        );

        $this->refreshCache();
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
