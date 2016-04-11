<?php

use yii\db\Schema;
use app\components\Migration;

class m160411_141350_change_mail_fields extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%mail}}',
            'mail_send_try',
            Schema::TYPE_SMALLINT . ' Comment \'Попытки\''
        );

        $this->addColumn(
            '{{%mail}}',
            'mail_send_last_try',
            Schema::TYPE_DATETIME . ' Comment \'Дата последней попытки\''
        );

        $this->refreshCache();
    }

    public function down()
    {
        $this->dropColumn(
            '{{%mail}}',
            'mail_send_last_try'
        );

        $this->dropColumn(
            '{{%mail}}',
            'mail_send_try'
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
