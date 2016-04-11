<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MailSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mail-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'mail_id') ?>

    <?= $form->field($model, 'mail_domen_id') ?>

    <?= $form->field($model, 'mail_createtime') ?>

    <?= $form->field($model, 'mail_from') ?>

    <?= $form->field($model, 'mail_fromname') ?>

    <?php // echo $form->field($model, 'mail_to') ?>

    <?php // echo $form->field($model, 'mail_toname') ?>

    <?php // echo $form->field($model, 'mail_text') ?>

    <?php // echo $form->field($model, 'mail_html') ?>

    <?php // echo $form->field($model, 'mail_status') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
