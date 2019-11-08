<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Whour */

$this->title = 'Редактировать интервал: ' . $model->htext;
$this->params['breadcrumbs'][] = ['label' => 'Whours', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->hid, 'url' => ['view', 'id' => $model->hid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="whour-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
