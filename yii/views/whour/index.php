<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\WhourSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Временные интервалы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="whour-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'hid',
            'hour',
            'htext',
            'status',
            'dayYr',
            'did',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
