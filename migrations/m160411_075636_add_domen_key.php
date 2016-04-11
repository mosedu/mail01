<?php

use yii\db\Schema;
use app\components\Migration;

class m160411_075636_add_domen_key extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%domain}}',
            'domain_authkey',
            Schema::TYPE_STRING . ' Comment \'Ключ\''
        );

        $this->createIndex('idx_domain_authkey', '{{%domain}}', 'domain_authkey');

        $this->addColumn(
            '{{%domain}}',
            'domain_authkey_updated',
            Schema::TYPE_DATETIME . ' Comment \'Дата обновления ключа\''
        );

        $this->refreshCache();
    }

    public function down()
    {
        $this->dropColumn(
            '{{%domain}}',
            'domain_authkey_updated'
        );

        $this->dropIndex('idx_domain_authkey', '{{%domain}}');

        $this->dropColumn(
            '{{%domain}}',
            'domain_authkey'
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
