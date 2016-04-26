<?php

namespace tests\codeception\unit\models;

use yii;
use yii\codeception\TestCase;

use app\components\MailgateAdapter;
use app\models\Mail;

/**
 *
 * Это плохие тесты, потому что они тестируют не отдельные кусочки, но пока они живут тут
 *
 */

class MailadapterTest extends TestCase
{
    /**
     *
     */
    public static function setUpBeforeClass()
    {
//        parent::setUpBeforeClass();
        $sCommand = "d:\\projects\\mysql-5.5.42\\bin\\mysql.exe -u root mail_gate < d:\\projects\\web\\mail01\\tests\\codeception\\unit\\fixtures\\data\\domains.sql";
        Yii::info('MailadapterTest setUpBeforeClass(): ' . $sCommand);
        $sRet = exec($sCommand, $aPrint, $nRetVal);
        Yii::info('MailadapterTest setUpBeforeClass(): sRet = ' . $sRet . ' aPrint = ' . implode("\n", $aPrint) . ' nRetVal = ' . $nRetVal);

//        $sCommand = "d:\\projects\\mysql-5.5.42\\bin\\mysql.exe -u root -e 'Delete From " . Mail::getTableSchema()->fullName . " Where domain_id > 0;' mail_gate";
        $sCommand = "d:\\projects\\mysql-5.5.42\\bin\\mysql.exe -u root -e \"Delete From mgate_mail Where mail_id > 0;\" mail_gate";
        Yii::info('MailadapterTest setUpBeforeClass(): ' . $sCommand);
        $sRet = exec($sCommand, $aPrint, $nRetVal);
        Yii::info('MailadapterTest setUpBeforeClass(): sRet = ' . $sRet . ' aPrint = ' . implode("\n", $aPrint) . ' nRetVal = ' . $nRetVal);
    }

//    public function _before()
//    {
//        $sCommand = "d:\\projects\\mysql-5.5.42\\bin\\mysql.exe -u root mail_gate < d:\\projects\\web\\mail01\\tests\\codeception\\unit\\fixtures\\data\\domains.sql";
//        Yii::info('MailadapterTest : _before(): ' . $sCommand);
//        $sRet = exec($sCommand, $aPrint, $nRetVal);
//        Yii::info('MailadapterTest : sRet = ' . $sRet . ' aPrint = ' . implode("\n", $aPrint) . ' nRetVal = ' . $nRetVal);
//    }

//    protected function setUp()
//    {
//        $sCommand = "d:\\projects\\mysql-5.5.42\\bin\\mysql.exe -u root mail_gate < d:\\projects\\web\\mail01\\tests\\codeception\\unit\\fixtures\\data\\domains.sql";
//        Yii::info('MailadapterTest setUp(): ' . $sCommand);
//        parent::setUp();
//        $sRet = exec($sCommand, $aPrint, $nRetVal);
//        Yii::info('MailadapterTest setUp(): sRet = ' . $sRet . ' aPrint = ' . implode("\n", $aPrint) . ' nRetVal = ' . $nRetVal);
//    }

    /**
     * Проверяем пустой массив - должно быть исключение
     * @expectedException \Exception
     */
    public function testSendReturnsFalseIfEmptyData() {
        $ob = new MailgateAdapter();
        $ob->send([]);
    }

    /**
     * Проверяем невалидный ключик
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
     * Проверяем валидный ключик
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
     * Проверяем создание посьма с заголовками
     */
    public function testSendWithAppendedHeadersReturnsTrue() {
        $ob = new MailgateAdapter();
        $sRet = $ob->send([
            'to' => 'test@mail.ru',
            'text' => 'text mail',
            'subject' => 'test subject',
            'domainkey' => 'apikey-active',
            'mailHeaders' => [
                'cc' => 'test@example.com',
                'priority' => 'hight',
            ],
        ]);
        Yii::info('Return send: ' . print_r($sRet, true));
        $this->assertTrue(strlen($sRet) > 2, 'return should be more then 0');
        $aRet = json_decode($sRet, true);
        $this->assertTrue($aRet !== null, 'return should be decoded json');
        $this->assertTrue($aRet['mail_id'] && ($aRet['mail_id'] > 0), 'return should has id for new accepted mail');

        $model = Mail::findOne($aRet['mail_id']);
        $oHeaders = $model->headers;

        $this->assertTrue($oHeaders !== null, 'Model should has headers');
        $this->assertTrue(is_array($oHeaders->getHeaderValue()), 'Headers should be an array');
        $this->assertTrue(count($oHeaders->getHeaderValue()) == 2, 'Headers should has 2 elements');
        $this->assertTrue($oHeaders->getHeaderValue('Cc') == 'test@example.com', 'Headers should has Cc element');
        $this->assertTrue($oHeaders->getHeaderValue('Priority') == 'hight', 'Headers should has Priority element');
//        \Yii::info('Headers: ' . print_r($ob->getResponseHeaders(), true));
    }

    /**
     * Проверяем заблокированный домен
     */
    public function testSendReturnsFalseIfBlockedDomain() {
        $ob = new MailgateAdapter();
        $sRet = $ob->send([
            'to' => 'test@mail.ru',
            'text' => 'text mail',
            'subject' => 'test subject',
            'domainkey' => 'apikey-blocked',
        ]);
        Yii::info('Return send: ' . print_r($sRet, true));
        $this->assertTrue(strlen($sRet) > 2, 'return should be more then 0');
        $aRet = json_decode($sRet, true);
        $this->assertTrue($aRet !== null, 'return should be decoded json');
        $this->assertTrue(isset($aRet['status']) && ($aRet['status'] == 403), 'return should has fobbiden status');
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
