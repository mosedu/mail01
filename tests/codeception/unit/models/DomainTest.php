<?php

namespace tests\codeception\unit\models;

use yii;
use yii\codeception\TestCase;
use yii\db\Expression;

use app\modules\api1\models\Domain;
use app\tests\codeception\unit\fixtures\DomainFixture;

class DomainTest extends TestCase
{
    /**
     *
     */
//    public static function setUpBeforeClass()
//    {
//        parent::setUpBeforeClass();
//
////        $sCommand = "d:\\projects\\mysql-5.5.42\\bin\\mysql.exe -u root -e 'Delete From " . Domain::getTableSchema()->fullName . " Where domain_id > 0;' mail_gate";
//        $sCommand = "d:\\projects\\mysql-5.5.42\\bin\\mysql.exe -u root -e 'Delete From mgate_domain Where domain_id > 0;' mail_gate";
//        Yii::info('DomainTest setUpBeforeClass(): ' . $sCommand);
//        $sRet = exec($sCommand, $aPrint, $nRetVal);
//        Yii::info('DomainTest setUpBeforeClass(): sRet = ' . $sRet . ' aPrint = ' . implode("\n", $aPrint) . ' nRetVal = ' . $nRetVal);
//    }

    public function fixtures()
    {
        return [
            'domain' => [
                'class' => DomainFixture::className(),
                'dataFile' => '@app/tests/codeception/unit/fixtures/data/domain.php'
            ]
        ];
    }

    protected function setUp()
    {
        parent::setUp();
        // uncomment the following to load fixtures for domain table
        //$this->loadFixtures(['domain']);
    }

    /**
     * Проверяем пустой объект - не должен пройти валидацию
     */
    public function testValidateReturnsFalseIfParametersAreNotSet() {
        $domen = new Domain;
        $this->assertFalse($domen->validate(['domain_name']), "New Domain should not validate");
    }

    /**
     * Валидное имя - проходит валидацию
     */
    public function testValidateReturnsTrueIfParametersAreSet() {
        $configurationParams = [
            'domain_name' => 'valid.name',
            'domain_mail_from' => 'mail@valid.name',
        ];
        $domen = new Domain($configurationParams);
        $this->assertTrue($domen->validate(), "Domain with set parameters should validate");
    }

    /**
     * Невалидное имя - не проходит валидацию
     */
    public function testValidateReturnsFalseIfNameAreDoesntHaveDot() {
        $configurationParams = [
            'domain_name' => 'invalid',
        ];
        $domen = new Domain($configurationParams);
        $this->assertFalse($domen->validate(['domain_name']), "Domain with name without dot should not validate");
    }

    /**
     * Валидное имя - проходит валидацию
     */
    public function testValidateReturnsTrueIfRussianDimainName() {
        $configurationParams = [
            'domain_name' => 'доменЁ.рф',
        ];
        $domen = new Domain($configurationParams);
        $this->assertTrue($domen->validate(['domain_name']), "Domain with russian name should validate");
    }

    /**
     * Невалидное имя - не проходит валидацию
     */
    public function testValidateReturnsFalseIfIncorrectDomainNameAreSet() {
        $domain = new Domain;
        $domain->domain_name = '----';
        $this->assertFalse($domain->validate(['domain_name']), "Invalid Domain name should not validate");
    }

    /**
     * Длинное имя - не проходит валидацию
     */
    public function testValidateReturnsFalseIfLongDomainNameAreSet() {
        $domain = new Domain;
        $domain->domain_name = str_repeat('a.a', 100);
        $this->assertFalse($domain->validate(['domain_name']), "Long Domain name should not validate");
    }

    /**
     * Генерим токен
     */
    public function testAuthKeyGeneration() {
        $domain = new Domain;
        $domain->generateAuthKey();

        $this->assertTrue(strlen($domain->domain_authkey) == 42, 'domain_authkey should has length 32 (random string) + 10 (time digits)'); // должно быть 32 + 10 = 42

        $this->assertRegExp('|^.+[\\d]{10}$|', $domain->domain_authkey, 'domain_authkey should has any chars and 10 digits on the end');

        $this->assertInstanceOf('yii\db\Expression', $domain->domain_authkey_updated, 'domain_authkey_updated should has db expression for set time NOW()');
    }

    /**
     * Читаем запись из базы
     */
    public function testGetDomainData() {
        $expectedAttrs = $this->domain['second'];

        $domain = Domain::findOne($expectedAttrs['domain_id']);

        $this->assertEquals($expectedAttrs['domain_name'], $domain->domain_name, 'Domain from DB should has its name');

        $this->assertRegExp('|^[\\d]{4}-[\\d]{2}-[\\d]{2}\\s+[\\d]{2}:[\\d]{2}:[\\d]{2}$|', $domain->domain_createtime, 'domain_createtime from DB should has MySQL time');

        $this->assertRegExp('|^[\\d]{4}-[\\d]{2}-[\\d]{2}\\s+[\\d]{2}:[\\d]{2}:[\\d]{2}$|', $domain->domain_authkey_updated, 'domain_createtime from DB should has MySQL time');

        $this->assertTrue(strlen($domain->domain_authkey) == 42, 'domain_authkey from DB should has length 32 (random string) + 10 (time digits)'); // должно быть 32 + 10 = 42

        $this->assertRegExp('|^.+[\\d]{10}$|', $domain->domain_authkey, 'domain_authkey from DB should has any chars and 10 digits on the end');

        $this->assertEquals(Domain::DOMAIN_STATUS_ACTIVE, $domain->domain_status, 'domain_status from DB should equal ' . Domain::DOMAIN_STATUS_ACTIVE);
    }

    /**
     * Сохраняем запись в базе
     */
    public function testSaveDomainData() {
        $domain = new Domain;
        $aData = [
            'domain_name' => 'test.int',
            'domain_mail_from' => 'mail@test.int',
        ];
        $domain->attributes = $aData;

        $bSaved = $domain->save();
        $this->assertTrue($bSaved, 'Domain should be saved, but has errors: ' . print_r($domain->getErrors(), true));

        $domain = Domain::findOne($domain->domain_id);

        $this->assertEquals($aData['domain_name'], $domain->domain_name, 'Domain should save its name');

        $this->assertRegExp('|^[\\d]{4}-[\\d]{2}-[\\d]{2}\\s+[\\d]{2}:[\\d]{2}:[\\d]{2}$|', $domain->domain_createtime, 'domain_createtime should has MySQL time');

        $this->assertRegExp('|^[\\d]{4}-[\\d]{2}-[\\d]{2}\\s+[\\d]{2}:[\\d]{2}:[\\d]{2}$|', $domain->domain_authkey_updated, 'domain_createtime should has MySQL time');

        $this->assertTrue(strlen($domain->domain_authkey) == 42, 'domain_authkey should has length 32 (random string) + 10 (time digits)'); // должно быть 32 + 10 = 42

        $this->assertRegExp('|^.+[\\d]{10}$|', $domain->domain_authkey, 'domain_authkey should has any chars and 10 digits on the end');

        $this->assertEquals(Domain::DOMAIN_STATUS_BLOCKED, $domain->domain_status, 'domain_status should equal ' . Domain::DOMAIN_STATUS_BLOCKED);
    }

}

/*
namespace models;

class DomainTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    // tests
    public function testMe()
    {
    }
}
*/