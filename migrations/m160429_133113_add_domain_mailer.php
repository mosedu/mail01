<?php

use yii\db\Schema;
use app\components\Migration;

class m160429_133113_add_domain_mailer extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%domain}}',
            'domain_mailer_id',
            Schema::TYPE_STRING . ' Comment \'Mailer\''
        );

        $this->refreshCache();
    }

    public function down()
    {
        $this->dropColumn(
            '{{%domain}}',
            'domain_mailer_id'
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
