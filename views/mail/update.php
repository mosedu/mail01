<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Mail */

$this->title = 'Update Mail: ' . ' ' . $model->mail_id;
$this->params['breadcrumbs'][] = ['label' => 'Mails', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->mail_id, 'url' => ['view', 'id' => $model->mail_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mail-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
