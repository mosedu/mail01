<?php

namespace tests\codeception\unit\models;

use yii;
use yii\codeception\TestCase;

use app\components\MailgateAdapter;

class MailadapterTest extends TestCase
{
    public function _before()
    {
        $sCommand = "d:\\projects\\mysql-5.5.42\\bin\\mysql.exe -u root mail_gate < d:\\projects\\web\\mail01\\tests\\codeception\\unit\\fixtures\\data\\domains.sql";
        echo "\n\n" . $sCommand . "\n\n";
//        Yii::info('sCommand = ' . $sCommand);
        exec($sCommand);
    }

    protected function setUp()
    {
        parent::setUp();
        $sCommand = "d:\\projects\\mysql-5.5.42\\bin\\mysql.exe -u root mail_gate < d:\\projects\\web\\mail01\\tests\\codeception\\unit\\fixtures\\data\\domains.sql";
        echo "\n\n" . $sCommand . "\n\n";
    }

    /**
     * Проверяем пустой массив - должно быть исключение
     * @expectedException \Exception
     */
    public function testSendReturnsFalseIfEmptyData() {
        $ob = new MailgateAdapter();
        $ob->send([]);
    }

    /**
     * Проверяем минимальный массив - должно пройти
     */
    public function testSendReturnsFalseIfIncorrectKey() {
        $ob = new MailgateAdapter();
        $sRet = $ob->send([
            'to' => 'test@mail.ru',
            'text' => 'text mail',
            'subject' => 'test subject',
            'domainkey' => 'nonexistancekey',
        ]);
        Yii::info('Return send: ' . print_r($sRet, true));
        $this->assertTrue(strlen($sRet) > 2, 'return should be more then 0');
        $aRet = json_decode($sRet, true);
        $this->assertTrue($aRet !== null, 'return should be decoded json');
        $this->assertTrue(isset($aRet['status']) && ($aRet['status'] == 403), 'return should has fobbiden status');
        \Yii::info('Headers: ' . print_r($ob->getResponseHeaders(), true));
    }

    /**
     * Проверяем минимальный массив - должно пройти
     */
    public function testSendReturnsTrueIfMinimalData() {
        $ob = new MailgateAdapter();
        $sRet = $ob->send([
            'to' => 'test@mail.ru',
            'text' => 'text mail',
            'subject' => 'test subject',
            'domainkey' => 'apikey-active',
        ]);
        Yii::info('Return send: ' . print_r($sRet, true));
        $this->assertTrue(strlen($sRet) > 2, 'return should be more then 0');
        $aRet = json_decode($sRet, true);
        $this->assertTrue($aRet !== null, 'return should be decoded json');
        $this->assertTrue($aRet['mail_id'] && ($aRet['mail_id'] > 0), 'return should has id for new accepted mail');
        \Yii::info('Headers: ' . print_r($ob->getResponseHeaders(), true));
    }

    /**
     * Текст html для письма - проходит валидацию
     */
//    public function testValidateReturnsTrueIfHtmlBodyAreSet() {
//        $configurationParams = [
//            'mail_html' => '<div></div>',
//        ];
//        $oMail = new Mail($configurationParams);
//        $this->assertTrue($oMail->validate(['mail_text', 'mail_html', ]), "Mail with set html body should validate");
//    }


}
