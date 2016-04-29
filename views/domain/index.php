<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Domain;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DomainSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Домены';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="domain-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Новый домен', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'domain_id',
            'domain_createtime',
            'domain_name',
//            'domain_status',

            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'domain_status',
                'filter' => Domain::getAllStatuses(),
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    /** @var $model app\models\Mail */
                    return Html::encode($model->getStatus());
                },
            ],




            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'buttonOptions' => [
                    'class' => 'btn btn-success',
                ],

            ],
        ],
    ]); ?>

</div>
