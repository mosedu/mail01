<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MaillogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="maillog-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'mlog_id') ?>

    <?= $form->field($model, 'mlog_createtime') ?>

    <?= $form->field($model, 'mlog_mail_id') ?>

    <?= $form->field($model, 'mlog_type') ?>

    <?= $form->field($model, 'mlog_text') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
