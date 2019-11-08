<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Whour */

$this->title = $model->htext;
$this->params['breadcrumbs'][] = ['label' => 'Whours', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="whour-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->hid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->hid], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить данную запись?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'hid',
            'hour',
            'htext',
            'status',
            'dayYr',
            'did',
        ],
    ]) ?>

</div>
