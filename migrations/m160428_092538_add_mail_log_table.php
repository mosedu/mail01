<?php

use yii\db\Schema;
use app\components\Migration;

class m160428_092538_add_mail_log_table extends Migration
{
    public function up()
    {
        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('{{%maillog}}', [
            'mlog_id' => Schema::TYPE_PK . ' Comment \'Id\'',
            'mlog_createtime' => Schema::TYPE_DATETIME . ' Comment \'Создан\'',
            'mlog_mail_id' => Schema::TYPE_INTEGER . ' Unsigned Comment \'Письмо\'',
            'mlog_type' => Schema::TYPE_SMALLINT . ' Comment \'Тип\'',
            'mlog_text' => Schema::TYPE_TEXT . ' Comment \'Текст\'',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_mlog_mail_id', '{{%maillog}}', 'mlog_mail_id');
        $this->createIndex('idx_mlog_type', '{{%maillog}}', 'mlog_type');

        $this->refreshCache();
    }

    public function down()
    {
        $this->dropTable('{{%maillog}}');
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
