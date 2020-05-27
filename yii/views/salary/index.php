<style>
    table {
        width: 100%;
    }
    table th {
        width: 88px;
        padding: 4px 0 8px 4px;
    }
    td {
        padding: 4px 0 4px 8px;
        vertical-align: top;
    }
    td span {
        display: block;
        font-size: 12px;
        color: #666666;
        /* margin-top: -5px;*/
    }
    td span:first-child {
        /* display: block; */
        float: right;
        line-height: /* 40px; */ 18px;
        margin-right: 10px;
        color: #000000;
    }
    
    td span:last-child {
        margin-top: -2px;
    }
    
    tr {
        border-bottom: 1px solid #CCCCCC;
    }
    tr:hover {
        background-color: #fafafa;
    }
    .tbl-row-block {
        /* border: 1px solid #000000; */
        background-color: #f7fff7;
    }
    .tbl-row-block:hover {
        background-color: #e2ffe2;
    }
    .tbl-row-empty {
        /* border: 1px solid #000000; */
        background-color: #ffe0e0;
    }
    .tbl-row-empty:hover {
        background-color: #ffa0a0;
    }
    
    .btn-close-modal {
        position: absolute;
        top: 10px;
        left: 10px;
    }
    
    s1 {
        color: red;
        font-size: 13px;
    }
    s2 {
        color: green;
        font-size: 13px;
    }
    s3 {
        color: blue;
        font-size: 13px;
    }
    s4 {
        color: #999999;
        font-size: 11px;
    }
    s5 {
        display: block;
        color: #999999;
        font-size: 11px;
    }
    .no-border {
        font-size: 14px;
    }
    
    .no-border tr {
        border: none;
    }
    .fs-16 {
        font-size: 16px;
    }
    #bg-layer {
        position: fixed;
        display: none;
        top: 0;
        left: 0;
        z-index: 9000;
        width: 100%;
        height: 100%;
        background-color: black;
        opacity: 0.4;
    }
    #modal {
        z-index: 9900;
        display: none;
        width: 1200px;
        /* min-height: 600px; */
        background-color: #ffffff;
        border: 1px solid #999999;
        border-radius: 4px;
        position: fixed;
        top: 200px;
        /* left: 360px; */
        left: calc(50% - 600px);
    }
    #modal-data {
        margin: 3px;
        width: 30%;
        float: right;
        margin-top: 32px;
        margin-bottom: 24px;
    }
    #modal input, #modal select {
        display: inherit;
        height: 24px;
        line-height: 24px;
        padding: 2px 6px;
    }
    #modal-table .btn, tfoot .btn {
        height: 24px;
        padding: 0;
        width: 24px;
        line-height: 24px;
    }
    #modal-table tr td:nth-child(4) {
        padding-left: 12px;
    }
    #modal .form-group {
        margin-bottom: 4px;
    }
    #modal .form-group label {
        text-align: right;
    }
    #modal-footer {
        width: 90%;
        height: 36px;
        margin-top: 16px;
        margin-bottom: 16px;
    }
    
    #load {
        z-index: 9999;
        display: none;
        position: fixed;
        width: 100vw;
        height: 100vh;
        left:50%;
        top:50%;
        margin-left:-48px; /*Смещаем блок на половину всей ширины влево*/
        margin-top:-48px;
        background: url(../files/images/load.svg) no-repeat;
    }
    .pos-1 {
        position: absolute;
        margin-left: -4px;
    }
    .pos-2 {
        position: absolute;
        margin-left: 16px;
    }
    .btn-add, .btn-copy, .btn-lock, .btn-delete {
        background: url(../images/salary/small/add.png) no-repeat;
        background-position: 50% 50%;
        height: 24px;
        width: 24px;
        display: block;
        border: none;
        cursor: pointer;
        
    }
    .btn-copy {
        background: url(../images/salary/small/copy.png) no-repeat;
    }
    .btn-lock {
        background: url(../images/salary/small/lock_01.png) no-repeat;
    }
    .btn-delete {
        background: url(../images/salary/small/delete.png) no-repeat;
    }
    
    #getDownloadLink, #loadExcelFile {
        text-align: center;
    }
    
    #getDownloadLink, #loadExcelFile {
        display: none;
    }
    
</style>

<div id="bg-layer"></div>
<div id="load"></div>
<div id="modal">
    <div id="modal-text"></div>
    <center>
        <div style="float: left; margin: 3px; width: 65%; margin-left: 50px; margin-top: 20px; ">
            <h3 id="modal-sldate"></h3>
            <h3 id="modal-fio"></h3>
            <h5 id="modal-post" style="text-decoration: 1underline; "></h5>
        </div>
        <div  id="modal-data">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Оклад</label>
                <div class="col-sm-6">
                    <input type="number" id="modal-salary" class="form-control" onchange="calcModalSalary(); " />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Премия</label>
                <div class="col-sm-6">
                    <input type="number" id="modal-award" class="form-control" onchange="calcModalSalary(); " />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Часы</label>
                <div id="modal-hour" class="col-sm-6 text-left">
                    176 / 176
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Задачи</label>
                <div id="modal-cost" class="col-sm-6 text-left">
                    48.00
                </div>
            </div>
            <div class="form-group row" style="display: none;">
                <label class="col-sm-4 col-form-label">Смены</label>
                <div id="modal-shift" class="col-sm-6 text-left">
                    Д=17 О=6
                </div>
            </div>
        </div>
        
        
        <table style="width: 90%; margin-top: 16px; ">
            <thead>
                <tr>
                    <th style="width: 30%; ">Наименование</th>
                    <th style="width: 20%; ">Основание</th>
                    <th style="width: 17%; ">Зависит от</th>
                    <th style="width: 13%; ">Периодичность</th>
                    <th style="width: 10%; ">Сумма</th>
                    <th style="width: 5%; ">Удалить</th>
                </tr>
            </thead>
            <tbody id="modal-table">
            </tbody>
            <tfoot>
                <tr>
                    <td>
                        <h4>Итого: </h4>
                    </td>
                    <td colspan="3">
                        <span id="modal-hint"></span>
                    </td>
                    <td>
                        <h4 id="modal-total"></h4>
                    </td>
                    <td>
                        <button id="button-add-row" class="btn-add btn0-success" onclick="addModalTableRow(); " ></button>
                    </td>
                </tr>
            </tfoot>
        </table>
        <div id="modal-footer">
            <button id="button-save" class="btn btn-success pull-left" onclick="saveModalSalary(); ">Сохранить</button>
            <button class="btn btn-danger pull-right" onclick="open_close_modal(false); ">Отмена</button>
            
        </div>
        <button class="btn btn-default btn-close-modal" onclick="open_close_modal(false); ">Х</button>
    </center>
</div>
<center>
<?php use yii\helpers\Html; ?>
<?php use PHPExcel; ?>
    
<?php //$this->title = "Сигнал: Расчёт pfhgkfns" ?>

<label class="control-label" >
    Месяц
    <select id="month" name='month' class="form-control" onchange="loadSalary(); ">
        <!-- <option disabled value="0">Не выбран</option> -->
        <option value="01"<?= date('m') == '01' ? ' selected ' : '' ?>>Январь</option>
        <option value="02"<?= date('m') == '02' ? ' selected ' : '' ?>>Февраль</option>
        <option value="03"<?= date('m') == '03' ? ' selected ' : '' ?>>Март</option>
        <option value="04"<?= date('m') == '04' ? ' selected ' : '' ?>>Апрель</option>
        <option value="05"<?= date('m') == '05' ? ' selected ' : '' ?>>Май</option>
        <option value="06"<?= date('m') == '06' ? ' selected ' : '' ?>>Июнь</option>
        <option value="07"<?= date('m') == '07' ? ' selected ' : '' ?>>Июль</option>
        <option value="08"<?= date('m') == '08' ? ' selected ' : '' ?>>Август</option>
        <option value="09"<?= date('m') == '09' ? ' selected ' : '' ?>>Сентябрь</option>
        <option value="10"<?= date('m') == '10' ? ' selected ' : '' ?>>Октябрь</option>
        <option value="11"<?= date('m') == '11' ? ' selected ' : '' ?>>Ноябрь</option>
        <option value="12"<?= date('m') == '12' ? ' selected ' : '' ?>>Декабрь</option>
    </select>
</label>
<label class="control-label" >
    Год
    <select id="year" name='year' class="form-control"  onchange="loadSalary(); ">
        <!-- <option disabled value="0">Не выбран</option> -->
        <option value="2021"<?= date('Y') == '2021' ? ' selected ' : '' ?>>2021</option>
        <option value="2020"<?= date('Y') == '2020' ? ' selected ' : '' ?>>2020</option>
        <option value="2019"<?= date('Y') == '2019' ? ' selected ' : '' ?>>2019</option>
        <option value="2018"<?= date('Y') == '2018' ? ' selected ' : '' ?>>2018</option>
        <option value="2017"<?= date('Y') == '2017' ? ' selected ' : '' ?>>2017</option>
    </select>
</label>
<label class="control-label" > Выбрать отдел
    <select id="did" name="did" class="form-control" onchange="loadSalary(); ">
        <option value="X" selected>Не выбран</option>
        <option value="0">Все сотрудники</option>
        <option value="1">Монтажники</option>
        <option value="2">Линейщики</option>
        <!-- <option <?= ''//$table->did == 3 ? ' selected ' : '' ?> value="3">Ремонтники</option> -->
        <option value="5">Техподдержка</option>
        <?php if (Yii::$app->user->id != 3) { ?><option value="6">Абонентский</option><?php } ?>
    </select>
</label>   
<!-- <button class="btn btn-success" onclick="loadSalary(); ">Показать</button> -->
<div id="myDiv"></div>
<div id="myDiv1"></div>


<hr>

<table id="tableID">
    <tr>
        <th>№ п/п</th>
        <th>№ таб</th>
        <th style="width: auto;">Фамилия И.О.</th>
        <th>Часы</th>
        <th>Сумма</th>
        <th>Действия</th>
    </tr>
</table>
<br>
<!-- <button class="btn btn-danger">Сформировать</button> -->

</center>
<p>
    &nbsp;
</p>
<p>
    Розовым цветом выделены записи для которых не сформирована выплата. Для такой записи возможно выполнить автоматическое создание выплаты на основе предыдущего месяца (при её наличии).
</p>
<p>
    Зеленым цветом выделены записи по которым выполнена выплата и передана на оплату. Изменить такую запись уже нельзя.
</p>
<div id="getDownloadLink"><button class="btn btn-default" onclick="getExcelFileLink();" oncontextmenu="getExcelFileLinkOnPeriod(event);">Получить ссылку на файл</button></div>
<div id="loadExcelFile"><a class="btn btn-success">Скачать файл</a></div>
<pre><?= ''//var_dump($salary) ?></pre>
<div id="myDiv"></div>
<div id="myDiv1"></div>
<script>

// округляем число до нужного числа разрядов, по умолчанию просто округление, если отрицательное значение - то знаки после запятой
function roundX(value, x = 1) {
    if (x < 0) {
        x = x * (-1);
        return Math.ceil(value * x) / x;
    }
    else {
        return Math.ceil(value / x) * x;
    }
}


function show_date_on_sldate(sldate = '') {
    
    var result = '';
    if (sldate == '') {
        var currDate = new Date();
        var year = currDate.getFullYear();
        var month = currDate.getMonth();
            month++;
            if (month < 10) month = '0' + month;
    }
    else {
        var year = '' + sldate[0] + sldate[1] + sldate[2] + sldate[3];
        var month = '' + sldate[4] + sldate[5];
    }
    
    switch(month) {
        case '01': result = 'Январь ' + year;
        break;
        case '02': result = 'Февраль  ' + year;
        break;
        case '03': result = 'Март ' + year;
        break;
        case '04': result = 'Апрель ' + year;
        break;
        case '05': result = 'Май ' + year;
        break;
        case '06': result = 'Июнь ' + year;
        break;
        case '07': result = 'Июль ' + year;
        break;
        case '08': result = 'Август ' + year;
        break;
        case '09': result = 'Сентябрь ' + year;
        break;
        case '10': result = 'Октябрь ' + year;
        break;
        case '11': result = 'Ноябрь ' + year;
        break;
        case '12': result = 'Декабрь ' + year;
        break;
        
    }
    return result;
}

function open_close_modal(show = true) {
    var bgr = document.getElementById("bg-layer");
    var mdl = document.getElementById("modal");
    if (show) {
        bgr.style.display = 'block';
        mdl.style.display = 'block';
    }
    else {
        bgr.style.display = 'none';
        mdl.style.display = 'none';
    }
    
}

function show_hide_load(show = true) {
    var bgnd = document.getElementById('bg-layer');
    var load = document.getElementById('load');
    if (show) {
        bgnd.style.display = 'block';
        load.style.display = 'block';
    }
    else  {
        bgnd.style.display = 'none';
        load.style.display = 'none';
    }
    
}

function show_hide_summa(self) {
    var input = self.parentNode.getElementsByTagName('INPUT')[0];
    if (self.value == 'summa') input.style.display = 'inherit';
    else input.style.display = 'none';
}

function runAjax(url, cFunction) {
    var xhttp;
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            cFunction(this);
        }
    };
    xhttp.open("GET", url, true);
    xhttp.send();
}
    
function ajax1(xhttp) {
    document.getElementById("myDiv").innerHTML = xhttp.responseText;
}

function ajax2(xhttp) {
    document.getElementById("myDiv1").innerHTML = xhttp.responseText;
}

function loadModalForm(xhttp) {
    
    var modal = document.getElementById('modal');
    var data = document.getElementById('modal-data');
    var table = document.getElementById('modal-table');
    var fio = document.getElementById('modal-fio');
    var post = document.getElementById('modal-post');
    var sldate = document.getElementById('modal-sldate');
    var salary = document.getElementById('modal-salary');
    var award = document.getElementById('modal-award');
    var hour = document.getElementById('modal-hour');
    var cost = document.getElementById('modal-cost');
    var aRow = document.getElementById('button-add-row');
    //var modal = document.getElementById('modal-text');
    
    var arr = JSON.parse(xhttp.responseText);
    
    // сохраняем данные из запроса в форме
    data.setAttribute('data-eid', arr['eid']);
    data.setAttribute('data-sldate', arr['sldate']);
    data.setAttribute('data-id', arr['id']);
    data.setAttribute('data-salary', arr['salary']);
    data.setAttribute('data-award', arr['award']);
    data.setAttribute('data-block', arr['block']);
    data.setAttribute('data-phour', arr['report']['phour']);
    data.setAttribute('data-whour', arr['report']['whour']);
    data.setAttribute('data-cost', arr['report']['cost']);
    data.setAttribute('data-shour', arr['report']['_shour']);
    
    // выводим данные о сотруднике
    fio.innerHTML = arr['fio'];
    post.innerHTML = arr['post'];
    sldate.innerHTML = 'Начисления за ' + show_date_on_sldate(arr['sldate']) + ' года';
    salary.value = arr['salary'];
    award.value = arr['award'];
    hour.innerHTML = arr['report']['phour'] + ' / ' + arr['report']['whour'] + ' / ' + arr['report']['_shour'];
    hour.title = 'Часов по календарю: ' + arr['report']['phour'] + '\nЧасов по табелю: ' + arr['report']['whour'] + '\nЧасов переработки: ' + arr['report']['_shour'];
    cost.innerHTML = arr['report']['cost'];
    
    var shifts = '';
    for (var shift in arr['report']['table']) {
        shifts+= shift + '=' + arr['report']['table'][shift] + ' ';
    }
    
    if (arr['block'] == 1) aRow.style.display = 'none';
    else aRow.style.display = 'block';
        
    //alert(shifts);
    //arr['report']['table'];
    //arr['report']['table']['8'];
    
//    var myDiv = document.getElementById('myDiv');
//        myDiv.innerHTML = xhttp.responseText;
    
    // очищаем строки таблицы
    var rows = document.getElementsByClassName('modal-table-row');
    while(rows.length > 0){
        rows[0].parentNode.removeChild(rows[0]);
    }
    
    var tmp_row = document.querySelector('#tmp-row');

    // заполняем строки
    for (var id in arr['payout']) {
        
            // клонируем новую строку и вставляем её в таблицу

            var clone = document.importNode(tmp_row.content, true);
            tr = clone.querySelector('tr.modal-table-row');
            tr.setAttribute('data-id', arr['payout'][id]['id']);
            tr.setAttribute('data-change', 'none');
            td = clone.querySelectorAll('td');
            info = td[0].getElementsByTagName('input')[0];
            info.value = arr['payout'][id]['info'];
            sBase = td[1].getElementsByTagName('option');
            for (i = 0; i < sBase.length; i++) {
                if (sBase[i].value == arr['payout'][id]['base']) {
                    sBase[i].selected = true;
                }
                else {
                    
                    if ((arr['payout'][id]['base'].indexOf('summa=') != -1) && (sBase[i].value == 'summa')) {
                        input = sBase[i].parentNode.parentNode.getElementsByTagName("INPUT")[0];
                        //sBase[i].parentNode.parentNode.getElementsByTagName("INPUT")[0].style.display = 'inherit';
                        //sBase[i].parentNode.parentNode.getElementsByTagName('INPUT')[0].style.display = 'true';
                        input.style.display = 'inherit';
                        input.value = arr['payout'][id]['base'].substr(6);
                        sBase[i].selected = true;
                    }
                }
            }
            sDep = td[2].getElementsByTagName('option');
            for (i = 0; i < sDep.length; i++) {
                if (sDep[i].value == arr['payout'][id]['depends']) sDep[i].selected = true;
            }
            sType = td[3].getElementsByTagName('option');
            for (i = 0; i < sType.length; i++) {
                if (sType[i].value == arr['payout'][id]['type']) sType[i].selected = true;
            }
            if (arr['block'] == 1) td[5].innerHTML = '';
            
            table.appendChild(clone);
    }
    
    calcModalSalary();

}

function addModalTableRow() {
    var tmp_row = document.querySelector('#tmp-row');
    var table = document.getElementById('modal-table');
    
    var clone = document.importNode(tmp_row.content, true);
    
    tr = clone.querySelector('tr.modal-table-row');
    tr.setAttribute('data-id', '-1');
    tr.setAttribute('data-change', 'new');
    td = clone.querySelectorAll('td');
    sBase = td[1].getElementsByTagName('option');
    sBase[0].selected = true;
    sDep = td[2].getElementsByTagName('option');
    sDep[0].selected = true;
    sType = td[3].getElementsByTagName('option');
    sType[0].selected = true;
    
    table.appendChild(clone);
    calcModalSalary();
    
}

function removeModalTableRow(self) {
    if (confirm('Удалить строку начислений?')) {
        if (self.parentNode.parentNode.getAttribute('data-id') != -1) {
            self.parentNode.parentNode.style.display = 'none';
            self.parentNode.parentNode.setAttribute('data-change', 'remove');
        }
        else {
            var elem = self.parentNode;
            var parent = elem.parentNode;
            parent.remove(elem);
        }
        calcModalSalary();
    }
}

function calcModalSalary() {
    
    var table = document.getElementById('modal-table');
    var salary = document.getElementById('modal-salary').value;
    var award = document.getElementById('modal-award').value;
    var data = document.getElementById('modal-data');
    var save = document.getElementById('button-save');
    
    //var salary = data.getAttribute('data-salary', 1);
    var phour = data.getAttribute('data-phour', 1);
    var whour = data.getAttribute('data-whour', 0);
    var shour = data.getAttribute('data-shour', 0);
    var cost = roundX(data.getAttribute('data-cost', 0), -100);
    var block = data.getAttribute('data-block', 0);
    
    
    var rows = table.getElementsByClassName('modal-table-row');
    
    var mtotal = document.getElementById('modal-total');
    var total = 0;
    var show_rows = 0; // счетчик отображаемых строк таблицы
    
    for (i = 0; i < rows.length; i++) {
        
        var row_total = 0;
        
        // не обрабатываем строку помеченную как "удалена"
        if (rows[i].getAttribute('data-change') == 'remove') continue;
        else {
            
            show_rows++;
            var td = rows[i].getElementsByTagName('td');
            var sBase = td[1].getElementsByTagName('select')[0].value;
            var summa = td[1].getElementsByTagName('input')[0].value;
            var sDep = td[2].getElementsByTagName('select')[0].value;
            var sType = td[3].getElementsByTagName('select')[0].value;
            //alert(salary + ' ' + whour + ' ' + phour + ' ' + total);
            switch (sBase) {
                case 'salary': 
                    switch (sDep){
                        case 'shiftcount': row_total = row_total + salary * whour / phour;
                        break;
                        case 'taskcount': row_total = row_total + cost * salary / phour;
                        break;
                        case 'overtime': row_total = row_total + shour * salary / phour;
                        break;
                        case 'fixed': row_total = row_total + salary * 1;
                        break;
                        
                    }
                        
                break;
                case 'award':
                    switch (sDep){
                        case 'shiftcount': row_total = row_total + award * whour / phour;
                        break;
                        case 'taskcount': row_total = row_total + cost * award / phour;
                        break;
                        case 'overtime': row_total = row_total + shour * award / phour;
                        break;
                        case 'fixed': row_total = row_total + award * 1;
                        break;
                        
                    }
                break;
                case 'summa':
                    switch (sDep){
                        case 'shiftcount': row_total = row_total + summa * whour / phour;
                        break;
                        case 'taskcount': row_total = row_total + summa * cost;
                        break;
                        case 'overtime': row_total = row_total + summa * shour;
                        break;
                        case 'fixed': row_total = row_total + summa * 1;
                        break;
                        
                    }
                break;
            }
            row_total = roundX(row_total, 10);
            td[4].innerHTML = row_total;
            total = total + row_total;
            //alert(summa);
        }
        
    }
    
    if ((show_rows > 0) && (salary > 0) && (award > 0) && (block != 1)) {
        save.style.display = 'block';
    }
    else {
        save.style.display = 'none';
    }
    
    mtotal.innerHTML = total;// Math.ceil(total / 10) * 10;
    
}

function getSaveResult(xhttp) {
    if (xhttp.responseText) {
        open_close_modal(false);
        loadSalary();
    }
    else alert('При выполнении операции произошла ошибка!');
    //return -1;
}

function saveModalTableRows(xhttp) {
    //alert('paysalary.id = ' + xhttp.responseText);
    
    var id = xhttp.responseText; 
    if (id == -1) {
        alert('При сохранении данных произошла ошибка! Попробуйте повторить операцию');
        return ;
    }
        
    var data = document.getElementById('modal-data');
    var salary_id = id; //data.getAttribute('data-id');
        
    var table = document.getElementById('modal-table');
    var rows = table.getElementsByTagName('TR');
    var arr = {};
    
    for (i = 0; i < rows.length; i++) {
        var _arr = {};
        arr[i] = _arr;
        var id = rows[i].getAttribute('data-id');
            arr[i]['id'] = id;
        var mode = rows[i].getAttribute('data-change');
            arr[i]['mode'] = mode;
        var td = rows[i].getElementsByTagName('TD');
        var info = td[0].getElementsByTagName('input')[0].value;
            arr[i]['info'] = info;
        var base = td[1].getElementsByTagName('select')[0].value;
            arr[i]['base'] = base;
        var summ = td[1].getElementsByTagName('input')[0].value;        
            arr[i]['summ'] = summ;
        var deps = td[2].getElementsByTagName('select')[0].value;
            arr[i]['deps'] = deps;
        var type = td[3].getElementsByTagName('select')[0].value;
            arr[i]['type'] = type;
        
        
        //arr[i] = _arr;
        //alert(arr);
        //if (deps == 'summa') deps = 'summa=' + summ;
        var url = '/ajax/save-modal-table-row?id=' + id + '&salary_id=' + salary_id + '&mode=' + mode + '&base=' + base + '&depends=' + deps + '&summa=' + summ +  '&type=' + type + '&info=' + info + '&sorting=' + i;
        //alert(url);
        //runAjax(url, getSaveResult);
    }
    
    runAjax('/ajax/save-modal-table-array?salary_id=' + salary_id + '&data=' + JSON.stringify(arr), getSaveResult);

    return -1;    
}


function saveModalSalary() {
    
    
    var data = document.getElementById('modal-data');
        var _eid = data.getAttribute('data-eid');
        var _sldate = data.getAttribute('data-sldate');
        var _id = data.getAttribute('data-id');
        var _salary = data.getAttribute('data-salary');
        var _award = data.getAttribute('data-award');
        var _block = data.getAttribute('data-block');
    var salary = document.getElementById('modal-salary').value;
    var award = document.getElementById('modal-award').value;
    var table = document.getElementById('modal-table');
    var rows = table.getElementsByTagName('TR');
    

    if ((salary == -1) || (award == -1) || (rows.length == 0)) {
        alert('Проверьте правильность заполнения полей формы!');
        return null;
    }
    
    runAjax("/ajax/insert-paysalary?id=" + _id + "&eid=" + _eid + "&sldate=" + _sldate + "&salary=" + salary + "&award=" + award, saveModalTableRows);
 
}


function loadTable(xhttp) {
    
    var table = document.getElementById("tableID");
    var rows = table.getElementsByClassName("tbl-row");
    var did = document.getElementById('did').value;
    
    // очищаем таблицу
    while(rows.length > 0){
        rows[0].parentNode.removeChild(rows[0]);
    }
    
    // заполняем новыми данными
    var arr = JSON.parse(xhttp.responseText);
    
//        document.getElementById("myDiv").innerHTML = xhttp.responseText;
    
    var cnt = 1; // номер по порядку
    for (var key in arr) {
        
        // игнорируем строку если не совпадает отдел
        if ((did != 0) && (did != arr[key]['did'])) continue;
        
        var fio = arr[key]['fio_short'];
        
        if ('salary' in arr[key]) 
            var salary = getSalaryInfo(arr[key]['salary'][0], arr[key]['report']);
        else var salary = false;
        
        if ('_salary' in arr[key])
            var _salary = getSalaryInfo(arr[key]['_salary'][0], arr[key]['_report']);
        else var _salary = false;
          
        
        // 3. - формируем строку
        
        var tr = document.createElement("TR");
//        if (!payment) tr.classList.add("tbl-row-empty");
//        if (block) tr.classList.add("tbl-row-block");
        tr.classList.add("tbl-row");
        if (salary && salary['block']) tr.classList.add("tbl-row-block");
        tr.id = 'eid[' + arr[key]['eid'] + '][' + arr[key]['sldate'] + ']';
        tr.setAttribute('data-eid', arr[key]['eid']);
        tr.setAttribute('data-sldate', arr[key]['sldate']);
        tr.title = salary ? salary['title'] : '';
        tr.onclick = function() {
                         tr_click(this);
        };
        var td1 = document.createElement("TD");
            td1.innerHTML = cnt;
        var td2 = document.createElement("TD");
            td2.innerHTML = arr[key]['eid'];
        var td3 = document.createElement("TD");
            td3.classList.add("fs-16");
            td3.innerHTML = arr[key]['fio_short'];
        var sp1 = document.createElement("SPAN"); // tr3
            sp1.innerHTML = arr[key]['post'];
        var sp2 = document.createElement("SPAN"); // tr3
            sp2.innerHTML = "ОКЛАД: <s1>" + (salary ? salary['base'] : '0') + (_salary ? (' <s4>' + _salary['base']) + '</s4>' : '') + "</s1>, ПРЕМИЯ: <s2>" + (salary ? salary['award'] : '0') + (_salary ? (' <s4>' + _salary['award']) + '</s4>' : '') + "</s2>, ПООЩРЕНИЕ: <s3>" + (salary ? salary['ext'] : '0') + (_salary ? (' <s4>' + _salary['ext']) + '</s4>' : '') + "</s3>";
            //else sp2.innerHTML = "ОКЛАД: <s1>" + Math.ceil(sh_base) + "</s1>, ПРЕМИЯ: <s2>" + Math.ceil(sh_award) + "</s2>, ПООЩРЕНИЕ: <s3>" + Math.ceil(sh_ext) + "</s3>" + '<br>' + _footer;
        var td4 = document.createElement("TD");
        var td5 = document.createElement("TD");
            td5.classList.add("fs-16");
            //if (_total != 0) td5.innerHTML = Math.ceil((sh_base + sh_award + sh_ext) / 10) * 10 + '<br><s4>' + _total + '</s4>';
            //else
            td5.innerHTML = (salary ? salary['total'] : '0') + (_salary ? ('<s5>' + _salary['total'] + '</s5>') : '') ;//Math.ceil((sh_base + sh_award + sh_ext) / 10) * 10;
            //td5.innerHTML = Math.ceil((sh_base + sh_award + sh_ext) / 10) * 10;
            //td5.title = Math.ceil((sh_base + sh_award + sh_ext) / 10) * 10 * 0.87;
            td5.title = salary ? Math.ceil(salary['total'] * 0.87) : '';
        var td6 = document.createElement("TD");
            td6.setAttribute('tools', 'source');
        
        // кнопка создания начислений на основании прошлого периода
        if (_salary && !salary) {
            var btn = document.createElement("A");
                btn.classList.add('btn-copy');
                //btn.classList.add('btn-xs');
                btn.classList.add('btn0-warning');
                btn.classList.add('pos-2');
                btn.title = 'скопировать из прошлого месяца';
                //btn.innerHTML = '*';
                btn.setAttribute('data-eid', arr[key]['eid']);
                btn.setAttribute('data-sldate', arr[key]['sldate']);
                btn.onclick = function(e) {
                    //alert('Завершено.');
                    //open_modal();
                    if (confirm('Начисления будут скопированы из прошлого месяца.\nСоздать начисления автоматически?'))
                        copySalaryFromPrevMonth(this);
                    e.stopPropagation();
                };
        }
        else var btn = false;
        
        // кнопка создания начислений на основании стандартного расчета
        // оклад + премия + доплата по задачам + доплата часов
        // используются строки payout, где salary_id = 0
        if (!_salary && !salary) {
            var btn0 = document.createElement("A");
                btn0.classList.add('btn-add');
                //btn0.classList.add('btn-xs');
                btn0.classList.add('btn0-info');
                btn0.classList.add('pos-2');
                btn0.title = 'создать стандартные начисления';
                //btn0.innerHTML = '*';
                btn0.setAttribute('data-eid', arr[key]['eid']);
                btn0.setAttribute('data-sldate', arr[key]['sldate']);
                btn0.setAttribute('data-sfio', arr[key]['fio_short']);
                btn0.onclick = function(e) {
                    //alert('Завершено.');
                    //open_modal();
                    var salary = prompt('Введите оклад: ', 0);
                    var award = prompt('Введите премию: ', 0);
                    var summa = prompt('Введите стоимость за ед. задач: ', 300);
                    
                    if (!salary || !award || !summa) {
                        alert('Ошибка! Необходимо указать размер оклада, премии и стоимости ед. задач.');
                        e.stopPropagation();
                        return false;
                    }
                    
                    var sfio = this.getAttribute('data-sfio');
                    
                    if (confirm('Создать начисления для ' + sfio + ' с окладом: ' + salary + ', премией: ' + award + ' и стоимостью ед. задач: ' + summa + '?'))
                        createSalaryFromTemplate(this, salary, award, summa);
                    
//                    if (confirm('Начисления будут скопированы из прошлого месяца.\nСоздать начисления автоматически?'))
//                        copySalaryFromPrevMonth(this);
                    e.stopPropagation();
                };
        }
        else var btn0 = false;
       
        // кнопка блокировки записи
        var curr = new Date();
        var sldate = (curr.getFullYear()) * 100 + curr.getMonth() + 1;
        if (salary && salary['payment'] && !salary['block'] && (arr[key]['sldate'] < sldate)) {
        
            var btn1 = document.createElement("A");
                btn1.classList.add('btn-lock');
                //btn.classList.add('btn-xs');
                btn1.classList.add('btn0-success');
                btn1.classList.add('pos-2');
                btn0.title = 'заблокировать изменения';
                //btn1.innerHTML = '>';
                btn1.setAttribute('data-eid', arr[key]['eid']);
                btn1.setAttribute('data-sldate', arr[key]['sldate']);
                btn1.onclick = function(e) {
                    //if (confirm('Сгенерировать автоматически?')) alert('Завершено.');
                    //close_modal();
                    if (confirm('Данное действие выполняется при передаче данных по выплате в бухгалтерию.\nДальнейшее редактирование выплат будет невозможно.\nЗафиксировать начисления?'))
                        blockSalary(this);
                    e.stopPropagation();
                };
        }
        else var btn1 = false;
        
        
        
        // 4. добавляем строку в документ
        
    
        cnt++;
        
                
        
            td3.append(sp1);
            td3.append(sp2);
            tr.append(td1);
            tr.append(td2);
            tr.append(td3);
            tr.append(td4);
            tr.append(td5);
            if (btn) td6.append(btn);
            if (btn0) td6.append(btn0);
            if (btn1) td6.append(btn1);
            tr.append(td6)

            table.append(tr);
        
    }    

    show_hide_load(false);
}

function tr_click(self) {
    var eid = self.getAttribute('data-eid');
    var sldate = self.getAttribute('data-sldate');
    //open_close_modal();
    runAjax("/ajax/load-modal-form?eid=" + eid + "&sldate=" + sldate, loadModalForm);
    open_close_modal();
    show_date_on_sldate();
}

    
function sleep(miliseconds) {
   var currentTime = new Date().getTime();

   while (currentTime + miliseconds >= new Date().getTime()) {
   }
}
function ajax(url, id) {
    
    var req = new XMLHttpRequest();
    
    req.onreadystatechange = function() {
        if (req.readyState == XMLHttpRequest.DONE) {   // XMLHttpRequest.DONE == 4
            if (req.status == 200) {
                
                document.getElementById(id).innerHTML = req.responseText;
                //alert(req.responseText);
           }
           else if (req.status == 400) {
              alert('There was an error 400');
           }
           else {
               alert('something else other than 200 was returned');
           }
        }
        return '';
    };
    
    req.open("GET", url, true);
    req.send();
    
}

function loadSalary() {
    
    var did = document.getElementById('did').value;
    if (did == "X") return;
    
    show_hide_load();

    var month = document.getElementById('month').value;
    var year = document.getElementById('year').value;
    
    var link = document.getElementById('getDownloadLink');
    var load = document.getElementById('loadExcelFile');
    link.style.display = 'block';
    load.style.display = 'none';
    
    if (month == "0") month = "03"; // проверка в принципе не нужна, так как это должно заполняться из php
    if (year == "0") year = "2019"; // проверка в принципе не нужна, так как это должно заполняться из php
    runAjax("/ajax/get-salary-for-month?month=" + month + "&year=" + year, loadTable);

}


window.onload = function() {
    
//var month = document.getElementById('month').value;
//var year = document.getElementById('year').value;

//ajax("/ajax/map-near-house?xcor=52.339928&ycor=35.346035", "myDiv1");
//ajax("/ajax/get-salary-for-month?month=01&year=2019", "myDiv");

//runAjax("/ajax/map-near-house?xcor=52.339928&ycor=35.346035", ajax1);
//runAjax("/ajax/map-near-house?xcor=52.339928&ycor=35.346035", ajax2);
loadSalary();

};

function getSalaryInfo(salary, report) {
    
    var result = new Array();
        
        //result['detail']   = '';        // строка детальной расшифровки начислений
        result['base']     = 0;         // оклад для вывода
        result['award']    = 0;         // премия исх для вывода
        result['ext']      = 0;         // премия доп для вывода
        result['total']    = 0;         // итого для вывода
        result['title']    = '';        // строка детальной расшифровки начислений
        result['footer']   = '';        // строка сноска по начислениям <s1><s2><s3>
        
        
        result['payment']  = false;     // наличие платежей
        result['block']    = false;     // блокировка записи
        
//        payment = true;

        var id= salary['id'];
        var eid = salary['eid'];

        var sldate = salary['sldate'];
        var bs_pay = salary['payment'];
        var award = salary['award'];

        if (salary['block'] == 1) result['block'] = true;

        // нужны еще параметры переработки, ко-во единиц, общее отработанное время и т.д.

        var _did = report['did'];
        var phour = report['phour'];
        var whour = report['whour'];
        var _shour = report['_shour'];
        var cost = Math.ceil(report['cost'] * 100) / 100;

        for (var pay in salary['payout']) {
            
            result['payment'] = true;

            var base = salary['payout'][pay]['base'];
            var depends = salary['payout'][pay]['depends'];
            var type = salary['payout'][pay]['type'];
            var info = salary['payout'][pay]['info'];

            switch(base) {
                case 'salary': // основание "оклад"
                    switch(depends) {
                        case 'shiftcount': // коэффициент "количество смен" (отработанное время / рабочее время за месяц по табелю)
                            result['base'] = result['base'] + Math.ceil(bs_pay * whour / phour);
                            result['title'] = result['title'] + info + ': ' + bs_pay + '*' + whour + '/' + phour + ' = ' + Math.ceil(bs_pay * whour / phour) + '\n';
                        break;
                        case 'overtime': // коэффициент "переработка"
                            result['ext'] = result['ext'] + Math.ceil(bs_pay * _shour / phour);
                            result['title'] = result['title'] + info + ': ' + bs_pay + '*' + _shour + '/' + phour + ' = ' + Math.ceil(bs_pay * _shour / phour) + '\n';
                        break;
                        case 'taskcount': // коэффициент "количество задач"
                            result['ext'] = result['ext'] + Math.ceil(cost * bs_pay / phour);
                            result['title'] = result['title'] + info + ': ' + bs_pay + '*' + cost + '/' + phour + ' = ' + Math.ceil(bs_pay * cost / phour) + '\n';
                        break;
                        case 'fixed': // коэффициент "фиксированная сумма" (не зависит от коэффициента)
                            result['ext'] = result['ext'] + bs_pay;
                            result['title'] = result['title'] + info + ': ' + bs_pay  + '\n';
                        break;
                        default: 
                            //alert('default');
                        break;
                    }

                break;

                case 'award': // основание "премия"
                    switch(depends) {
                        case 'shiftcount': // коэффициент "количество смен" (отработанное время / рабочее время за месяц по табелю)
                            result['award'] = result['award'] + Math.ceil(award * whour / phour);
                            result['title'] = result['title'] + info + ': ' + award + '*' + whour + '/' + phour + ' = ' + Math.ceil(award * whour / phour) + '\n';
                        break;
                        case 'overtime': // коэффициент "переработка"
                            result['ext'] = result['ext'] + Math.ceil(award * _shour / phour);
                            result['title'] = result['title'] + info + ': ' +  Math.ceil(award * _shour / phour) + '\n';
                        break;
                        case 'taskcount': // коэффициент "количество задач" - количество задач умноженное на среднечасовой оклад
                            result['ext'] = result['ext'] + Math.ceil(award * cost / phour); //award / phour;
                            result['title'] = result['title'] + info + ': ' + award + '*' + cost + '/' + phour + ' = ' + Math.ceil(award * cost / phour) + '\n';
                        break;
                        case 'fixed': // коэффициент "фиксированная сумма" (не зависит от коэффициента)
                            result['ext'] = result['ext'] + award * 1;
                            result['title'] = result['title'] + info + ': ' + award + '\n';
                        break;
                        default: 

                        break;
                    }
                break;

                case 'total': // основание "итоговая сумма"
                    switch(depends) {
                        case 'shiftcount': // коэффициент "" (отработанное время / рабочее время за месяц по табелю)

                        break;
                        case 'overtime': // коэффициент ""

                        break;
                        case 'taskcount': // коэффициент ""

                        break;
                        case 'fixed': // коэффициент ""

                        break;
                        default: 

                        break;
                    }
                break;

                default:
                    var idx = base.indexOf('summa=');
                    if (idx != -1) { // ищем основание "summa=" в строке
                        var summa = base.substr(idx + 6);
                        switch(depends) {
                            case 'shiftcount': // коэффициент "" (отработанное время / рабочее время за месяц по табелю)
                                result['ext'] = result['ext'] + Math.ceil(summa * whour / phour);
                                result['title'] = result['title'] + info + ': ' + summa + '*' + whour + '/' + phour + ' = ' + Math.ceil(summa * whour / phour) + '\n';
                            break;
                            case 'overtime': // коэффициент ""
                                result['ext'] = result['ext'] + Math.ceil(_shour * summa / phour);
                                result['title'] = result['title'] + info + ': ' + summa + '*' + _shour + '/' + phour + ' = ' + Math.ceil(summa * _shour / phour) + '\n';
                            break;
                            case 'taskcount': // коэффициент ""
                                result['ext'] = result['ext'] + Math.ceil(cost * summa);
                                result['title'] = result['title'] + info + ': ' + summa + '*' + cost + ' = ' + Math.ceil(summa * cost) + '\n';
                            break;
                            case 'fixed': // коэффициент ""
                                result['ext'] = result['ext'] + summa * 1;
                                result['title'] = result['title'] + info + ': ' + summa + '\n';
                            break;
                            default: 

                            break;
                        }
                    }

                break;
            }
        }

    result['total'] = Math.ceil((result['base'] + result['award'] + result['ext']) / 10) * 10;
    //result['footer'] = 'ОКЛАД: ' + result['base'] + ' ПРЕМИЯ: ' + result['award'] + ' ПООЩРЕНИЕ: ' + result['ext'];
    return result;
}

function onCopySalary(xhttp) {
    var rsp = xhttp.responseText;
    alert(rsp);
    loadSalary();
}

function copySalaryFromPrevMonth(self) {
    var eid = self.getAttribute('data-eid');
    var sldate = self.getAttribute('data-sldate');
    runAjax("/ajax/copy-salary-from-prev-month?eid=" + eid + "&sldate=" + sldate, onCopySalary);
}

function blockSalary(self) {
    var eid = self.getAttribute('data-eid');
    var sldate = self.getAttribute('data-sldate');
    runAjax("/ajax/block-salary?eid=" + eid + "&sldate=" + sldate, onCopySalary);
}

function createSalaryFromTemplate(self, salary, award, summa) {
    var eid = self.getAttribute('data-eid');
    var sldate = self.getAttribute('data-sldate');
    runAjax("/ajax/create-salary-from-template?eid=" + eid + "&sldate=" + sldate + '&salary=' + salary + '&award=' + award + '&summa=' + summa, onCopySalary);
}

function showLinkExcel(xhttp) {
    show_hide_load(false);
    var link = document.getElementById('getDownloadLink');
    var load = document.getElementById('loadExcelFile');
    link.style.display = 'none';
    load.style.display = 'block';
    var atag = load.getElementsByTagName('A')[0];
    if (xhttp.responseText < 100) {
        atag.setAttribute('href', '#');
        atag.innerHTML = 'Файл не сформирован';
    }
    else {
        atag.setAttribute('href', xhttp.responseText);
        atag.innerHTML = 'Скачать файл';
    }
    
    
    //link.innerHTML = xhttp.responseText;
    
}


// запуск создания файла отчета и получение ссылки
function getExcelFileLink() {
    
    var month = document.getElementById('month').value;
    var year = document.getElementById('year').value;
    //var myDiv = document.getElementById('myDiv');
    var rows = document.getElementsByClassName('tbl-row');
    
    var eids = new Array();
    
    for (i = 0; i < rows.length; i++) {
        
        eids[i] = rows[i].getAttribute('data-eid');
    }
    var link = document.getElementById('getDownloadLink');
    var load = document.getElementById('loadExcelFile');
    link.style.display = 'block';
    load.style.display = 'none';
    show_hide_load();
    
    runAjax('/ajax/get-salary-load-excel-link?month=' + month + '&year=' + year + '&eids=' + JSON.stringify(eids), showLinkExcel);
    //myDiv.innerHTML = '/ajax/get-salary-load-excel-link?month=' + month + '&year=' + year + '&eids=' + JSON.stringify(eids);
}

// запуск создания файла отчета с запросом даты начала и окончания и получение ссылки
function getExcelFileLinkOnPeriod(event) {
    var start = prompt('Укажите дату начала отчета.');
    var finish = prompt('Укажите дату окончания запроса.');
    
    var month = document.getElementById('month').value;
    var year = document.getElementById('year').value;
    //var myDiv = document.getElementById('myDiv');
    var rows = document.getElementsByClassName('tbl-row');
    
    var eids = new Array();
    
    for (i = 0; i < rows.length; i++) {
        
        eids[i] = rows[i].getAttribute('data-eid');
    }
    var link = document.getElementById('getDownloadLink');
    var load = document.getElementById('loadExcelFile');
    link.style.display = 'block';
    load.style.display = 'none';
    show_hide_load();
    
    runAjax('/ajax/get-salary-load-excel-link?month=' + month + '&year=' + year + '&start=' + start + '&finish=' + finish + '&eids=' + JSON.stringify(eids), showLinkExcel);
    event.preventDefault();
}

</script>

<!--    <tr class="tbl-row">
        <td>1</td>
        <td>18</td>
        <td style="font-size: 16px;">Найдин В.В.
            <span>начальник отдела</span>
            <span>ОКЛАД: <s1>NNNNN</s1>, ПРЕМИЯ: <s2>NNNNN</s2>, ПООЩРЕНИЕ: <s3>NNNNN</s3></span>
        </td>
        <td>175</td>
        <td>NNNNN</td>
    </tr>
    <tr class="tbl-row">
        <td>2</td>
        <td>1</td>
        <td style="font-size: 16px;">Зенкин А.С.
            <span>радиомонтажник (сварка оптоволокна)</span>
            <span>ОКЛАД: <s1>NNNNN</s1>, ПРЕМИЯ: <s2>NNNN</s2>, ПООЩРЕНИЕ: <s3>NNNNN</s3></span>
        </td>
        <td>175</td>
        <td>NNNNN</td>
    </tr>
    <tr class="tbl-row">
        <td>3</td>
        <td>1</td>
        <td style="font-size: 16px;">Ситников Р.С.
            <span>радиомонтажник (сварка оптоволокна)</span>

            <span>ОКЛАД: <s1>NNNNN</s1>, ПРЕМИЯ: <s2>NNNN</s2>, ПООЩРЕНИЕ: <s3>NNNNN</s3></span>
        </td>
        <td>175</td>
        <td>NNNNN</td>
    </tr>
    <tr class="tbl-row">
        <td>4</td>
        <td>1</td>
        <td style="font-size: 16px;">Филимонов Н.
            <span>радиомонтажник (водитель)</span>
            <span>ОКЛАД: <s1>NNNNN</s1>, ПРЕМИЯ: <s2>NNNN</s2>, ПООЩРЕНИЕ: <s3>NNNNN</s3></span>
        </td>
        <td>175</td>
        <td>NNNNN</td>
    </tr>
    <tr class="tbl-row">
        <td>5</td>
        <td>1</td>
        <td style="font-size: 16px;">Филиппов Н.
            <span>радиомонтажник</span>
            <span>ОКЛАД: <s1>NNNNN</s1>, ПРЕМИЯ: <s2>NNNN</s2>, ПООЩРЕНИЕ: <s3>NNNNN</s3></span>
        </td>
        <td>175</td>
        <td>NNNNN</td>
    </tr>
    <tr class="tbl-row">
        <td>6</td>
        <td>1</td>
        <td style="font-size: 16px;">Кусков М.
            <span>радиомонтажник</span>
            <span>ОКЛАД: <s1>NNNNN</s1>, ПРЕМИЯ: <s2>NNNN</s2>, ПООЩРЕНИЕ: <s3>NNNNN</s3></span>
        </td>
        <td>175</td>
        <td>NNNNN</td>
    </tr>
    <tr class="tbl-row">
        <td>7</td>
        <td>1</td>
        <td style="font-size: 16px;">Гончаров А.
            <span>бригадир</span>
            <span style="display: block; ">ОКЛАД: <s1>NNNNN</s1>, ПРЕМИЯ: <s2>NNNNN</s2>, ПООЩРЕНИЕ: <s3>NNNNN</s3></span>
            <div style="width: calc(100% + 176px);">
                <table class="no-border">
                    <tr>
                        <td>Оклад</td>
                        <td>см.</td>
                        <td>NNNNN* 175 / 175</td>
                        <td>NNNNN</td>
                    </tr>
                    <tr>
                        <td>Премия исходная</td>
                        <td>см.</td>
                        <td>NNNN * 175 / 175</td>
                        <td>NNNN</td>
                    </tr>
                    <tr>
                        <td>Поощрение</td>
                        <td>см.</td>
                        <td>NNNNN * 175 / 175</td>
                        <td>NNNNN</td>
                    </tr>
                    <tr>
                        <td>Переработка</td>
                        <td>час.</td>
                        <td>NNN * 8</td>
                        <td>NNN</td>
                    </tr>
                </table>
            </div>
        </td>
        <td>175</td>
        <td>NNNNN</td>
    </tr>
-->
<template id="tmp-row" >
    <tr class="modal-table-row">
        <td>
            <input type="text" class="form-control" placeholder="Название выплаты" />
        </td>
        <td>
            <select class="form-control" style="width: 55%;" onchange="calcModalSalary(); show_hide_summa(this); ">
                <option value="salary">Оклад</option>
                <option value="award">ПремияИсх</option>
                <option value="summa">Сумма</option>
            </select>
            <input type="number" class="form-control" style="width: 35%; display: none; " value="0" onchange="calcModalSalary(); "/>
        </td>
        <td>
            <select  class="form-control" onchange="calcModalSalary(); ">
                <option value="shiftcount">Отработанных часов</option>
                <option value="overtime">Часов переработки</option>
                <option value="taskcount">Числа задач</option>
                <option value="fixed">Фиксированная</option>
            </select>
        </td>
        <td>
            <select class="form-control">
                <option value="regular">Ежемесячно</option>
                <option value="onetime">Однократно</option>
            </select>
        </td>
        <td></td>
        <td>
            <a class="btn-delete btn0-danger" onclick="removeModalTableRow(this); "></a>
        </td>
    </tr>
</template>
