<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\WhourSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="whour-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'hid') ?>

    <?= $form->field($model, 'hour') ?>

    <?= $form->field($model, 'htext') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'dayYr') ?>

    <?php echo $form->field($model, 'did') ?>

    <div class="form-group">
        <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Сброс', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
