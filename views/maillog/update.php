<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Maillog */

$this->title = 'Update Maillog: ' . ' ' . $model->mlog_id;
$this->params['breadcrumbs'][] = ['label' => 'Maillogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->mlog_id, 'url' => ['view', 'id' => $model->mlog_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="maillog-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
