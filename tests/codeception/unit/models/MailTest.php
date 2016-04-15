<?php

namespace tests\codeception\unit\models;

use yii\codeception\TestCase;
use yii\db\Expression;

use app\modules\api1\models\Mail;
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
     * Валидный адрес не проходит валидацию
     */
    public function testValidateReturnsTrueIfEmailIsCorrect() {
        $configurationParams = [
            'mail_to' => 'text@mail.ru',
        ];
        $oMail = new Mail($configurationParams);
        $this->assertTrue($oMail->validate(['mail_to', ]), "Mail with correct mail_to should validate");
    }


}
