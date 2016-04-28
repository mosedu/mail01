<?php

use yii\db\Schema;
use app\components\Migration;

class m160428_141236_add_mail_mta_id extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%mail}}',
            'mail_mta_id',
            Schema::TYPE_STRING . ' Comment \'MTA Id\''
        );

        $this->refreshCache();
    }

    public function down()
    {
        $this->dropColumn(
            '{{%mail}}',
            'mail_mta_id'
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
