<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DomainSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="domain-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'domain_id') ?>

    <?= $form->field($model, 'domain_createtime') ?>

    <?= $form->field($model, 'domain_name') ?>

    <?= $form->field($model, 'domain_status') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
