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
//            'domain_createtime',
//            'domain_name',
//            'domain_status',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'domain_name',
//                'filter' => Domain::getAllStatuses(),
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    /** @var $model app\models\Domain */
                    return Html::encode($model->domain_name)
                        . '<br />'
                        . '<span style="color: #cccccc;">'
                        . Html::encode($model->domain_authkey)
                        . '</span>';
                },
            ],

            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'domain_status',
                'filter' => Domain::getAllStatuses(),
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    /** @var $model app\models\Domain */
                    return Html::encode($model->getStatus());
                },
            ],

            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'domain_status',
                'filter' => Domain::getAllMailers(),
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    /** @var $model app\models\Domain */
                    $oMailer = $model->getMailer();
                    return Html::encode($model->domain_mailer_id)
                    . ' '
                    . ( $oMailer === null ?
                        '---' :
                        ('<strong>'.$oMailer['mailer']['transport']['host'].'</strong>'
                            . '<br />'
                            . $oMailer['from']
                            . ' '
                            . '<span style="color: #cccccc;">' . $oMailer['mailer']['transport']['class'] . '</span>'
                        )
                    );
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {block} {unblock}',
                'buttonOptions' => [
                    'class' => 'btn btn-success',
                ],
                'buttons' => [
                    'block' => function ($url, $model, $key) {
                        $sTitle = 'Заблокировать ' . $model->domain_name;
                        $options = [
                            'title' => $sTitle,
                            'class' => 'btn btn-success',
                            'aria-label' => $sTitle,
                            'data-confirm' => $sTitle . '?',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ];
                        return $model->domain_status == Domain::DOMAIN_STATUS_ACTIVE ? Html::a('<span class="glyphicon glyphicon-ban-circle"></span>', $url, $options) : '';
                    },
                    'unblock' => function ($url, $model, $key) {
                        $sTitle = 'Разблокировать ' . $model->domain_name;
                        $options = [
                            'title' => $sTitle,
                            'class' => 'btn btn-success',
                            'aria-label' => $sTitle,
                            'data-confirm' => $sTitle . '?',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ];
                        return $model->domain_status == Domain::DOMAIN_STATUS_ACTIVE ? '' : Html::a('<span class="glyphicon glyphicon-ok-circle"></span>', $url, $options);
                    },
                ],

            ],
        ],
    ]); ?>

</div>
