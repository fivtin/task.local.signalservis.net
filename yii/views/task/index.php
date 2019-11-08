<?php use app\models\Index; ?>
<?php use app\components\CalendarWiget; ?>
<?php use app\components\TlistWidget; ?>
<?php use yii\helpers\Html; ?>
<style>
    label {
        font-weight: normal;
    }
    .font-danger {
        color: green;
        background-color: #DDEFDD;
    }
</style>
<?php
$pnstyle = array(0 => 'success', 1 => 'info', 2 => 'default', 3 => 'warning', 4 => 'danger', 5 => 'primary');
if (!$index->select) $this->title = "Сигнал: Активные задачи";
else $this->title = "Сигнал: Задачи на ".ShowDate($index->date);
?>
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-body" style="height: 502px">
                    <!-- КОД КАЛЕНДАРЯ -->
                    <?php echo CalendarWiget::widget(['date' => $index->date, 'select' => $index->select, 'mode' => 'withTasks']); ?>
                    <center>
                    <u>Навигация</u><br>
                    <a href="/task/set-reset">Главная</a><br>
                    <a href="/task/set-today">Сегодня</a><br>
                    <a href="/task/prev-month"> -месяц </a> &nbsp;&nbsp;&nbsp; <a href="/task/next-month"> месяц+ </a><br>
                    <a href="/task/prev-year"> -год </a> &nbsp;&nbsp;&nbsp; <a href="/task/next-year"> год+ </a><br>
                    <br>
                    <?php if (($index->select) && ($index->date >= date("Y-m-d"))) { ?>
                    <a class="btn btn-success" href="/task/new/<?= $index->date ?>">Создать задачу на <?= ShowDate($index->date) ?></a>
                    <?php }
                    else { ?>
                    <a class="btn btn-success" href="/task/new">Создать задачу</a>
                    <?php } ?>
                    </center>
                    <br>
                    <?php
                    //[список сотрудников без задач<br>
                    //и время без задач<br>
                    //(если выбрана дата)]
                    ?>
                </div>
            </div>
            <div>
                <?php if (Yii::$app->user->id != -1) { ?>
                <div>
                    <?= Html::beginForm("/task/search", 'post', ['class' => 'form-horizontal']) ?>
                    <input id="search" type="text" name="search" class="form-control" />
                    <center>
                        <button type="submit" class="btn btn-primary" style="margin-top: 8px; ">Поиск</button>
                    </center>
                    <?= Html::endForm() ?>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="col-md-8">
            <!-- здесь выводим задачи  -->
            <!-- НАЧАЛО БЛОКА -->
            <?= $this->render('flashes.php') ?>
            <?php
                if (count($notes) > 0) {
                    ?><h4>Напоминания на сегодня:</h4><?php
                    foreach ($notes as $note) {
                        ?>
                        <div class="row panel panel-success">
                            <div class="panel-body font-danger">
                                <div class="col-md-10">
                                <strong><?= $note['title'] ?></strong>
                                <p><?= nl2br($note['info']) ?></p>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary pull-right glyphicon glyphicon-ok" title="Прочитал" onclick="readnote(this);" data-id="<?= $note['nid'] ?>"></button>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
            ?>
            <?php echo TlistWidget::widget(['date' => $index->date, 'select' => $index->select, 'search' => $index->search]); ?>
            <!-- КОНЕЦ БЛОКА -->
        </div>        
    </div>
</div>

<!--<div class="alert alert-success alert-dismissible" role="alert">
<strong><?= ''//$note['title'] ?></strong> <?= ''//$note['info'] ?>
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>-->
<script>
    function readnote(elem) {
        //var parNode = elem.parentNode.parentNode.parentNode;
        //parNode.style.display = 'none';
        //alert(elem.getAttribute('data-bind'));
        document.location.href = '/note/read/' + elem.getAttribute('data-id');
    }
</script>