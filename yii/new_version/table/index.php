<style>
    .container {
        width: 90%;
    }
    input.day, span.day {
        width: 24px;
        height: 24px;
        margin: 2px 2px 0 0;
        padding: 2px;
        border: 1px solid #cccccc;
        float: left;
        text-align: center;
        text-transform: uppercase;
        font-weight: 400;
    }
    td p {
        font-size: 12px;
        color: #999999;
        margin-bottom: 1px;
        line-height: 12px;
    }
    td {
        padding: 0 5px !important;
    }
    .days-tips .non-month {
        background-color: #cccccc;
        border-color: #666666;
        color: #ffffff;
    }
    .days-tips .celebration {
        background-color: #ff9999;
    }
    
    .no-float-day div {
        /* float: left; */
        line-height: 28px;
        height: 28px;
    }
    
    .no-float-day {
        width: 20%;
        float: left;
    }
    
    .no-float-day div span {
        line-height: 20px;
    }
    
    .employe-row .non-month {
        background-color: #eeeeee;
        border-color: #666666;
    }
    .hday {
        border-color: #ff0000 !important;
        color: #ff0000;
    }
    .shortday {
        border-color: #009900 !important;
    }
    .today {
        font-weight: 600 !important;
        color: #003eff;
        text-decoration: underline;
    }
    .ts-comment {
        border-radius: 11px;
    }
    .row-comment span {
        font-weight: bold;
        color: #0081c2;
    }
    
</style>
<center>
<?php use yii\helpers\Html; ?>
<?php use PHPExcel; ?>
    
<?php $this->title = "Сигнал: Табель рабочего времени" ?>
<?= Html::beginForm(['/table/index'], 'post', ['class' => 'form-vertical']) ?>
<input type="hidden" name="action" value="view">
<label class="control-label" >
    Месяц
    <select name='month' class="form-control">
        <option disabled selected value="0">Не выбран</option>
        <option <?= $table->calendar->month == 1 ? ' selected ' : '' ?> value="01">Январь</option>
        <option <?= $table->calendar->month == 2 ? ' selected ' : '' ?> value="02">Февраль</option>
        <option <?= $table->calendar->month == 3 ? ' selected ' : '' ?> value="03">Март</option>
        <option <?= $table->calendar->month == 4 ? ' selected ' : '' ?> value="04">Апрель</option>
        <option <?= $table->calendar->month == 5 ? ' selected ' : '' ?> value="05">Май</option>
        <option <?= $table->calendar->month == 6 ? ' selected ' : '' ?> value="06">Июнь</option>
        <option <?= $table->calendar->month == 7 ? ' selected ' : '' ?> value="07">Июль</option>
        <option <?= $table->calendar->month == 8 ? ' selected ' : '' ?> value="08">Август</option>
        <option <?= $table->calendar->month == 9 ? ' selected ' : '' ?> value="09">Сентябрь</option>
        <option <?= $table->calendar->month == 10 ? ' selected ' : '' ?> value="10">Октябрь</option>
        <option <?= $table->calendar->month == 11 ? ' selected ' : '' ?> value="11">Ноябрь</option>
        <option <?= $table->calendar->month == 12 ? ' selected ' : '' ?> value="12">Декабрь</option>
    </select>
</label>
<label class="control-label" >
    Год
    <select name='year' class="form-control">
        <option disabled selected value="0">Не выбран</option>
        <option <?= $table->calendar->year == 2020 ? ' selected ' : '' ?> value="2020">2020</option>
        <option <?= $table->calendar->year == 2019 ? ' selected ' : '' ?> value="2019">2019</option>
        <option <?= $table->calendar->year == 2018 ? ' selected ' : '' ?> value="2018">2018</option>
        <option <?= $table->calendar->year == 2017 ? ' selected ' : '' ?> value="2017">2017</option>
    </select>
</label>
<?php 

if (Yii::$app->user->identity->role[5] != 'x') { ?>
<label class="control-label" > Выбрать отдел
    <select name="did" class="form-control">
        <!-- <option disabled selected value="0">Не выбран</option> -->
        <option <?= $table->did == 1 ? ' selected ' : '' ?> value="0">Все сотрудники</option>
        <option <?= $table->did == 1 ? ' selected ' : '' ?> value="1">Монтажники</option>
        <option <?= $table->did == 2 ? ' selected ' : '' ?> value="2">Линейщики</option>
        <!-- <option <?= ''//$table->did == 3 ? ' selected ' : '' ?> value="3">Ремонтники</option> -->
        <option <?= $table->did == 5 ? ' selected ' : '' ?> value="5">Техподдержка</option>
        <?php if (Yii::$app->user->id != 3) { ?><option <?= $table->did == 6 ? ' selected ' : '' ?> value="6">Абонентский</option><?php } ?>
    </select>
</label>
<?php } ?>
<button type="submit" class="btn btn-success">Показать</button>
<?= Html::endForm() ?>
<hr>
<!-- здесь будет форма табеля -->
<h3>Табель рабочего времени за <?= ShowMonthYear($table->calendar->first) ?></h3>
<?= ''//$stop ?>
<?= Html::beginForm(['/table/save'], 'post', ['class' => '']) ?>
<input type="hidden" name="action" value="save">
<input type="hidden" name="month" value="<?= $table->calendar->month ?>">
<input type="hidden" name="year" value="<?= $table->calendar->year ?>">
<input type="hidden" name="did" value="<?= $table->did ?>">


<input type="hidden" name="first" value="<?= $table->calendar->first ?>">
<input type="hidden" name="last" value="<?= $table->calendar->last ?>">

<?php
    $phpexcel = new PHPExcel(); // Создаём объект PHPExcel
    /* Каждый раз делаем активной 1-ю страницу и получаем её, потом записываем в неё данные */
    $page = $phpexcel->setActiveSheetIndex(0); // Делаем активной первую страницу и получаем её
    $phpexcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);// Устанавливаем автоматическую ширину колонки
    $phpexcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    //$phpexcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    //$phpexcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    //$phpexcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
//    $phpexcel->getActiveSheet()->getColumnDimension('C')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('D')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('E')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('F')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('G')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('H')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('I')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('J')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('K')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('L')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('M')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
//    $phpexcel->getActiveSheet()->getColumnDimension('O')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('P')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('Q')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('R')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('S')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('T')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('U')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('V')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('W')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('X')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('Y')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('Z')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('AA')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('AB')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('AC')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('AD')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('AE')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('AF')->setWidth(4);
//    $phpexcel->getActiveSheet()->getColumnDimension('AG')->setWidth(4);
    
    
    
    
    
    $arDocFont = array(
        'font'  => array(
            //'bold'  => true,
            //'color' => array('rgb' => '778899'),
            //'size'  => 13,
            'name'  => 'Arial'
        ),
    );
    
    
    $page->setTitle($table->calendar->year.'_'.$table->calendar->month.'_'.getDepTitle($table->did, true)); // Ставим заголовок на странице
    $page->getDefaultStyle()->applyFromArray($arDocFont);
    
    $arHeadStyle = array(
        'font'  => array(
            'bold'  => true,
            //'color' => array('rgb' => '778899'),
            //'size'  => 13,
            //'name'  => 'Verdana'
        ),
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            )
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'bbbbbb')
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            //'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
    );
    $arCellHDay = array(
//        'fill' => array(
//            'type' => PHPExcel_Style_Fill::FILL_SOLID,
//            'color' => array('rgb' => 'FF0000')
//        ),
//        'borders' => array(
//            'allborders' => array(
//                'style' => PHPExcel_Style_Border::BORDER_THIN,
//                'color' => array('rgb' => 'FF0000')
//            )
//        ),
        'font'  => array(
            'color' => array('rgb' => 'dd1111'),
            //'size'  => 13,
            //'bold'  => true,
        )
    );
    
    $arCellSDay = array(
//        'fill' => array(
//            'type' => PHPExcel_Style_Fill::FILL_SOLID,
//            'color' => array('rgb' => '00FF00')
//        ),
//        'borders' => array(
//            'allborders' => array(
//                'style' => PHPExcel_Style_Border::BORDER_THIN,
//                'color' => array('rgb' => 'FF0000')
//            )
//        ),
        'font'  => array(
            'color' => array('rgb' => '009900'),
            //'size'  => 13,
            //'bold'  => true,
        )
    );
    
    $arCellWDay = array(
//        'fill' => array(
//            'type' => PHPExcel_Style_Fill::FILL_SOLID,
//            'color' => array('rgb' => '00FF00')
//        ),
//        'borders' => array(
//            'allborders' => array(
//                'style' => PHPExcel_Style_Border::BORDER_THIN,
//                'color' => array('rgb' => 'FF0000')
//            )
//        ),
        'font'  => array(
            'color' => array('rgb' => '333333'),
            //'color' => array('rgb' => '666666'),
            //'size'  => 13,
            //'bold'  => true,
        )
    );
    
    $arCell8Hour = array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '56a8ef')
        ),
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            )
        ),
    );
    $arCell12Hour = array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'ffa500')
        ),
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => 'FFFFFF')
            )
        ),
        'font'  => array(
            'color' => array('rgb' => 'ffffff'),
            //'size'  => 13,
            //'bold'  => true,
        ),
    );
    $arCellDay = array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '008000')
        ),
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => 'FFFFFF')
            )
        ),
        'font'  => array(
            'color' => array('rgb' => 'ffffff'),
            //'size'  => 13,
            //'bold'  => true,
        ),
    );
    $arCellNight = array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '0000ff')
        ),
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => 'FFFFFF')
            )
        ),
        'font'  => array(
            'color' => array('rgb' => 'ffffff'),
            //'size'  => 13,
            //'bold'  => true,
        ),
    );
    $arCellSick = array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'dd0000')
        ),
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => 'FFFFFF')
            )
        ),
        'font'  => array(
            'color' => array('rgb' => 'ffffff'),
            //'size'  => 13,
            //'bold'  => true,
        ),
    );
    $arCellLeave = array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '808080')
        ),
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            )
        ),
        'font'  => array(
            'color' => array('rgb' => 'ffffff'),
            //'size'  => 13,
            //'bold'  => true,
        ),
    );
    $arCellComp = array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '96e1fd')
        ),
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            )
        ),
    );
    $arCellWish = array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '1db49f')
        ),
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            )
        ),
        'font'  => array(
            'color' => array('rgb' => 'ffffff'),
            //'size'  => 13,
            //'bold'  => true,
        ),
    );
    $arCellExtraLeave = array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'f3ef71')
        ),
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            )
        ),
        'font'  => array(
            'color' => array('rgb' => '000000'),
            //'size'  => 13,
            //'bold'  => true,
        ),
    );
    $arCellFired = array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '000000')
        ),
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            )
        ),
        'font'  => array(
            'color' => array('rgb' => 'ffffff'),
            //'size'  => 13,
            //'bold'  => true,
        ),
    );
    
    $arCellBorder = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            )
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            //'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
    );
    $arCellTotal = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            )
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            //'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'font'  => array(
            //'color' => array('rgb' => 'ffffff'),
            //'size'  => 13,
            'bold'  => true,
        ),
    );
    $arCellHBlank = array(
//        'fill' => array(
//            'type' => PHPExcel_Style_Fill::FILL_SOLID,
//            'color' => array('rgb' => 'ff8888')
//        ),
    );
    
    
    //$page->getStyle('A1')->applyFromArray($arCellHDay);
    //$page->getStyle('B1')->applyFromArray($arHeadStyle);
//    $page->getStyle('C1')->applyFromArray($arHeadStyle);
//    $page->getStyle('D1')->applyFromArray($arHeadStyle);
//    $page->getStyle('E1')->applyFromArray($arHeadStyle);
//    $page->getStyle('F1')->applyFromArray($arHeadStyle);
//    $page->getStyle('G1')->applyFromArray($arHeadStyle);
//    $page->getStyle('H1')->applyFromArray($arHeadStyle);
//    $page->getStyle('I1')->applyFromArray($arHeadStyle);
//    $page->getStyle('J1')->applyFromArray($arHeadStyle);
//    $page->getStyle('K1')->applyFromArray($arHeadStyle);
//    $page->getStyle('L1')->applyFromArray($arHeadStyle);
//    $page->getStyle('M1')->applyFromArray($arHeadStyle);
//    $page->getStyle('N1')->applyFromArray($arHeadStyle);
//    $page->getStyle('O1')->applyFromArray($arHeadStyle);
//    $page->getStyle('P1')->applyFromArray($arHeadStyle);
//    $page->getStyle('Q1')->applyFromArray($arHeadStyle);
//    $page->getStyle('R1')->applyFromArray($arHeadStyle);
//    $page->getStyle('S1')->applyFromArray($arHeadStyle);
//    $page->getStyle('T1')->applyFromArray($arHeadStyle);
//    $page->getStyle('U1')->applyFromArray($arHeadStyle);
//    $page->getStyle('V1')->applyFromArray($arHeadStyle);
//    $page->getStyle('W1')->applyFromArray($arHeadStyle);
//    $page->getStyle('X1')->applyFromArray($arHeadStyle);
//    $page->getStyle('Y1')->applyFromArray($arHeadStyle);
//    $page->getStyle('Z1')->applyFromArray($arHeadStyle);
//    $page->getStyle('AA1')->applyFromArray($arHeadStyle);
//    $page->getStyle('AB1')->applyFromArray($arHeadStyle);
//    $page->getStyle('AC1')->applyFromArray($arHeadStyle);
//    $page->getStyle('AD1')->applyFromArray($arHeadStyle);
//    $page->getStyle('AE1')->applyFromArray($arHeadStyle);
//    $page->getStyle('AF1')->applyFromArray($arHeadStyle);
//    $page->getStyle('AG1')->applyFromArray($arHeadStyle);
//    $page->getStyle('AH1')->applyFromArray($arHeadStyle);
   
    
?>

<table class="table table-condensed">
    <tr>
        <th>Фамилия И.О.</th>
        <?php
            $page->getStyle('A1')->applyFromArray($arHeadStyle);
            $page->setCellValue('A1', 'Фамилия И.О.'); // Добавляем в ячейку A1 "№"
        ?>
        <th>Отдел</th>
        <?php
            $page->getStyle('B1')->applyFromArray($arHeadStyle);
            $page->setCellValue('B1', 'Отдел'); // Добавляем в ячейку A1 "№"
        ?>
        <?php $Nm = 0; ?>
        <?php $xlsb = ord('C'); ?>
        <th class='days-tips'>
            <?php
                $sumhour = 0;
                for ($i = 0; $i < $table->calendar->countSF; $i++) { 
                    
                    if ($table->calendar->days[$i]['curMonth']) {
                        if ($table->calendar->days[$i]['HDay'] == '0') $sumhour = $sumhour + 8;
                        //if (($table->calendar->days[$i]['HDay'] != '1') && ($table->calendar->days[$i]['HDay'] != '2')) $sumhour = $sumhour + 8;
                        if ($table->calendar->days[$i]['HDay'] == '2') $sumhour = $sumhour + 7;
                        
                        $ei = getExcelIndex($xlsb, $Nm);
                        $phpexcel->getActiveSheet()->getColumnDimension($ei)->setWidth(4);
                        $page->getStyle($ei.'1')->applyFromArray($arHeadStyle);
                        $page->setCellValue($ei."1", $Nm + 1); // Добавляем в ячейку A1 "№"
                        if ($table->calendar->days[$i]['HDay'] == "0") $page->getStyle($ei."1")->applyFromArray($arCellWDay);
                        if ($table->calendar->days[$i]['HDay'] == "1") $page->getStyle($ei."1")->applyFromArray($arCellHDay);
                        if ($table->calendar->days[$i]['HDay'] == "2") $page->getStyle($ei."1")->applyFromArray($arCellSDay);
                        if ($table->calendar->days[$i]['HDay'] == "3") $page->getStyle($ei."1")->applyFromArray($arCellHDay);
//                        if ($Nm < 24) {
//                            if (($table->calendar->days[$i]['HDay'] == "1") || ($table->calendar->days[$i]['HDay'] == "3")) $page->getStyle(chr($xlsb + $Nm)."1")->applyFromArray($arCellHDay);
//                            if ($table->calendar->days[$i]['HDay'] == "2") $page->getStyle(chr($xlsb + $Nm)."1")->applyFromArray($arCellSDay);
//                            $page->setCellValue(chr($xlsb + $Nm)."1", $Nm+1); // Добавляем в ячейку A1 "№"
//                            
//                        }
//                        else {
//                            if (($table->calendar->days[$i]['HDay'] == "1") || ($table->calendar->days[$i]['HDay'] == "3")) $page->getStyle(chr($xlsb + $Nm)."1")->applyFromArray($arCellHDay);
//                            if ($table->calendar->days[$i]['HDay'] == "2") $page->getStyle(chr($xlsb + $Nm)."1")->applyFromArray($arCellSDay);
//                            $page->setCellValue('A'.chr($xlsb + $Nm - 26)."1", $Nm+1); // Добавляем в ячейку A1 "№"
//                            
//                        }
                        $Nm++;
                    }
                    $ei = getExcelIndex($xlsb, $Nm);
                    $page->getStyle($ei."1")->applyFromArray($arHeadStyle);
                    $page->setCellValue($ei."1", 'Часов');
                    ?>
                    <span class="day<?= $table->calendar->days[$i]['today'] ? ' today' : '' ?>
                                    <?= !$table->calendar->days[$i]['curMonth'] ? ' non-month' : '' ?>
                                    <?= $table->calendar->days[$i]['HDay'] == "1" ? ' hday' : '' ?>
                                    <?= $table->calendar->days[$i]['HDay'] == "2" ? ' shortday' : '' ?>
                                    <?= $table->calendar->days[$i]['HDay'] == "3" ? ' hday celebration' : '' ?>">
                        <?= $table->calendar->days[$i]['showDay'] ?>
                    </span>
            <?php } ?>
        </th>
        <th>Часы</th>
    </tr>
    
    
<?php
    $report = '';
    for ($e = 0; $e < count($table->employe); $e++) {
        
        $eid = findArrayEid($table->employe[$e]['eid'], $table->tsheet);
        $total = 0;
        ?>
    <tr>
        <td style="min-width: 240px;" id="eid<?= $table->employe[$e]['eid'] ?>"<?php if (count($eid['ts_comment']) > 0) { echo ' class="row-comment"'; $report = $report.'<b>'.$table->employe[$e]['fio_short'].'</b><br>'; } ?>><span<?= $table->employe[$e]['note'] == '' ? '' : ' title="'.$table->employe[$e]['note'].'"'?>><?= $table->employe[$e]['fio_short'].($table->employe[$e]['note'] == '' ? '' : '<sup>*</sup>') ?></span><p><?= $table->employe[$e]['post'] ?></p></td>
        <?php
            $page->getStyle('A'.(2 + $e))->applyFromArray($arCellBorder);
            $page->setCellValue('A'.(2 + $e), $table->employe[$e]['fio_short']);
        ?>
        <td><?= getDepTitle($table->employe[$e]['did'], true) ?></td>
        <?php
            $page->getStyle('B'.(2 + $e))->applyFromArray($arCellBorder);
            $page->setCellValue('B'.(2 + $e), getDepTitle($table->employe[$e]['did'], true));
        ?>
        <td style="min-width: <?= $table->calendar->countSF * 30 ?>px;" class="employe-row">
        <?php
            $No = 0;
            $Nm = 0;
            for ($i = 0; $i < $table->calendar->countSF; $i++) {
                $shift = '';
                $comment = '';
                // здесь надо искать букву для $shift
                

                if ($eid != false) {
                    // есть элементы для отображения
                    // ищем значение для даты
                    $shift = findShiftFromDay($table->calendar->days[$i]['dtLink'], $eid['timesheet']);
                    $comment = findCommentFromDay($table->calendar->days[$i]['dtLink'], $eid['ts_comment']);
                    
                    
                }
                
                if ($table->calendar->days[$i]['curMonth']) {
                    
                    $fl = true;
                    $Nm++;
                    $ei = getExcelIndex($xlsb, $No);
                    $page->getStyle($ei.(2 + $e))->applyFromArray($arCellBorder);
                    
                    //if ($shift == '') $total = $total + 8;
                    //if ($shift == '') $total = $total + 8;
                    if ($shift == 'Д') {
                        $total = $total + 12;
//                        if ($table->calendar->days[$i]['HDay'] == "2") $total = $total - 1;
//                        if ($table->calendar->days[$i]['HDay'] == "3") $total = $total + 12;
                        $fl = false;
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellDay);
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellBorder);
                        $page->setCellValue($ei.(2 + $e), 'Д');
                    } // 11
                    if ($shift == 'Н') {
                        $total = $total + 12;
//                        if ($table->calendar->days[$i]['HDay'] == "2") $total = $total - 1;
//                        if ($table->calendar->days[$i]['HDay'] == "3") $total = $total + 12;
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellNight);
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellBorder);
                        $page->setCellValue($ei.(2 + $e), 'Н');
                        $fl = false;
                    }
                    if ($shift == '8') {
                        $total = $total + 8;
                        if ($table->calendar->days[$i]['HDay'] == "2") $total = $total - 1;
//                        if ($table->calendar->days[$i]['HDay'] == "3") $total = $total + 8;
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCell8Hour);
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellBorder);
                        $page->setCellValue($ei.(2 + $e), '8');
                        $fl = false;
                    }
                    if ($shift == 'С') {
                        $total = $total + 12;
//                        if ($table->calendar->days[$i]['HDay'] == "2") $total = $total - 1;
//                        if ($table->calendar->days[$i]['HDay'] == "3") $total = $total + 12;
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCell12Hour);
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellBorder);
                        $page->setCellValue($ei.(2 + $e), 'С');
                        $fl = false;
                    } // 11
                    if ($shift == 'К') {
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellComp);
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellBorder);
                        $page->setCellValue($ei.(2 + $e), 'К');
                        $fl = false;
                    }
                    if ($shift == 'Б') {
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellSick);
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellBorder);
                        $page->setCellValue($ei.(2 + $e), 'Б');
                        $fl = false;
                    }
                    if ($shift == 'О') {
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellLeave);
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellBorder);
                        $page->setCellValue($ei.(2 + $e), 'О');
                        $fl = false;
                    }
                    if ($shift == 'З') {
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellWish);
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellBorder);
                        $page->setCellValue($ei.(2 + $e), 'З');
                        $fl = false;
                    }
                    if ($shift == 'Ч') {
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellExtraLeave);
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellBorder);
                        $page->setCellValue($ei.(2 + $e), 'Ч');
                        $fl = false;
                    }
                    if ($shift == 'Х') {
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellFired);
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellBorder);
                        $page->setCellValue($ei.(2 + $e), 'Х');
                        $fl = false;
                    }
                    if ($fl && (($table->calendar->days[$i]['HDay'] == '1') || ($table->calendar->days[$i]['HDay'] == '3'))) $page->getStyle($ei.(2 + $e))->applyFromArray($arCellHBlank);
                ?>
                    <input name="day[<?= $table->employe[$e]['eid'] ?>][<?= $table->calendar->days[$i]['dtLink'] ?>]"
                        class="day<?= $table->calendar->days[$i]['today'] ? ' today' : '' ?>
                                    <?= !$table->calendar->days[$i]['curMonth'] ? ' non-month' : '' ?>
                                    <?= $table->calendar->days[$i]['HDay'] == "1" ? ' hday' : '' ?>
                                    <?= $table->calendar->days[$i]['HDay'] == "2" ? ' shortday' : '' ?>
                                    <?= $table->calendar->days[$i]['HDay'] == "3" ? ' hday celebration' : '' ?>
                                    <?php if ($comment != '') { echo ' ts-comment'; $report = $report. ShowDigiDate($table->calendar->days[$i]['dtLink']).' - '.$comment.'<br>'; } ?>"
                                    value="<?= $shift ?>" title="<?= $comment ?>"  oninput="changestyleandsum(this);"
                    >
            
                <?php 
                    $No++;
                } 
                else {
                ?>
                    <span class="day<?= $table->calendar->days[$i]['today'] ? ' today' : '' ?>
                                    <?= !$table->calendar->days[$i]['curMonth'] ? ' non-month' : '' ?>
                                    <?= $table->calendar->days[$i]['HDay'] == "1" ? ' hday' : '' ?>
                                    <?= $table->calendar->days[$i]['HDay'] == "2" ? ' shortday' : '' ?>">
                        <?= $shift ?>
                    </span>
                <?php }
                ?>
                
            <?php } ?>
        </td>
        <td id="td<?= $table->employe[$e]['eid'] ?>"><?= $total ?></td>
        <?php
         {
                        $ei = getExcelIndex($xlsb, $Nm);
                        $page->getStyle($ei.(2 + $e))->applyFromArray($arCellTotal);
                        $page->setCellValue($ei.(2 + $e), $total);
                    }
        ?>
    </tr>
        <?php
    }
?>
    
    <?php
       /* Начинаем готовиться к записи информации в xlsx-файл */
        $objWriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');
        /* Записываем в файл */
        $xls = 'table_'.date("YmdHis", time() + 3600 * 3).'.xlsx';
        $objWriter->save('../web/files/docs/'.$xls);
    ?>
    
</table>


<?= (($table->did != 0) && (($table->did == Yii::$app->user->identity->did) || 
     (Yii::$app->user->id == 1) ||
     ((Yii::$app->user->id == 4) && (($table->did == 1) || ($table->did == 5) || ($table->did == 6))) 
        )) ? Html::submitButton('Сохранить', ['class' => 'btn btn-success']) : '' ?>
<?= Html::endForm() ?>

</center>
<hr>
Часов в месяц по производственному календарю: <b id="sumhour"><?= $sumhour ?></b>
<?php
if (count($table->employe) > 0) { ?>
<br /><a href="/web/files/docs/<?= $xls ?>"><?= $xls ?></a>
<?php } ?>
<hr>
<p id="creport" style="font-size: smaller;">
</p>
<noscript><?= $report ?></noscript>
<hr>
<div class="no-float-day">
    <div><span class="day" style="background-color: rgb(86, 168, 239); color: black;">8</span> - 8-час. рабочий день</div>
    <div><span class="day" style="background-color: orange; color: white;">С</span> - смена 9:00-21:00 или 8:00-20:00</div>
</div>
<div class="no-float-day">    
    <div><span class="day" style="background-color: green; color: white;">Д</span> - дневная смена тех.поддержки</div>
    <div><span class="day" style="background-color: blue; color: white;">Н</span> - ночная смена тех.поддержки</div>
</div>
<div class="no-float-day">    
    <div><span class="day" style="background-color: gray; color: black;">О</span> - отпуск</div>
    <div><span class="day" style="background-color: rgb(243, 239, 113); color: black;">Ч</span> - отпуск (авария ЧАЭС)</div>
</div>
<div class="no-float-day">
    <div><span class="day" style="background-color: rgb(221, 0, 0); color: white;">Б</span> - больничный</div>
    <div><span class="day" style="background-color: rgb(29, 180, 159); color: white;">З</span> - заявление</div>
</div>
<div class="no-float-day">    
    <div><span class="day" style="background-color: black; color: white;">Х</span> - не работает</div>
    <div><span class="day">Ш</span> - комментарий [ i ]</div>
</div>
<script>

//function checkpresskey(elem) {
//    if (event.keyCode == 73) { var com = prompt('Комментарий: ', elem.title); return false; }
//    else return false;
//}

// доступные значения табеля
var listAllow = new Array("Д", "Н", "8", "С", "О", "Х", "Б", "З", "Ч", "К");

function updatecomment() {
    //var i = 0;
    var creport = document.getElementById("creport");
    //creport.innerHTML = "";
    var table = document.getElementsByTagName("TABLE")[0];
    var rows = table.getElementsByTagName("TR");
    var rtext = "";
    for (w = 1; w < rows.length; w++) {
        var cmt = false;
        var ctext = "";
        var tds = rows[w].getElementsByTagName("TD");
        var td0 = tds[0];
        var td2 = tds[2];
        var td3 = tds[3];
        var inputs = td2.getElementsByTagName("INPUT");
        for (t = 0; t < inputs.length; t++) {
            if (inputs[t].title != '') {
                cmt = true;
                var slength = inputs[t].name.length - 1;
                ctext = 
                    ctext + 
                    inputs[t].name[slength - 2] + inputs[t].name[slength - 1] + "." +
                    inputs[t].name[slength - 5] + inputs[t].name[slength - 4] + "." +
                    inputs[t].name[slength - 10] + inputs[t].name[slength - 9] + inputs[t].name[slength - 8] + inputs[t].name[slength - 7] + " - " +    
                    inputs[t].title + "<br>";
                //creport.innerHTML = creport.innerHTML + inputs[t].title + "<br>";
            }
            
        }
        if (ctext != "") rtext = rtext + "<b>" + td0.firstChild.innerHTML + "</b><br>" + ctext;
        if (cmt) td0.classList.add("row-comment"); else td0.classList.remove("row-comment");
       // if (ctext != "") creport.innerHTML = ctext;
    }
    creport.innerHTML = rtext;
}

    
function changestyle(elem) {
    elem.value = elem.value.trim().toUpperCase();
    if (elem.value == "L") elem.value = "Д";
    if (elem.value == "Y") elem.value = "Н";
    if (elem.value == "C") elem.value = "С";
    if (elem.value == "J") elem.value = "О";
    if (elem.value == "L") elem.value = "Д";
    if (elem.value == "X") elem.value = "Ч";
    if (elem.value == "R") elem.value = "К";
    if ((elem.value == "{") || (elem.value == "[")) elem.value = "Х";
    if ((elem.value == ",") || (elem.value == "<")) elem.value = "Б";
    if (elem.value == "P") elem.value = "З";
    if (listAllow.indexOf(elem.value) == -1) elem.value = "";
    if (elem.value == '') { elem.style.backgroundColor = 'white'; elem.style.color = 'black'; }
    if (elem.value == 'Д') { elem.style.backgroundColor = 'green'; elem.style.color = 'white'; }
    if (elem.value == 'Н') { elem.style.backgroundColor = 'blue'; elem.style.color = 'white'; }
    if (elem.value == '8') { elem.style.backgroundColor = '#56a8ef'; elem.style.color = 'black'; }
    if (elem.value == 'С') { elem.style.backgroundColor = 'orange'; elem.style.color = 'white'; }
    if (elem.value == 'О') { elem.style.backgroundColor = 'gray'; elem.style.color = 'black'; }
    if (elem.value == 'Х') { elem.style.backgroundColor = 'black'; elem.style.color = 'white'; }
    if (elem.value == 'Б') { elem.style.backgroundColor = '#dd0000'; elem.style.color = 'white'; }
    if (elem.value == 'З') { elem.style.backgroundColor = '#1db49f'; elem.style.color = 'white'; }
    if (elem.value == 'Ч') { elem.style.backgroundColor = '#f3ef71'; elem.style.color = 'black'; }
    if (elem.value == 'К') { elem.style.backgroundColor = '#96e1fd'; elem.style.color = 'black'; }
}

function autofill(elem) {
    var inputs = elem.parentNode.getElementsByTagName("INPUT");
    var ivalue = elem.value;
    var flstop = true;
    var pos = 0;
    
    // ищем выбранный элемент в списке
    while (pos <= inputs.length && flstop) {
        
        if (inputs[pos].name == elem.name) flstop = false;
        else pos++;
    }
    // нашли, теперь выполняем операции
    
    if (elem.value == "") { // режим очистки графика
        for (i = pos; i < inputs.length; i++) {
            inputs[i].value = "";
            changestyle(inputs[i]);
        }
    }
    
    if (elem.value == "О") { // режим заполнения отпуска - все дни до конца месяца
        for (i = pos; i < inputs.length; i++) {
            inputs[i].value = "О";
            changestyle(inputs[i]);
        }
    }
    if (elem.value == "Х") { // режим заполнения отпуска - все дни до конца месяца
        for (i = pos; i < inputs.length; i++) {
            inputs[i].value = "Х";
            changestyle(inputs[i]);
        }
    }
    if (elem.value == "Ч") { // режим заполнения отпуска ЧАЭС - не более 7 дней до конца месяца
        for (i = pos; ((i < inputs.length) && (i < pos + 7)); i++) {
            inputs[i].value = "Ч";
            changestyle(inputs[i]);
        }
    }
    
    if (elem.value == "8") { // для этого режима заполняем рабочие дни до конца графика
        for (i = pos; i < inputs.length; i++) {
            if (!inputs[i].classList.contains("hday")) {
                inputs[i].value = "8";
                changestyle(inputs[i]);
            }
            else {
                inputs[i].value = "";
                changestyle(inputs[i]);
            }
        }
    }
    
    if (elem.value == "Д") { // для этого режима заполняем рабочие дни до конца графика
        var i = pos;
        while (i < inputs.length) {

            if ((i) < inputs.length) {
                inputs[i].value = "Д";
                changestyle(inputs[i]);
                i++;
            }
            if ((i) < inputs.length) {
                inputs[i].value = "Н";
                changestyle(inputs[i]);
                i++;
            }
            if ((i) < inputs.length) {
                inputs[i].value = "";
                changestyle(inputs[i]);
                i++;
            }
            if ((i) < inputs.length) {
                inputs[i].value = "";
                changestyle(inputs[i]);
                i++;
            }
        }
    }
    
    
    if (elem.value == "С") { // для этого режима нужно уточнить сколько элементов смены уже установлено (2 или 3)
                             // после чего заполнить согласно соответствующей схеме
        if (((pos - 1) >= 0) && (inputs[pos - 1].value == "С") && ((pos - 2) >= 0) && (inputs[pos - 2].value == "С"))
             var mode = "3";
        else var mode = "2";
        
        //for (i = pos + 1; i < inputs.length; i++) {
            var i = pos + 1;
            while (i < inputs.length) {
            
            if (mode == "2") {
                if ((i) < inputs.length) {
                    inputs[i].value = "";
                    changestyle(inputs[i]);
                    i++;
                }
                if ((i) < inputs.length) {
                    inputs[i].value = "";
                    changestyle(inputs[i]);
                    i++;
                }
                if ((i) < inputs.length) {
                    inputs[i].value = "С";
                    changestyle(inputs[i]);
                    i++;
                }
                if ((i) < inputs.length) {
                    inputs[i].value = "С";
                    changestyle(inputs[i]);
                    i++;
                }
            }
            if (mode == "3") {
                if ((i) < inputs.length) {
                    inputs[i].value = "";
                    changestyle(inputs[i]);
                    i++;
                }
                if ((i) < inputs.length) {
                    inputs[i].value = "";
                    changestyle(inputs[i]);
                    i++;
                }
                if ((i) < inputs.length) {
                    inputs[i].value = "";
                    changestyle(inputs[i]);
                    i++;
                }
                if ((i) < inputs.length) {
                    inputs[i].value = "С";
                    changestyle(inputs[i]);
                    i++;
                }
                if ((i) < inputs.length) {
                    inputs[i].value = "С";
                    changestyle(inputs[i]);
                    i++;
                }
                if ((i) < inputs.length) {
                    inputs[i].value = "С";
                    changestyle(inputs[i]);
                    i++;
                }
            }
        }
    }
    
    for (i = 100000; i < inputs.length; i++) {
        // игнорируем инпуты пока не дойдем до нужного
        if (flstop && (inputs[i].name == elem.name))
            flstop = false;
            var curr = inputs[i];
        
        
        
        if (!flstop) { inputs[i].value = elem.value; changestyle(inputs[i]);
            
        }
    }
}

function changestyleandsum(elem) {
    
    if ((elem.value.indexOf('i') != -1) ||
        (elem.value.indexOf('I') != -1) ||
        (elem.value.indexOf('ш') != -1) ||
        (elem.value.indexOf('Ш') != -1)) {
            elem.value = elem.value.replace("i", "");
            elem.value = elem.value.replace("I", "");
            elem.value = elem.value.replace("ш", "");
            elem.value = elem.value.replace("Ш", "");
            var query = prompt("Введите комментарий: ", elem.title);
            if (query != null) {
                savecomment(elem, query);
                return;
            }
    }
    
    if ((elem.value.indexOf('q') != -1) ||
        (elem.value.indexOf('Q') != -1) ||
        (elem.value.indexOf('й') != -1) ||
        (elem.value.indexOf('Й') != -1)) {
            elem.value = elem.value.replace("q", "");
            elem.value = elem.value.replace("Q", "");
            elem.value = elem.value.replace("й", "");
            elem.value = elem.value.replace("Й", "");
            autofill(elem);
    }

    changestyle(elem);

        var prnt = elem.parentNode;
        var tt = prnt.getElementsByTagName("INPUT");
        var summa = 0;
        for (w = 0; w < tt.length; w++) {
            if (tt[w].value == "8") { summa = summa + 8; if (tt[w].classList.contains('shortday')) summa = summa - 1; /* if (tt[w].classList.contains('celebration')) summa = summa + 8; */}
            if (tt[w].value == "Д") { summa = summa + 12; /* if (tt[w].classList.contains('shortday')) summa = summa - 1; if (tt[w].classList.contains('celebration')) summa = summa + 12; */}// 11
            if (tt[w].value == "Н") { summa = summa + 12; /* if (tt[w].classList.contains('shortday')) summa = summa - 1; if (tt[w].classList.contains('celebration')) summa = summa + 12; */}
            if (tt[w].value == "С") { summa = summa + 12; /* if (tt[w].classList.contains('shortday')) summa = summa - 1; if (tt[w].classList.contains('celebration')) summa = summa + 12; */}// 11
            
        }
        var tr = prnt.parentNode;
        var ss = tr.getElementsByTagName("TD");
        var lasttd = ss[3];
        //var firsttd = ss[0];
        //
        lasttd.innerHTML = summa;
        lasttd.style.color = "#ff0000";
        
}

$(document).ready(function() {
        //var listAllow = ["Д", "Н", "8", "С", "О", "Х", "Б"];
        var days = document.getElementsByClassName("day");
        for (i = 0; i < days.length; i++) {
            if (days[i].tagName == "INPUT") {
                changestyle(days[i]);
                updatecomment();
            }
        }
    }
);

function getXmlHttp(){
  var xmlhttp;
  try {
    xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
  } catch (e) {
    try {
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    } catch (E) {
      xmlhttp = false;
    }
  }
  if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
    xmlhttp = new XMLHttpRequest();
  }
  return xmlhttp;
}


// javascript-код голосования из примера
function savecomment(elem, comment) {
	// (1) создать объект для запроса к серверу
	var req = getXmlHttp();
       
        // (2)
	// span рядом с кнопкой
	// в нем будем отображать ход выполнения
	//var statusElem = document.getElementById('vote_status') 
	
	req.onreadystatechange = function() {  
        // onreadystatechange активируется при получении ответа сервера

		if (req.readyState == 4) { 
            // если запрос закончил выполняться

			//statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)

			if(req.status == 200) { 
                 // если статус 200 (ОК) - выдать ответ пользователю
                                elem.title = comment;
                                if (comment == '') { elem.classList.remove("ts-comment"); } else { elem.classList.add("ts-comment"); }
				alert(req.responseText);
                                updatecomment();
			}
			// тут можно добавить else с обработкой ошибок запроса
		}

	}

       // (3) задать адрес подключения
        comment = comment.replace(new RegExp('"', 'g'), '');
        
	req.open('GET', '/table/ajax?'+elem.name+'=&comment='+comment, true);  

	// объект запроса подготовлен: указан адрес и создана функция onreadystatechange
	// для обработки ответа сервера
	 
        // (4)
	req.send(null);  // отослать запрос
  
        // (5)
	//statusElem.innerHTML = 'Ожидаю ответа сервера...' 
}

window.onload = function() {
   // Ваш скрипт
   
};

</script>
<?php if (Yii::$app->user->id == 1) { 
    ?><pre>
    <?= var_dump($table) ?>
    </pre><?php
}
?>