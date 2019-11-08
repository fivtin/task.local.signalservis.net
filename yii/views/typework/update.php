<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Typework */

$this->title = 'Редактирование: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Вид работ', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->twid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="typework-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
