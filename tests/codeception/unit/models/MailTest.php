<?php

namespace tests\codeception\unit\models;

use yii\codeception\TestCase;
use yii\db\Expression;

use app\modules\api1\models\Mail;
use app\models\MailHeader;
use app\tests\codeception\unit\fixtures\MailFixture;

class MailTest extends TestCase
{
//    public function fixtures()
//    {
//        return [
//            'mail' => [
//                'class' => MailFixture::className(),
//                'dataFile' => '@app/tests/codeception/unit/fixtures/data/mail.php'
//            ]
//        ];
//    }

    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Проверяем пустой объект - не должен пройти валидацию
     */
    public function testValidateReturnsFalseIfParametersAreNotSet() {
        $oMail = new Mail;
        $this->assertFalse($oMail->validate(), "New Mail should not validate");

        $nErrFields = 0;
        $aErrors = $oMail->getErrors();
        foreach($aErrors As $k=>$v) {
            $nErrFields++;
        }

        $this->assertEquals($nErrFields, 4, "4 fields should have errors: mail_to, mail_subject and mail_text");
        $this->assertTrue(isset($aErrors['mail_to']), "New Mail should has 'mail_to' field");
        $this->assertTrue(isset($aErrors['mail_text']), "New Mail should has 'mail_text' field");
        $this->assertTrue(isset($aErrors['mail_subject']), "New Mail should has 'mail_subject' field");
        $this->assertTrue(isset($aErrors['mail_domen_id']), "New Mail should has 'mail_domen_id' field");
    }

    /**
     * Текст html для письма - проходит валидацию
     */
    public function testValidateReturnsTrueIfHtmlBodyAreSet() {
        $configurationParams = [
            'mail_html' => '<div></div>',
        ];
        $oMail = new Mail($configurationParams);
        $this->assertTrue($oMail->validate(['mail_text', 'mail_html', ]), "Mail with set html body should validate");
    }

    /**
     * Текст plain для письма - проходит валидацию
     */
    public function testValidateReturnsTrueIfPlainBodyAreSet() {
        $configurationParams = [
            'mail_text' => 'text',
        ];
        $oMail = new Mail($configurationParams);
        $this->assertTrue($oMail->validate(['mail_text', 'mail_html', ]), "Mail with set plain body should validate");
    }

    /**
     * Невалидный адрес не проходит валидацию
     */
    public function testValidateReturnsFalseIfEmailIsIncorrect() {
        $configurationParams = [
            'mail_to' => 'text',
        ];
        $oMail = new Mail($configurationParams);
        $this->assertFalse($oMail->validate(['mail_to', ]), "Mail with incorrect mail_to should not validate");
    }

    /**
     * Валидный адрес проходит валидацию
     */
    public function testValidateReturnsTrueIfEmailIsCorrect() {
        $configurationParams = [
            'mail_to' => 'text@mail.ru',
        ];
        $oMail = new Mail($configurationParams);
        $this->assertTrue($oMail->validate(['mail_to', ]), "Mail with correct mail_to should validate");
    }

    /**
     * Валидные данные проходят валидацию
     */
    public function testValidateReturnsTrueIfExistMinimalData() {
        $configurationParams = [
            'mail_to' => 'test@mail.ru',
            'mail_text' => 'test text',
            'mail_subject' => 'test subject',
            'mail_domen_id' => 1,
        ];
        $oMail = new Mail($configurationParams);
        $this->assertTrue($oMail->validate(), "Mail with correct data should validate");
    }

    /**
     * Добавление невалидного заголовка не проходит валидацию
     */
    public function testValidateReturnsFalseIfHasUnavailableHeader() {
        $sBadHeaderName = 'errormailheader';

        $configurationParams = [
            'mail_to' => 'test@mail.ru',
            'mail_text' => 'test text',
            'mail_subject' => 'test subject',
            'mail_domen_id' => 1,
            'mailHeaders' => [
                'cc' => 'test@example.com',
                $sBadHeaderName => 'test',
                'Priority' => 3,
            ],
        ];

        $oMail = new Mail($configurationParams);
        $this->assertFalse($oMail->validate(), "Mail with incorrect header should not validate");

        $aErr = $oMail->getErrors();

        // должна появиться ошибка о поле заголовков
        $this->assertTrue(isset($aErr['mailHeaders']), "Mail error should has field mailHeaders");

        // должен появиться в ошибках плохой заголовок
        $this->assertTrue(array_reduce($aErr['mailHeaders'], function($carry, $el) use($sBadHeaderName) { return $carry || (strpos($el, $sBadHeaderName) !== false); }, false), "Mail error should has error about '{$sBadHeaderName}'");

        // не должно быть в ошибках допустимых заголовков
        $aGoodHeaders = MailHeader::getAvailableHeaders();
        foreach($aGoodHeaders As $v) {
            $v = strtolower($v);
            $this->assertFalse(array_reduce($aErr['mailHeaders'], function($carry, $el) use($v) { return $carry || (strpos($el, 'has not available header ' . $v) !== false); }, false), "Mail error should has not error about '{$v}' : ERROR = " . print_r($aErr, true));
        }
//        \Yii::info('oMail->getErrors() = ' . print_r($oMail->getErrors(), true));
    }

    /**
     * Сохранение письма с валидными заголовками
     */
    public function testSaveReturnsTrueIfHasAvailableHeaders() {

        $configurationParams = [
            'mail_to' => 'test@mail.ru',
            'mail_text' => 'test text',
            'mail_subject' => 'test subject',
            'mail_domen_id' => 1,
            'mailHeaders' => [
                'cc' => 'test@example.com',
                'Priority' => 3,
            ],
        ];

        $oMail = new Mail($configurationParams);
        $this->assertTrue($oMail->save(), "Mail with correct header should save");

        $aErr = $oMail->getErrors();

        $this->assertTrue(count($aErr) == 0, "Mail should has not errors");

        $model = Mail::findOne($oMail->mail_id);
        $oHeaders = $model->headers;

        $this->assertTrue($oHeaders !== null, 'Model should has headers');
        $this->assertTrue(is_array($oHeaders->getHeaderValue()), 'Headers should be an array');
        $this->assertTrue(count($oHeaders->getHeaderValue()) == 2, 'Headers should has 2 elements');
        $this->assertTrue($oHeaders->getHeaderValue('Cc') == 'test@example.com', 'Headers should has Cc element');
        $this->assertTrue($oHeaders->getHeaderValue('Priority') == 3, 'Headers should has Priority element');

    }


}
