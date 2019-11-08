<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Whour */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="whour-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'hour')->textInput() ?>

    <?= $form->field($model, 'htext')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'dayYr')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'did')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
