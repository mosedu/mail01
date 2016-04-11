<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DomainSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Domains';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="domain-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Domain', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'domain_id',
            'domain_createtime',
            'domain_name',
            'domain_status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
