<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Maillog */

$this->title = 'Create Maillog';
$this->params['breadcrumbs'][] = ['label' => 'Maillogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="maillog-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
