<?php

namespace tests\codeception\unit\models;

use yii\codeception\TestCase;
use yii\db\Expression;

use app\modules\api1\models\Domain;
use app\tests\codeception\unit\fixtures\DomainFixture;

class DomainTest extends TestCase
{
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
        $user = new Domain;
        $this->assertFalse($user->validate(), "New Domain should not validate");
    }

    /**
     * Валидное имя - проходит валидацию
     */
    public function testValidateReturnsTrueIfParametersAreSet() {
        $configurationParams = [
            'domain_name' => 'valid.name',
        ];
        $user = new Domain($configurationParams);
        $this->assertTrue($user->validate(), "User with set parameters should validate");
    }

    /**
     * Невалидное имя - не проходит валидацию
     */
    public function testValidateReturnsFalseIfIncorrectDomainNameAreSet() {
        $domain = new Domain;
        $domain->domain_name = '----';
        $this->assertFalse($domain->validate(), "Invalid Domain name should not validate");
    }

    /**
     * Длинное имя - не проходит валидацию
     */
    public function testValidateReturnsFalseIfLongDomainNameAreSet() {
        $domain = new Domain;
        $domain->domain_name = str_repeat('a.a', 100);
        $this->assertFalse($domain->validate(), "Long Domain name should not validate");
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
     * Сохраняем запись в базе
     */
    public function testSaveDomainData() {
        $domain = new Domain;
        $aData = [
            'domain_name' => 'test.int',
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