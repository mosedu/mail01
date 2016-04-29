<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Domain */

$this->title = $model->isNewRecord ? 'Новый домен' : ('Изменение домена: ' . $model->domain_name);
$this->params['breadcrumbs'][] = ['label' => 'Домены', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->domain_id, 'url' => ['view', 'id' => $model->domain_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="domain-update">

    <!-- h1><?= '' // Html::encode($this->title) ?></h1 -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
