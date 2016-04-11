<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mails';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mail-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Mail', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'mail_id',
            'mail_domen_id',
            'mail_createtime',
            'mail_from',
            'mail_fromname',
            // 'mail_to',
            // 'mail_toname',
            // 'mail_text:ntext',
            // 'mail_html:ntext',
            // 'mail_status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
