<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
use app\models\Domain;

/* @var $this yii\web\View */
/* @var $model app\models\Mail */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="manual-form">
    <?php $form = ActiveForm::begin([
        'id' => 'manual-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validateOnType' => false,
    ]); ?>

    <div class="row">
        <div class="col-xs-3">
            <?= $form->field($model, 'mail_domen_id')->dropDownList(Domain::getDomainList()) // ->textInput() ?>
        </div>
        <div class="col-xs-3">
            <?= $form->field($model, 'mail_fromname')->textInput(['maxlength' => true, 'placeholder' => 'Имя От кого, email будет подставлен из домена отправки', ]) ?>
        </div>
        <div class="col-xs-3">
            <?= $form->field($model, 'mail_to')->textInput(['maxlength' => true, 'placeholder' => 'email Кому', ]) ?>
        </div>
        <div class="col-xs-3">
            <?= $form->field($model, 'mail_toname')->textInput(['maxlength' => true, 'placeholder' => 'Имя Кому', ]) ?>
        </div>
    </div>



    <?= $form->field($model, 'mail_subject')->textInput(['maxlength' => true, 'placeholder' => 'Тема письма', ]) ?>

    <?= '' // $form->field($model, 'mail_createtime')->textInput() ?>

    <?= '' // $form->field($model, 'mail_from')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mail_text')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'mail_html')->textarea(['rows' => 3]) ?>

    <?= '' // $form->field($model, 'mail_status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
 <?php

$sJs = <<<EOT
var oForm = jQuery('#{$form->options['id']}'),
    oCancel = jQuery('#{$form->options['id']}-cancel'),
    oDialog = oForm.parents('[role="dialog"]');

oCancel.on("click", function(event){ event.preventDefault(); oDialog.modal('hide'); return false; });

oForm
    .on('submit', function (event) {
    //    console.log("submit()");
        var formdata = oForm.data().yiiActiveForm,
            oRes = jQuery("#formresultarea");

        event.preventDefault();
        if( formdata.validated ) {
            // имитация отправки
            formdata.validated = false;
            formdata.submitting = true;

            window.location.reload();

            // показываем подтверждение
            oRes
                .text("Данные сохранены")
                .fadeIn(800, function(){
                    setTimeout(
                        function(){
                            oRes.fadeOut(function(){ window.location.reload(); });
                        },
                        1000
                    );
                });
        }
        return false;
    });
//console.log("oForm = ", oForm);
EOT;

$this->registerJs($sJs, View::POS_READY, 'submit_user_form');