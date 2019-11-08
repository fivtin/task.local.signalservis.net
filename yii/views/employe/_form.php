<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Employe */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employe-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fio')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fio_short')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'did')->textInput() ?>
    
    <?= $form->field($model, 'post')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'dgroup')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'note')->textInput() ?>
    
    <?= $form->field($model, 'tab_task')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
