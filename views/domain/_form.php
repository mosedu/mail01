<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Domain;

/* @var $this yii\web\View */
/* @var $model app\models\Domain */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="domain-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= '' // $form->field($model, 'domain_createtime')->textInput() ?>

    <?= $form->field($model, 'domain_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'domain_status')->dropDownList(Domain::getAllStatuses()) // textInput() ?>

    <?= $form->field($model, 'domain_mailer_id')->dropDownList(Domain::getAllMailers()) ?>
    <?= '' // nl2br(print_r(Domain::getAllMailers(), true)) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', [ 'class' => 'btn btn-success', ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
