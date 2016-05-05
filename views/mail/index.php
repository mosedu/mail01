<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Письма';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mail-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Ручная отправка письма', ['manualform'], ['class' => 'btn btn-success showinmodal', 'title' => 'Ручная отправка']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'mail_id',
//            'mail_domen_id',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'mail_createtime',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    /** @var $model app\models\Mail */
                    return date('d.m.Y H:i:s', strtotime($model->mail_createtime))
                        . '<br />'
                        . $model->domain->domain_name
                        . ' '
                        . $model->getStatus()
                        . (($model->mail_status == \app\models\Mail::MAIL_STATUS_WAITING) && ($model->mail_send_try > 0) ? (' !! ['.$model->mail_send_try.']') : '');
                },
            ],

            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'mail_from',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    /** @var $model app\models\Mail */
                    return Html::encode($model->mail_from)
                        . (!empty($model->mail_fromname) && ($model->mail_fromname != $model->mail_from) ? ('<br />' . Html::encode($model->mail_fromname)) : '');
                },
            ],

            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'mail_to',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    /** @var $model app\models\Mail */
                    return Html::encode($model->mail_to)
                        . (!empty($model->mail_toname) && ($model->mail_toname != $model->mail_to) ? ('<br />' . Html::encode($model->mail_toname)) : '');
                },
            ],

//            [
//                'class' => 'yii\grid\DataColumn',
//                'attribute' => 'logs',
//                'format' => 'raw',
//                'value' => function ($model, $key, $index, $column) {
//                    /** @var $model app\models\Mail */
//                    return nl2br(
//                        Html::encode(
//                            implode(
//                                "\n",
//                                ArrayHelper::map($model->maillog, 'mlog_id', 'mlog_text')
//                            )
//                        )
//                    );
//                },
//            ],

            'mail_subject',

//            'mail_createtime',
//            'mail_from',
//            'mail_fromname',
            // 'mail_to',
            // 'mail_toname',
            // 'mail_text:ntext',
            // 'mail_html:ntext',
            // 'mail_status',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{log}',
                'buttons' => [
                    'log' => function ($url, $model, $key) {
                            $options = [
                                'title' => 'Логи отправки письма',
                                'class' => 'btn btn-success showinmodal',
//                                'aria-label' => $v['title'],
//                                'data-confirm' => 'Вы уверены, что хотите данное сообщение ' . $v['title'],
//                                'data-method' => 'post',
//                                'data-pjax' => '0',
                            ];
                            $aOut[] = Html::a('<span class="glyphicon glyphicon-sort"></span>', $url, $options);
                        return implode(' ', $aOut);
                    }
                ],
            ],
        ],
    ]);

    ?>

</div>
