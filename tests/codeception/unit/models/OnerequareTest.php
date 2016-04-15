<?php

namespace tests\codeception\unit\models;

use yii\codeception\TestCase;
use  yii\base\DynamicModel;
use app\components\OnerequareValidator;

class OnerequareTest extends TestCase
{

    protected function setUp()
    {
        parent::setUp();
        // uncomment the following to load fixtures for domain table
        //$this->loadFixtures(['domain']);
    }

    /**
     * Проверяем наличие ошибки при пустых атрибутах
     */
    public function testValidateReturnsFalseIfParametersAreNotSet() {
        $text1 = '';
        $text2 = '';
        $text3 = '';
        $model = new DynamicModel(compact('text1', 'text2', 'text3'));

        $model->addRule(
                ['text1', ],
                OnerequareValidator::className(),
                ['anotherAttributes' => ['text1', 'text2', 'text3']]
            );
//        $model->validate();
//        \Yii::info(print_r($model, true));

        $this->assertFalse($model->validate(), "validator should not validate empty fields ");

        $this->assertTrue($model->hasErrors(), "validator should set error for empty fields");
    }

    /**
     * Проверяем отсутствие ошибки при непустом аттрибуте
     */
    public function testValidateReturnsTrueIfAttributeSet() {
        $text1 = 'value';
        $text2 = '';
        $text3 = '';
        $model = new DynamicModel(compact('text1', 'text2', 'text3'));

        $model->addRule(
            ['text1', ],
            OnerequareValidator::className(),
            ['anotherAttributes' => ['text2', 'text3', ]]
        );
//        $model->validate();
//        \Yii::info(print_r($model, true));

        $this->assertTrue($model->validate(), "validator should validate not empty field");

    }

    /**
     * Проверяем отсутствие ошибки при непустом другом аттрибуте
     */
    public function testValidateReturnsTrueIfAnotherSet() {
        $text1 = '';
        $text2 = '';
        $text3 = 'value';
        $model = new DynamicModel(compact('text1', 'text2', 'text3'));

        $model->addRule(
            ['text1', ],
            OnerequareValidator::className(),
            ['anotherAttributes' => ['text2', 'text3', ]]
        );
//        $model->validate();
//        \Yii::info(print_r($model, true));

        $this->assertTrue($model->validate(), "validator should validate not empty another field");

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