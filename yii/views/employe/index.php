<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchEmploye */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Employes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employe-index">

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

            'eid',
            'fio',
            'fio_short',
            'did',
            'post',
            'dgroup',
            'note',
            'tab_task',
            'status',

            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['width' => '80'],
            ],
        ],
    ]); ?>
</div>
