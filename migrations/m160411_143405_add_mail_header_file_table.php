<?php

use yii\db\Schema;
use app\components\Migration;

class m160411_143405_add_mail_header_file_table extends Migration
{
    public function up()
    {
        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('{{%mail_header}}', [
            'mhead_id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY' . ' Comment \'Id\'',
            'mhead_mail_id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL Comment \'Письмо\'',
            'mail_text' => Schema::TYPE_TEXT . ' Comment \'Заголовки\'',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_mhead_mail_id', '{{%mail_header}}', 'mhead_mail_id');

        $this->createTable('{{%mail_file}}', [
            'mfile_id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY' . ' Comment \'Id\'',
            'mfile_mail_id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL Comment \'Письмо\'',
            'mfile_path' => Schema::TYPE_TEXT . ' Comment \'Путь к файлу\'',
            'mfile_name' => Schema::TYPE_STRING . ' Comment \'Имя файла\'',
            'mfile_crc' => Schema::TYPE_STRING . ' Comment \'Контрольная сумма\'',
            'mfile_size' => Schema::TYPE_INTEGER . ' Comment \'Размер файла\'',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_mfile_mail_id', '{{%mail_file}}', 'mfile_mail_id');
        $this->createIndex('idx_mfile_name', '{{%mail_file}}', 'mfile_name');
        $this->createIndex('idx_mfile_crc', '{{%mail_file}}', 'mfile_crc');
        $this->createIndex('idx_mfile_size', '{{%mail_file}}', 'mfile_size');

        $this->refreshCache();
    }

    public function down()
    {
        $this->dropIndex('idx_mfile_mail_id', '{{%mail_file}}');
        $this->dropIndex('idx_mfile_name', '{{%mail_file}}');
        $this->dropIndex('idx_mfile_crc', '{{%mail_file}}');
        $this->dropIndex('idx_mfile_size', '{{%mail_file}}');
        $this->dropTable('{{%mail_file}}');

        $this->dropIndex('idx_mhead_mail_id', '{{%mail_header}}');
        $this->dropTable('{{%mail_header}}');

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
