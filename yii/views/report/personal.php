<?php //use Yii; ?>
<?php use yii\helpers\Html; ?>
<?php $this->title = 'Сигнал: Персональный отчет по сотруднику'; ?>
<center>
<div class="row">
    <div class="col-md-9">
        <h3>Персональный отчёт по сотруднику</h3>
        <?= Html::beginForm() ?>
        <label class="control-label" >Начальная дата<input id="start" class="form-control" type="date" name="start" value="<?= isset($start) ? $start : date("Y-m-01") ?>" required></label>
        <label class="control-label" >Дата окончания<input id="finish" class="form-control" type="date" name="finish" value="<?= isset($finish) ? $finish : date("Y-m-t") ?>" required></label>
        <label class="control-label" > Выбрать сотрудника
            <select name="eid" class="form-control">
            <option value="0">Не выбран</option>    
            <?php
             foreach ($emplist as $item) { ?>
                <option value="<?= $item['eid'] ?>"<?= (!empty($task) && ($task['employe']['eid'] == $item['eid'])) ? ' selected ' : '' ?>><?= $item['fio_short'] ?></option>

            <?php } ?>
            </select>
        </label>
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
<?php $No = 1; $total = 0; $sum = 0; $hour = 0; $hrtotal = 0; ?>
<?php if (isset($task)) { ?>
<h3>Персональный отчет по сотруднику</h3>
<h4 style="margin-bottom: 2px;"><u><?= $task['employe']['fio'] ?></u></h4>
<small><?= $task['employe']['post'] ?></small>
<h3>за период</h3>
<h3><?= ShowDate($start) ?> по <?= ShowDate($finish) ?></h3>
<?php if (!empty($task['task'])) { ?>
<?php //if (isset($task) && (count($task['task']) > 0)) { ?>

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
        Работы
    </th>
    <th>
        Статус
    </th>
    <th>
        Сотр
    </th>
    <th>
        Час
    </th>
    <th>
        ЕИС
    </th>
    </thead>
    <tbody>
<?php foreach ($task['task'] as $item) { ?>
<tr>
    <td><!-- № пункта -->
        <?= $No ?>
    </td>
    <td>
        <?= $item['tid'] ?>
    </td>
    <td>
        <?= DateReport($item['dttask']) ?>
    <td <?= $item['warning'] ? ' style="font-weight: 600; " ' : '' ?>>
        <a href="/task/<?= $item['tid'] ?>" target="_blank"><?= $item['title'] ?></a>
    </td>
    <td>
        <?= $item['status'] == 1 ? 'Выполнена' : 'В работе...' ?>
    </td>
    <td>
        <?= $item['empcount'] ?>
    </td>
    <td>
        <?= $item['workcount'] ?>
    </td>
    <td>
        <?php
        if ($item['status'] == 1) {
            $sum = $sum + $item['cost'];
            $hour = $hour + $item['workcount'];
        }
        $total = $total + $item['cost'];
        $hrtotal = $hrtotal + $item['workcount'];
        ?>
        <?= $item['cost'] ?>
    </td>
</tr>
    <?php
    $No++;
    }
    
    ?>

</table>
</center>
<hr>
<h4><u>Итого за период:</u> <?= $total != 0 ? number_format($total, 2) : '0' ?>, выполнено: <?= $sum != 0 ? number_format($sum, 2) : '0' ?></h4>
<h5><u>Рабочих часов:</u>  <?= $hrtotal ?>, учтенных: <?= $hour ?></h5>
<?php
    if ($task['comment']) {
        ?>
<hr>
<h4><u>Комментарии:</u></h4>
        <?php
        foreach ($task['comment'] as $comment) {
            ?>
            <?= ShowDigiDate($comment['cmdate']).' - '.$comment['comment'].'<br />' ?>
            <?php
        }
    }
?>

<?php }
else { ?>
<h4>...данные о задачах отсутствуют.</h4>
<?php } ?>
<?php } ?>

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