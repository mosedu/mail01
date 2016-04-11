<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Mail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mail-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'mail_domen_id')->textInput() ?>

    <?= $form->field($model, 'mail_createtime')->textInput() ?>

    <?= $form->field($model, 'mail_from')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mail_fromname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mail_to')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mail_toname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mail_text')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'mail_html')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'mail_status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
