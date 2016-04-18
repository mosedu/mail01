<?php

use yii\db\Schema;
use app\components\Migration;

class m160418_111202_add_mail_subject extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%mail}}',
            'mail_subject',
            Schema::TYPE_STRING . ' Comment \'Тема\''
        );

        $this->refreshCache();
    }

    public function down()
    {
        $this->dropColumn(
            '{{%mail}}',
            'mail_subject'
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
