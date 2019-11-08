<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\components\FBFWidget;
use yii\helpers\Url;


AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <!-- script type="text/javascript" language="JavaScript" src="../../css/snow.js"></script -->
    <link href="<?= Url::home() ?>css/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i&amp;subset=cyrillic,cyrillic-ext" rel="stylesheet">
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Сигнал ТВ',
        'brandUrl' => Yii::$app->homeUrl, 
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    
    // Здесь определяются пункты меню отображаемые пользователю:
    
    $nb_items = [];
    if (!Yii::$app->user->isGuest) {
        if (Yii::$app->user->identity->role[3] != 'x')
            $nb_items[] = '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Справочники <span class="caret"></span></a>
                <ul class="dropdown-menu nav navbar-nav navbar-inverse" role="menu">
                <li><a href="/material">Материалы</a></li>
                <li><a href="/employe">Сотрудники</a></li>
                <li><a href="/typework">Виды работ</a></li>
                <li><a href="/whour">Время работ</a></li>
                          </ul></li>';
//            $nb_items[] = ['label' => 'Материалы', 'url' => ['/material']];
//        if (Yii::$app->user->identity->role[3] != 'x')
//            $nb_items[] = ['label' => 'Сотрудники', 'url' => ['/employe']];
//        if (Yii::$app->user->identity->role[3] != 'x')
//            $nb_items[] = ['label' => 'Виды работ', 'url' => ['/typework']];
//        if (Yii::$app->user->identity->role[3] != 'x')
//            $nb_items[] = ['label' => 'Время работ', 'url' => ['/whour']];
        if (Yii::$app->user->identity->role[4] != 'x')
            //$nb_items[] = ['label' => 'Отчеты', 'url' => ['/report']];
            $nb_items[] = '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="/report/">Отчеты <span class="caret"></span></a>
                <ul class="dropdown-menu nav navbar-nav navbar-inverse" role="menu">
                <li><a href="/report/">Основной</a></li>
                <li><a href="/report/task/">Задачи</a></li>
                <li><a href="/report/personal/">Персональный</a></li>'.
                // '<li><a href="/task/newtask/">Тест</a></li>'.
                          '</ul></li>';
        //$nb_items[] = ['label' => 'Справка', 'url' => ['/task/info']];
        if ((Yii::$app->user->id == 100) || (Yii::$app->user->id == 8)) $nb_items[] = ['label' => 'Зарплата', 'url' => ['/salary']];
            //$nb_items[] = '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Заявки <span class="caret"></span></a>
            //    <ul class="dropdown-menu nav navbar-nav navbar-inverse" role="menu">
            //    <li><a href="/support">Текущие</a></li>
            //    <li><a href="/support/tv">Телевидение</a></li>
            //    <li><a href="/support/internet">Интернет</a></li>
            //              </ul></li>';
        $nb_items[] = ['label' => 'Заметки', 'url' => ['/note']];
        $nb_items[] = ['label' => 'Справка', 'url' => ['/task/typework']];
        $nb_items[] = ['label' => 'Шаблоны', 'url' => ['/pattern']];
        $nb_items[] = ['label' => 'Табель', 'url' => ['/table']];
        //$nb_items[] = ['label' => 'Contact', 'url' => '#', 'options' => ['data-toggle' => 'modal', 'data-target' => '#myModal']];
        $nb_items[] =   '<li>'
                        . Html::beginForm(['/task/logout'], 'post')
                        . Html::submitButton(
                            '(' . Yii::$app->user->identity->name . ') Выйти',
                            ['class' => 'btn btn-link logout']
                        )
                        . Html::endForm()
                        . '</li>';
    }
    else $nb_items[] = ['label' => 'Войти', 'url' => ['/task/login']];
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $nb_items
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Сигнал ТВ <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

