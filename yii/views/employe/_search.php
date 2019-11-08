<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchEmploye */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employe-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'eid') ?>

    <?= $form->field($model, 'fio') ?>

    <?= $form->field($model, 'fio_short') ?>

    <?= $form->field($model, 'did') ?>
    
    <?= $form->field($model, 'post') ?>
    
    <?= $form->field($model, 'dgroup') ?>
    
    <?= $form->field($model, 'tab_task') ?>

    <?= $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Сброс', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
