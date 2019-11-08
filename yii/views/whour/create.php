<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Whour */

$this->title = 'Новый интервал';
$this->params['breadcrumbs'][] = ['label' => 'Whours', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="whour-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
