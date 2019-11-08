<?php use yii\helpers\Html; ?>
<?php use Yii; ?>
<center>
<div class="row">
    <div class="col-md-9">
        <h3>Отчёт по задачам</h3>
        <?php $this->title = "Сигнал: Отчет по задачам отделов" ?>
        <?= Html::beginForm() ?>
        <label class="control-label" >Начальная дата<input id="start" class="form-control" type="date" name="start" value="<?= isset($start) ? $start : date("Y-m-01") ?>" required></label>
        <label class="control-label" >Дата окончания<input id="finish" class="form-control" type="date" name="finish" value="<?= isset($finish) ? $finish : date("Y-m-t") ?>" required></label>
        <?php 
        if (Yii::$app->user->identity->role[4] == 'f') { ?>
        <label class="control-label" > Выбрать отдел
            <select name="did" class="form-control">
            <option value="0">Все отделы</option>
            <?php 
            if (isset($did)) { ?>
            <option <?= $did == 1 ? ' selected ' : '' ?> value="1">Монтажники</option>
            <option <?= $did == 2 ? ' selected ' : '' ?> value="2">Линейщики</option>
            <option <?= $did == 3 ? ' selected ' : '' ?> value="3">Ремонтники</option>
            <?php }
            else { ?>
            <option value="1">Монтажники</option>
            <option value="2">Линейщики</option>
            <option value="3">Ремонтники</option>
            <?php }
            ?>

            </select>
        </label>
        <?php } ?>
        <button type="submit" class="btn btn-success">Отчет</button>
        <?= Html::endForm() ?>
    </div>
    <div class="col-md-3 text-left">
        <br>
        <a href="#" onclick="setDate('Current'); ">Текущий месяц</a><br>
        <a href="#" onclick="setDate('Today'); ">Сегодняшний день</a><br>
        <a href="#" onclick="setDate('LastMonth'); ">Прошлый месяц</a><br>
        <!-- <a href="#" onclick="setDate('LastWeek'); ">Прошлая неделя</a><br> -->
        <!-- <a href="#" onclick="setDate('LastYM'); ">Прошлый месяц того года</a><br> -->
        <a href="#" onclick="setDate('CurrYear'); ">Этот год</a><br>
        <a href="#" onclick="setDate('LastYear'); ">Прошлый год</a>
    </div>
</div>
<hr>
<?= isset($start) ? '<h3>Отчет по задачам отделов за период</h3><br><h2>'.ShowDate($start).' по '.ShowDate($finish).'</h2>' : '' ?>

<?php if (isset($task) && (count($task) > 0)) { ?>
<?php $No = 1; $done = 0; ?>
<table class="table table-striped table-condensed">
    <thead>
    <th>
        №
    </th>
    <th>
        Id
    </th>
    <th>
        Дата
    </th>
    <th>
        Отдел
    </th>
    <th>
        Описание задачи
    </th>
    <th>
        Статус
    </th>
    </thead>
<?php foreach ($task as $item) { ?>
    
<tr>
    <td><!-- № пункта -->
        <?= $No ?>
    </td>
    <td><!-- ID задачи -->
        <?= $item['tid'] ?>
    </td>
    <td><!-- дата задачи -->
        <?= DateReport($item['dttask']) ?>
    </td>
    <td><!-- номер отдела -->
        <?= $item['did'] ?>
    </td>
    <td <?= $item['warning'] ? ' style="font-weight: 600; " ' : '' ?>><!-- описание задачи -->
        <a href="/task/<?= $item['tid'] ?>" target="_blank" ><?= $item['title'] ?></a>
    </td>
    <td><!-- статус задачи -->
        <?php if ($item['status'] == 1) { echo 'Выполнена'; $done++; } else { echo 'В работе...'; } ?>
    </td>
</tr>

<?php
$No++;
}
?>
<tr>
    <td colspan="4">
        <h4>Всего задач: <?= $No - 1 ?></h4>
    </td>
    <td>
        <h4>Выполнено: <?= $done ?></h4>
    </td>
</tr>
</table>
</center>

<?php
}
?>
<script>
function TwoChar(input) {
    
    if (input < 10)
        result = "0" + input;
    else result = input;
    return result;
}

function setDate(value) {
//    Current
//    Today
//    LastMonth
//    LastWeek
//    LastYM
//    CurrYear
//    LastYear
    var dt = new Date();
    var start = document.getElementById('start');
    var finish = document.getElementById('finish');
    if (value == 'Current') {
        var ndate = new Date(dt.getFullYear(), dt.getMonth() + 1, 0);
        start.value = dt.getFullYear() + '-' + TwoChar(dt.getMonth() + 1) + '-01';
        finish.value = dt.getFullYear() + '-' + TwoChar(dt.getMonth() + 1) + '-' + TwoChar(ndate.getDate());
    }
    if (value == 'Today') {
        start.value = dt.getFullYear() + '-' + TwoChar(dt.getMonth() + 1) + '-' + TwoChar(dt.getDate());
        finish.value = dt.getFullYear() + '-' + TwoChar(dt.getMonth() + 1) + '-' + TwoChar(dt.getDate());
    }
    if (value == 'LastMonth') {
        var ndate = new Date(dt.getFullYear(), dt.getMonth() - 1, 1);
        start.value = ndate.getFullYear() + '-' + TwoChar(ndate.getMonth() + 1) + '-01';

        var ldate = new Date(ndate.getFullYear(), ndate.getMonth() + 1, 0);
        finish.value = ldate.getFullYear() + '-' + TwoChar(ldate.getMonth() + 1) + '-' + TwoChar(ldate.getDate());
    }
    //if (value == 'LastWeek') {}
    //if (value == 'LastYM') {}
    if (value == 'CurrYear') {
        start.value = dt.getFullYear() + '-01-01';
        finish.value = dt.getFullYear() + '-12-31';
    }
    if (value == 'LastYear') {
        dt.setYear(dt.getFullYear() - 1);
        start.value = dt.getFullYear() + '-01-01';
        finish.value = dt.getFullYear() + '-12-31';
    }

    
}
</script>