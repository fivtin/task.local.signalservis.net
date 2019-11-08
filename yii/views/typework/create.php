<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Typework */

$this->title = 'Новый вид работ';
$this->params['breadcrumbs'][] = ['label' => 'Виды работ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="typework-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
