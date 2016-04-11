<?php

use yii\db\Schema;
use app\components\Migration;

class m160301_111909_add_mail_table extends Migration
{
    public function up()
    {
//        return true;
        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('{{%domain}}', [
            'domain_id' => Schema::TYPE_PK . ' Comment \'Id\'',
            'domain_createtime' => Schema::TYPE_DATETIME . ' Comment \'Создан\'',
            'domain_name' => Schema::TYPE_STRING . ' Comment \'Имя\'',
            'domain_status' => Schema::TYPE_SMALLINT . ' Comment \'Статус\'',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_domain_name', '{{%domain}}', 'domain_name');

        $this->createTable('{{%mail}}', [
            'mail_id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY' . ' Comment \'Id\'',
            'mail_domen_id' => Schema::TYPE_INTEGER . ' Comment \'Домен\'',
            'mail_createtime' => Schema::TYPE_DATETIME . ' Comment \'Создано\'',
            'mail_from' => Schema::TYPE_STRING . ' Comment \'От\'',
            'mail_fromname' => Schema::TYPE_STRING . ' Comment \'От\'',
            'mail_to' => Schema::TYPE_STRING . ' Comment \'Кому\'',
            'mail_toname' => Schema::TYPE_STRING . ' Comment \'Кому\'',
            'mail_text' => Schema::TYPE_TEXT . ' Comment \'Текст\'',
            'mail_html' => Schema::TYPE_TEXT . ' Comment \'html\'',
            'mail_status' => Schema::TYPE_SMALLINT . ' Comment \'Статус\'',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_mail_domen_id', '{{%mail}}', 'mail_domen_id');
        $this->createIndex('idx_mail_to', '{{%mail}}', 'mail_to');
        $this->createIndex('idx_mail_from', '{{%mail}}', 'mail_from');


        $this->refreshCache();
    }

    public function down()
    {
        $this->dropTable('{{%mail}}');
        $this->dropTable('{{%domain}}');
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
