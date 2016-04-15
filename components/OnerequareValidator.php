<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 13.04.2016
 * Time: 15:49
 */

namespace app\components;

use Yii;
use yii\validators\Validator;
use yii\base\InvalidConfigException;
use yii\helpers\Html;

class OnerequareValidator extends Validator {

    /**
     * @var array
     *
     * list of attributes one of whitch should has value
     *
     */
    public $anotherAttributes = [];

    public $skipOnEmpty = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
//        Yii::info('OnerequareValidator: init');
        parent::init();
//        Yii::info('OnerequareValidator: init attributes = ' . print_r($this->anotherAttributes, true));
        if( count($this->anotherAttributes) == 0 ) {
            throw new InvalidConfigException("Validator should has anotherAttributes to validate.");
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
//        Yii::info('OnerequareValidator: validate ' . $attribute);
        $value = $model->$attribute;

        if ( !empty($value) ) {
//            Yii::info('OnerequareValidator(1): ' . $attribute . ' Value not empty : ' . $value);
            return;
        }

        foreach($this->anotherAttributes As $attr) {
            $value = $model->$attr;
            if ( !empty($value) ) {
//                Yii::info('OnerequareValidator(1): ' . $attr . ' Value not empty : ' . $value);
                return;
            }
        }

        $sError = $this->getErrorMsg($attribute);
//        Yii::info('OnerequareValidator: set ['.$attribute.'] error ' . $sError);
        $this->addError($model, $attribute, $sError);
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        $anotherAttributes = [];
        $message = json_encode($this->getErrorMsg($attribute), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $aNames = array_merge([$attribute], $this->anotherAttributes);
        $aNames = array_flip(array_flip($aNames));

        foreach($aNames As $v) {
            $anotherAttributes[] = Html::getInputId($model, $v);
        }

        $anotherAttributes = json_encode($anotherAttributes);

        return <<<EOT
var aAttr = {$anotherAttributes},
    bEmpty = true;

for(var i in aAttr) {
    var value = jQuery('#' + aAttr[i]).val();
    if( !(value === null || value === undefined || value == [] || value === '') ) {
        bEmpty = false;
        break;
    }
}

if( bEmpty ) {
//    yii.validation.addMessage(messages, options.message, value);
    messages.push({$message});
}
EOT;

    }

    /**
     * @param $attribute
     * @return mixed|string
     */
    public function getErrorMsg($attribute) {
        $aNames = array_merge([$attribute], $this->anotherAttributes);
        $aNames = array_flip(array_flip($aNames));

        $model = $this;
        $sLabels = array_reduce(
            $aNames,
            function($sRet, $element) use ($model) {
                return $sRet
                . ($sRet == '' ? '' : ', ')
                . (method_exists($model, 'getAttributeLabel') ? $model->getAttributeLabel($element) : $element);
            },
            ''
        );

        return 'Необходимо указать хотя бы одно значение из ' . $sLabels;

    }
}