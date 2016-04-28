<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Mail */

$this->title = 'Логи отправки письма';
$this->params['breadcrumbs'][] = ['label' => 'Письма', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$aLog = $model->maillog;

if( count($aLog) >0 ) {
    $sRet =
    $sHtml = nl2br(
        Html::encode(
            implode(
                "\n",
                ArrayHelper::map(
                    $aLog,
                    'mlog_id',
                    function($el) {
                        return $el->mlog_text;
                    }
                )
            )
        )
    );
}
else {
    $sHtml = 'Логов для данного сообщения не найдено';
}


?>
<div class="mail-view">

    <!-- h1><?= Html::encode($this->title) ?></h1 -->

    <?= $sHtml /* DetailView::widget([
        'model' => $model,
        'attributes' => [
            'mail_id',
            'mail_domen_id',
            'mail_createtime',
            'mail_from',
            'mail_fromname',
            'mail_to',
            'mail_toname',
            'mail_text:ntext',
            'mail_html:ntext',
            'mail_status',
        ],
    ]) */ ?>

</div>
