<style>
.rep-column {
    width: 52px;
}
.ws-nowrap {
    white-space: nowrap;
}
td, th {
    border-right: 1px solid #ddd;
}
td:last-child, th:last-child {
    border-right: none;
}
td p {
    font-size: 12px;
    color: #999999;
    margin-bottom: 1px;
    line-height: 12px;
}
.hlow {
    color: green;
    font-weight: 600;
}
.hhigh {
    color: red;
}
</style>

<center>
<?php use yii\helpers\Html; ?>
<?php use Yii; ?>
<?php use PHPExcel; ?>
    
<div class="row">
    <div class="col-md-9">
        <h3>Отчёт по сотрудникам</h3>
        <?php $this->title = "Сигнал: Отчёт по сотрудникам" ?>
        <?= Html::beginForm(['/report/index'], 'post', ['class' => 'form-vertical']) ?>
        <label class="control-label" >Начальная дата<input id="start" class="form-control" type="date" name="start" value="<?= isset($start) ? $start : date("Y-m-01") ?>" required></label>
        <label class="control-label" >Дата окончания<input id="finish" class="form-control" type="date" name="finish" value="<?= isset($finish) ? $finish : date("Y-m-t") ?>" required></label>
        <?php 
        if ((Yii::$app->user->identity->role[4] == 'f') || (Yii::$app->user->id == 2)) { ?>
        <label class="control-label" > Выбрать отдел
            <select name="did" class="form-control">
            <option value="0">Все отделы</option>
            <?php 
            if (isset($did)) { ?>
            <option <?= $did == 1 ? ' selected ' : '' ?> value="1">Монтажники</option>
            <?php if (Yii::$app->user->id != 2) { ?><option <?= $did == 2 ? ' selected ' : '' ?> value="2">Линейщики</option><?php } ?>
            <option <?= $did == 5 ? ' selected ' : '' ?> value="5">Техподдержка</option>
            <option <?= $did == 6 ? ' selected ' : '' ?> value="6">Абонентский</option>
            <?php }
            else { ?>
            <option value="1">Монтажники</option>
            <?php if (Yii::$app->user->id != 2) { ?><option value="2">Линейщики</option><?php } ?>
            <option value="5">Техподдержка</option>
            <option value="6">Абонентский</option>
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
<?= isset($start) ? '<h3>Отчёт по сотрудникам за период</h3><br><h3>'.ShowDate($start).' по '.ShowDate($finish).'</h3>' : '' ?>
<?php if (isset($emplist) && (count($emplist['report']) > 0)) { ?>
    <?php $No = 1; ?>
    <?php
        $phpexcel = new PHPExcel(); // Создаём объект PHPExcel
        /* Каждый раз делаем активной 1-ю страницу и получаем её, потом записываем в неё данные */
        $page = $phpexcel->setActiveSheetIndex(0); // Делаем активной первую страницу и получаем её
        //$phpexcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);// Устанавливаем автоматическую ширину колонки
        //$phpexcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $phpexcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $phpexcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        //$phpexcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        //$phpexcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $page->setTitle(ShowDigiDate($start).' - '.ShowDigiDate($finish)); // Ставим заголовок на странице
        $arHeadStyle = array(
            'font'  => array(
                //'bold'  => true,
                //'color' => array('rgb' => '778899'),
                'size'  => 13,
                //'name'  => 'Verdana'
        ));
        $page->getStyle('A1')->applyFromArray($arHeadStyle);
        $page->getStyle('B1')->applyFromArray($arHeadStyle);
        $page->getStyle('C1')->applyFromArray($arHeadStyle);
        $page->getStyle('D1')->applyFromArray($arHeadStyle);
        $page->getStyle('E1')->applyFromArray($arHeadStyle);
        $page->getStyle('F1')->applyFromArray($arHeadStyle);
        $page->getStyle('G1')->applyFromArray($arHeadStyle);
        $page->getStyle('H1')->applyFromArray($arHeadStyle);
        $page->getStyle('I1')->applyFromArray($arHeadStyle);
        $page->getStyle('J1')->applyFromArray($arHeadStyle);
        $page->getStyle('K1')->applyFromArray($arHeadStyle);
        $page->getStyle('L1')->applyFromArray($arHeadStyle);
        $page->getStyle('M1')->applyFromArray($arHeadStyle);
        $page->getStyle('N1')->applyFromArray($arHeadStyle);



    ?>
    <table class="table table-striped table-condensed">
        <thead>
        <th title="Номер по порядку">
            №<?php $page->setCellValue("A1", "№"); // Добавляем в ячейку A1 "№" ?>
        </th>
        <th title="Номер по базе данных">
            Id<?php $page->setCellValue("B1", "Id"); // Добавляем в ячейку B1 "Id" ?>
        </th>
        <th title="">
            Фамилия И.О.<?php $page->setCellValue("C1", "Фамилия И.О."); // Добавляем в ячейку C1 "Фамилия И.О." ?>
        </th>    
        <th title="Количество 8-ми часовых смен">
            8час<?php $page->setCellValue("D1", "8час"); ?>
        </th>
        <th title="Количество 12-ти часовых смен">
           2х2<?php $page->setCellValue("E1", "2х2"); ?>
        </th>
        <th title="Количество 12-ти часовых смен">
            День<?php $page->setCellValue("F1", "День"); ?>
        </th>
        <th title="Количество 12-ти часовых смен">
            Ночь<?php $page->setCellValue("G1", "Ночь"); ?>
        </th>
        <th title="Дней отпуска">
            Отпуск<?php $page->setCellValue("H1", "Отп"); ?>
        </th>
        <th title="Дней отпуска по ЧАЭС">
            ЧАЭС<?php $page->setCellValue("I1", "ЧАЭС"); ?>
        </th>
        <th title="Дней по заявлению">
            Заявл<?php $page->setCellValue("J1", "Заявл"); ?>
        </th>
        <th title="Дней по больничному листу">
            Больн<?php $page->setCellValue("K1", "Больн"); ?>
        </th>
        <th title="Дней компенсации">
            Комп<?php $page->setCellValue("L1", "Комп"); ?>
        </th>
        <th>Считать по</th>
        <th title="Часов по табелю или фактически" class="ws-nowrap">
            Часов<?php $page->setCellValue("M1", "Часов"); // Добавляем в ячейку D1 "Час" ?>
        </th>
        <th title="Часов переработки + праздничных + компенсация">
            Перераб<?php $page->setCellValue("N1", "Перераб"); // Добавляем в ячейку D1 "Час" ?>
        </th>
        <th title="Единиц измерения стоимости работ">
            ЕИС<?php $page->setCellValue("O1", "ЕИС"); // Добавляем в ячейку E1 "ЕИС" ?>
        </th>
        </thead>
    <?php
//                $page->setCellValue("A1", "Hello"); // Добавляем в ячейку A1 слово "Hello"
//                $page->setCellValue("A2", "World!"); // Добавляем в ячейку A2 слово "World!"
//                $page->setCellValue("B1", "MyRusakov.ru"); // Добавляем в ячейку B1 слово "MyRusakov.ru"
    ?>
    <?php foreach ($emplist['report'] as $item) { ?>
    <?php     //if ($item['hour'] == 0) continue; ?>
    <tr>
        <td class="rep-column"><!-- № пункта -->
            <?= $No ?><?php $page->setCellValue("A".($No + 1), $No); ?>
        </td>
        <td  class="rep-column">
            <?= $item['eid'] ?><?php $page->setCellValue("B".($No + 1), $item['eid']); ?>
        </td>
        <td style="width: 20%;">
            <?php
                $comment = '';
                if ($item['shift'] == 'О') $comment = ' (отпуск)';
                if ($item['shift'] == 'Ч') $comment = ' (ЧАЭС)';
                if ($item['shift'] == 'Х') $comment = ' (уволен)';
                if ($item['shift'] == 'Б') $comment = ' (больничный)';
            ?>
            <a href="/report/<?= $item['eid'] ?>/<?= $start ?>/<?= $finish ?>" target="_blank"><?= $item['sfio'].$comment ?></a><?php $page->setCellValue("C".($No + 1), $item['sfio']); ?>
            <p><?= $item['post'] ?></p>
        </td>
        <td class="rep-column">
            <?php
                // определяем вывод смен и рабочее время по сменам
                $ttime = 0; $s08 = ''; $s12 = ''; $sDn = ''; $sNc = ''; $sOt = ''; $sCA = ''; $sZv = ''; $sBl = ''; $sKm = '';




                foreach ($item['table'] as $key => $table) {

                    if ($key == '8') { $s08 = $table; $page->setCellValue("D".($No + 1), $table); $ttime = $ttime + (8 * $table); }
                    if ($key == 'С') { $s12 = $table; $page->setCellValue("E".($No + 1), $table); $ttime = $ttime + (12 * $table); }
                    if ($key == 'Д') { $sDn = $table; $page->setCellValue("F".($No + 1), $table); $ttime = $ttime + (12 * $table); }
                    if ($key == 'Н') { $sNc = $table; $page->setCellValue("G".($No + 1), $table); $ttime = $ttime + (12 * $table); }
                    if ($key == 'О') { $sOt = $table; $page->setCellValue("H".($No + 1), $table); }
                    if ($key == 'Ч') { $sCA = $table; $page->setCellValue("I".($No + 1), $table); }
                    if ($key == 'З') { $sZv = $table; $page->setCellValue("J".($No + 1), $table); }
                    if ($key == 'Б') { $sBl = $table; $page->setCellValue("K".($No + 1), $table); }
                    if ($key == 'К') { $sKm = $table; $page->setCellValue("L".($No + 1), $table); }
                    //if ($key == '_') { $shift = $shift.'__='.$table.'; ';
                }
            //$page->setCellValue("D".($No + 1), $shift);
            ?>
            <?= $s08//$item['tids'] ?>
        </td>
        <td class="rep-column">
            <?= $s12 ?>
        </td>
        <td class="rep-column">
            <?= $sDn ?>
        </td>
        <td class="rep-column">
            <?= $sNc ?>
        </td>
        <td class="rep-column">
            <?= $sOt ?>
        </td>
        <td class="rep-column">
            <?= $sCA ?>
        </td>
        <td class="rep-column">
            <?= $sZv ?>
        </td>
        <td class="rep-column">
            <?= $sBl ?>
        </td>
        <td class="rep-column">
            <?= $sKm ?>
        </td>
        <td><?= $item['tab_task'] ? '<i>задачам</i>' : 'табелю' ?></td>
        <?php
            // вычисляем и выводим отработаные часы по табелю
            if ($item['tab_task'] == 1) {
                //echo $item['whour'];
                $hour = $item['whour'];
            }
            else {
                //echo $item['phour'];
                $hour = $item['whour'];
            }
        ?>
        <td class="rep-column<?= ($hour < $item['phour']) ? ' hhigh' : (($hour > $item['phour']) ? ' hlow' : '') ?>">
            <?= $hour ?>
            <?php $page->setCellValue("M".($No + 1), $hour); ?>
        </td>
        <?php
//            // вычисляем и выводим переработки, праздничные и компенсации
//            if ($item['tab_task'] == 1) {
//                //echo $item['whour'];
//                $hour = $item['hour'] - $item['phour'] + $item['shour'];
//            }
//            else {
//                //echo $item['phour'];
//                $hour = $item['whour'] - $item['phour'] + $item['shour'];
//            }
            $hour = $item['_shour'];
        ?>
        <td class="rep-column<?= ($hour < 0) ? ' hhigh' : (($hour > 0) ? ' hlow' : '') ?>">
            
            <?= $hour ?>
            <?php $page->setCellValue("N".($No + 1), $hour); ?>
        </td>
        <td class="rep-column">
            <?= $item['cost'] != 0 ? number_format($item['cost'], 2) : '0' ?>
            <?php $page->setCellValue("O".($No + 1), ($item['cost'] != 0 ? number_format($item['cost'], 2) : 0)); ?>
            <?php // (((($item['cost'] * 230) + 20000) * 0.87), 2) : '0' ?>
        </td>
        <?php
        $No++;
        }
        
        // выводим информацию о продолжительности рабочего времени за период
        $htotal = 0;
        foreach ($emplist->days as $day) {
            if ($day[0] == '0') $htotal = $htotal + 8;
            if ($day[0] == '2') $htotal = $htotal + 7;
        }
        $page->setCellValue("C".($No + 3), 'Часов по производственному календарю за период:');
        $page->setCellValue("M".($No + 3), $htotal);
        
        
           /* Начинаем готовиться к записи информации в xlsx-файл */
            $objWriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');
            /* Записываем в файл */
            $xls = "report_".date("YmdHis", time() + 3600 * 3).".xlsx";
            $objWriter->save("../web/files/docs/".$xls);
        ?>

    </tr>
    </table>
    <a href="/web/files/docs/<?= $xls ?>"><?= $xls ?></a>
    <hr>
    Запрос выполнен за: <i><?= number_format($emplist->speed, 5) ?></i> сек.

<?php } ?>
</center>
<?php if (isset($emplist) && (Yii::$app->user->id == 1)) { ?><pre><?php var_dump($emplist); ?></pre><?php } ?>

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
    
window.onload=function(){
/* ваш код */


}
</script>