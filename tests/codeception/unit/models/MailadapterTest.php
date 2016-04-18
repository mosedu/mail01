<?php

namespace tests\codeception\unit\models;

use yii;
use yii\codeception\TestCase;

use app\components\MailgateAdapter;

class MailadapterTest extends TestCase
{

    protected function setUp()
    {
        parent::setUp();
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
    public function testSendReturnsTrueIfMinimalData() {
        $ob = new MailgateAdapter();
        $sRet = $ob->send([
            'to' => 'test@mail.ru',
            'text' => 'text mail',
            'subject' => 'test subject',
            'apikey' => '333222111',
        ]);
        Yii::info('Retirn send: ' . print_r($sRet, true));
        $this->assertTrue(strlen($sRet) > 2, 'return should be more then 0');
        $aRet = json_decode($sRet, true);
        $this->assertTrue($aRet !== null, 'return should be decoded json');
        $this->assertTrue($aRet['mail_id'] && ($aRet['mail_id'] > 0), 'return should has id for new accepted mail');
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
