<?php

use yii\db\Schema;
use app\components\Migration;

class m160426_141911_add_domain_mail_from extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%domain}}',
            'domain_mail_from',
            Schema::TYPE_STRING . ' Comment \'Адрес От для домена\''
        );

        $this->addColumn(
            '{{%domain}}',
            'domain_mail_fromname',
            Schema::TYPE_STRING . ' Comment \'Имя От для домена\''
        );

        $this->refreshCache();
    }

    public function down()
    {
        $this->dropColumn(
            '{{%domain}}',
            'domain_mail_fromname'
        );

        $this->dropColumn(
            '{{%domain}}',
            'domain_mail_from'
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
