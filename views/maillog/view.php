<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Maillog */

$this->title = $model->mlog_id;
$this->params['breadcrumbs'][] = ['label' => 'Maillogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="maillog-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->mlog_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->mlog_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'mlog_id',
            'mlog_createtime',
            'mlog_mail_id',
            'mlog_type',
            'mlog_text:ntext',
        ],
    ]) ?>

</div>
