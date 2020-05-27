<center>
<?php use yii\helpers\Html; ?>
<?php use Yii; ?>
<?php use PHPExcel; ?>
    
<div class="row">
    <div class="col-md-9">
        <h3>Список обращений на подключение</h3>
        <?php $this->title = "Список обращений на подключение" ?>
        <?= Html::beginForm(['/appeal/index'], 'post', ['class' => 'form-vertical']) ?>
        <label class="control-label" >Начальная дата<input id="start" class="form-control" type="date" name="start" value="<?= isset($start) ? $start : date("Y-m-01") ?>" required></label>
        <label class="control-label" >Дата окончания<input id="finish" class="form-control" type="date" name="finish" value="<?= isset($finish) ? $finish : date("Y-m-t") ?>" required></label>
        <label class="control-label" style="width: 45%;" >Строка для поиска<input id="search" class="form-control" type="text" name="search" value=""></label>
        <button type="submit" class="btn btn-success">Показать</button>
<!--        <?= Html::endForm() ?>
        <?= Html::beginForm(['/appeal/index'], 'post', ['class' => 'form-vertical']) ?>
        
        <button type="submit" class="btn btn-success">Найти</button>-->
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