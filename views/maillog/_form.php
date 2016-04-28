<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Maillog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="maillog-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'mlog_mail_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mlog_type')->textInput() ?>

    <?= $form->field($model, 'mlog_text')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
