<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TypeworkSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Виды работ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="typework-index">

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

            'twid',
            'title',
            'detail:ntext',
            'info',
            'status',
            'did',
            'cost',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
