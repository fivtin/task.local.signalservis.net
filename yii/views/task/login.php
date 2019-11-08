<h1>Авторизация</h1>
<?php

// вид отображающий страницу логина/пароля по модели Login

use yii\widgets\ActiveForm;
$form = ActiveForm::begin();
?>

<?= $form->field($login_model, 'username')->textInput() ?>
<?= $form->field($login_model, 'password')->passwordInput() ?>

<div>
    <button class="btn btn-success">Войти</button>
</div>

<?php
$form = ActiveForm::end();
?>