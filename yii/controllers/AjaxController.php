<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

use yii\web\Controller;
use Yii;
use app\models\Yandex;
use app\models\Salary;
use app\models\Report;
use app\models\Paysalary;
use app\models\Payout;
use app\models\Employe;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Alignment;

/**
 * Description of ApiController
 *
 * @author vitt
 */
class AjaxController extends ExtController {
    
    
    public function actionTable () {
        return 'Ajax: Table';
    }
    public function actionTask () {
        return 'Ajax: Task';
    }

    
    // возращает ближайший дом к введенным координатам
    
    public function actionMapNearHouse () {
        
        if (Yii::$app->request->isGet) {
            
            $x = htmlspecialchars(Yii::$app->request->get('xcor', false));
            $y = htmlspecialchars(Yii::$app->request->get('ycor', false));
            $r = 0;
            if ($x && $y) {
                do {
                    $r = $r + 0.00025;
                    $coord = Yandex::find()->select("*, (power((`xcor`-".$x."), 2) + power((`ycor`-".$y."), 2)) as `sss`")->where('power((`xcor`-'.$x.'), 2) + power((`ycor`-'.$y.'), 2) <= power('.$r.', 2)')-> orderBy('sss')->asArray()->all();
                }
                while (count($coord) == 0);
                $result = json_encode($coord[0]);
                return $result;
            }
        }
        return '';        
    }
    
    // возращает в виде массива зарплату сотрудников за указанный месяц для указанного отдела или для всех
    
    public function actionReturnEmployeesSalaryForMonth () {
        
        
        if (Yii::$app->request->isGet) {
            
            $did = htmlspecialchars(Yii::$app->request->get('did', 0));
            $month = htmlspecialchars(Yii::$app->request->get('month', date("m")));
            $year = htmlspecialchars(Yii::$app->request->get('year', date("Y")));
        }
        
        if ($did == 0) {
            $salary = Salary::find()->asArray()->where(['=', 'sldate', $year.$month])->with('employe')->with('payout')->all();
        }
        else {
            $salary = Salary::find()->asArray()->where(['=', 'sldate', $year.$month])->with('employe')->with('payout')->all();
        }
        return json_encode($salary);
        //return $this->render('index', ['result' => $salary]);
    }
    
    // возращает зарплату за указанный месяц для всех сотрудников с отчётом за указанные в параметрах GET даты
    
    public function actionGetSalaryForMonth () {
        
        $result = '';
        if (Yii::$app->request->isGet) {
            
            $month = htmlspecialchars(Yii::$app->request->get('month', date("m")));
            $year = htmlspecialchars(Yii::$app->request->get('year', date("Y")));
                        
            $start = htmlspecialchars(Yii::$app->request->get('start', $year.'-'.$month.'-01'));
            $finish = htmlspecialchars(Yii::$app->request->get('finish', $year.'-'.$month.'-'.date("t", strtotime($start))));
            
            $salary = new Salary($month, $year);
            $salary->addReport($start, $finish);

            return json_encode($salary->result);
        }
        
        return $result;
        //return $this->render('index', ['result' => $salary->result]);
    }
    
    // копирует начисления из прошлого месяца
    
    public function actionCopySalaryFromPrevMonth () {
        
        $result = '';
        if (Yii::$app->request->isGet) {
            $eid = htmlspecialchars(Yii::$app->request->get('eid'));
            $sldate = htmlspecialchars(Yii::$app->request->get('sldate'));
            
            $year = $sldate[0].$sldate[1].$sldate[2].$sldate[3];
            $month = $sldate[4].$sldate[5];
            
            if ($month == '01') {
                $month = 12;
                $year = $year - 1;
                
            }
            else $month = $month - 1;
            if ($month <= 9) $month = '0'.$month;
            $_sldate = $year.$month;
            
            $paysalary = Paysalary::find()->where(['=', 'eid', $eid])->andWhere(['=', 'sldate', $_sldate])->limit(1)->all();
            //echo var_dump($_sldate); die;
            $paysalary = $paysalary[0];
            
            //return var_dump($paysalary);
                
            $id = $paysalary->id;
            
            $paysalary->id = null;
            $paysalary->isNewRecord = true;
            $paysalary->sldate = $sldate;
            $paysalary->block = 0;
            
            $paysalary->save();
            $new_id = Yii::$app->db->getLastInsertID();
            
            
            
            $payout = Payout::find()->where(['=', 'salary_id', $id])->all();
            
            $i = 0;
            foreach ($payout as $item) {
                $item->id = null;
                $item->isNewRecord = true;
                $item->salary_id = $new_id;
                if ($item->type != 'onetime') {
                    $item->save();
                    $i++;
                }
            }
            $result = 'Добавлено: '.$i.' полей.';
            
        }
        return $result;
    }
    
    
    // блокирует запись от изменений - ПОКА НЕ РАБОТАЕТ В КОДАХ ОБРАБОТКИ НАЧИСЛЕНИЙ
    public function actionBlockSalary () {
        
        $result = '';
        if (Yii::$app->request->isGet) {
            $eid = htmlspecialchars(Yii::$app->request->get('eid'));
            $sldate = htmlspecialchars(Yii::$app->request->get('sldate'));
            
            $paysalary = Paysalary::find()->where(['=', 'eid', $eid])->andWhere(['=', 'sldate', $sldate])->limit(1)->all();
            if (count($paysalary) > 0) {
                $paysalary = $paysalary[0];
                $paysalary->block = 1;

                // !!!!!!!!!!!
                // нужно убедиться что для записи есть хоть одна запись с начислениями

                $payout = Payout::find()->asArray()->where(['=', 'salary_id', $paysalary->id])->all();
                if (count($payout) > 0) {

                    if ($paysalary->save())
                        $result = 'Успешно! Выплаты зафиксированы в БД.';
                }
                else {
                    $result = 'Ошибка! Для этой записи не сформировано ни одной выплаты.';
                }
            }
            else {
                $result = 'Ошибка! Запись отсутствует.';
            }
            
            
            
        }
        return $result;
    }

    
    // создает начисления из шаблона (записи в payout, не имеющие связи с paysalary)
    public function actionCreateSalaryFromTemplate() {
        
        if (Yii::$app->request->isGet) {
            $eid = htmlspecialchars(Yii::$app->request->get('eid'));
            $sldate = htmlspecialchars(Yii::$app->request->get('sldate'));
            $salary = htmlspecialchars(Yii::$app->request->get('salary'));
            $award = htmlspecialchars(Yii::$app->request->get('award'));
            $summa = htmlspecialchars(Yii::$app->request->get('summa'));
            $template = htmlspecialchars(Yii::$app->request->get('template'), 0);
            
            $paysalary = new Paysalary();
            $paysalary->eid = $eid;
            $paysalary->sldate = $sldate;
            $paysalary->payment = $salary;
            $paysalary->award = $award;
            $paysalary->save();
            $new_id = Yii::$app->db->getLastInsertID();
            
            $payout = Payout::find()->where(['=', 'salary_id', $template])->all();
            

            foreach ($payout as $item) {
                $item->id = null;
                $item->isNewRecord = true;
                $item->salary_id = $new_id;
                if (strpos($item->base, 'summa=')) $item->base = 'summa=' .$summa;
                $item->save();
            }
            return 'Выполнено! Начисления добавлены.';
            
        }
        return '';
    }
    
    // получает и передает данные по начислениям и отчетам для загрузки в модальную форму
    public function actionLoadModalForm() {
        if (Yii::$app->request->isGet) {
            $eid = htmlspecialchars(Yii::$app->request->get('eid'));
            $sldate = htmlspecialchars(Yii::$app->request->get('sldate'));
            
            $result = Array();
           
            $employe = Employe::find()->select('eid, fio, fio_short, post')->asArray()->where(['=', 'eid', $eid])->limit(1)->all();
            
            $report = new Report();
            
            
            $result['eid'] = $eid;
            $result['sldate'] = $sldate;
            $result['fio'] = $employe[0]['fio'];
            $result['fio_short'] = $employe[0]['fio_short'];
            $result['post'] = $employe[0]['post'];
            
            $sf = getDateFromSldate($sldate);
            $result['report'] = $report->getDateRepOnEid($sf['start'], $sf['finish'], $eid);
            
            
            $paysalary = Paysalary::find()->asArray()->where(['=', 'eid', $eid])->andWhere(['=', 'sldate', $sldate])->limit(1)->all();
            if (count($paysalary) > 0) {
                
                $result['id'] = $paysalary[0]['id'];
                $result['salary'] = $paysalary[0]['payment'];
                $result['award'] = $paysalary[0]['award'];
                $result['block'] = $paysalary[0]['block'];
                 
                $payout = Payout::find()->asArray()->where(['=', 'salary_id', $paysalary[0]['id']])->orderBy('sorting')->all();
                foreach ($payout as $pay) {
                    $result['payout'][] = $pay;
                }
            }
            else {
                
                $result['id'] = -1;
                $result['salary'] = -1;
                $result['award'] = -1;
                $result['block'] = -1;
                $result['payout'] = array();
                
            }
            return json_encode($result);
        }
        return '';
    }
    
    
    // создает или изменяет основную запись в paysalary
    public function actionInsertPaysalary() {
        if (Yii::$app->request->isGet) {
            $id = htmlspecialchars(Yii::$app->request->get('id'));
            $eid = htmlspecialchars(Yii::$app->request->get('eid'));
            $sldate = htmlspecialchars(Yii::$app->request->get('sldate'));
            $salary = htmlspecialchars(Yii::$app->request->get('salary'));
            $award = htmlspecialchars(Yii::$app->request->get('award'));
            
            // ЕСТЬ ЛИ СМЫСЛ ИСКАТЬ ЗАПИСИ ПО ID ?????
            
            if ($id == -1) {
                
                // новая запись, но нужно все равно проверить на существование записи для указанного сотрудника и периода
                $paysalary = Paysalary::find()->where(['=', 'eid', $eid])->andWhere(['=', 'sldate', $sldate])->limit(1)->all();
                
                if (count($paysalary) > 0)
                    $paysalary = $paysalary[0];
                else $paysalary = new Paysalary();
                $new = $paysalary->isNewRecord;
                $paysalary->eid = $eid;
                $paysalary->sldate = $sldate;
                $paysalary->payment = $salary;
                $paysalary->award = $award;
                
                if ($paysalary->save()) 
                    if ($new)
                        return Yii::$app->db->lastInsertID;
                    else return $paysalary->id;
                else return -1;
            }
            else {
                
                // меняем существующую запись - только значения ОКЛАД И ПРЕМИЯ, ДАТУ и EID изменить нельзя
                $paysalary = Paysalary::find()->where(['=', 'id', $id])->limit(1)->all();
                if (count($paysalary) < 1) {
                    $paysalary = Paysalary::find()->where(['=', 'eid', $eid])->andWhere(['=', 'sldate', $sldate])->limit(1)->all();
                    if (count($paysalary) < 1)
                        return -1;
                    else $id = $paysalary[0]->id;
                }
                $paysalary = $paysalary[0];
                $paysalary->payment = $salary;
                $paysalary->award = $award;
                if ($paysalary->save()) return $id;
                else return -1;
                
            }
        }
        return -1;
    }
    
    
    // сохраняет запись строки таблицы начислений (НЕ ИСПОЛЬЗУЕТСЯ)
    public function actionSaveModalTableRow() {
        if (Yii::$app->request->isGet) {
            
            // НУЖНО ОПРЕДЕЛИТЬ ЧТО ДЕЛАТЬ С ЗАПИСЬЮ (параметр mode)
            //     1. УДАЛИТЬ
            //     2. ИЗМЕНИТЬ
            //     3. ДОБАВИТЬ
            
            $id = htmlspecialchars(Yii::$app->request->get('id'));
            $salary_id = htmlspecialchars(Yii::$app->request->get('salary_id'));
            $mode = htmlspecialchars(Yii::$app->request->get('mode'));
            $base = htmlspecialchars(Yii::$app->request->get('base'));
            $depends = htmlspecialchars(Yii::$app->request->get('depends'));
            $summa = htmlspecialchars(Yii::$app->request->get('summa'));
            $type = htmlspecialchars(Yii::$app->request->get('type'));
            $info = htmlspecialchars(Yii::$app->request->get('info'));
            $sorting = htmlspecialchars(Yii::$app->request->get('sorting'));
            if ($base == 'summa') $base = 'summa='.$summa;
            
            
            
            
            if ($id == -1) {
                // новая запись
                
                $payout = new Payout();
                
                $payout->salary_id = $salary_id;
                $payout->base = $base;
                $payout->depends = $depends;
                $payout->type = $type;
                $payout->info = $info;
                $payout->sorting = $sorting;
                if ($payout->save()) return Yii::$app->db->lastInsertID;
                else return -1;
            }
            else {
                $payout = Payout::find()->where(['=', 'id', $id])->limit(1)->all();
                $payout = $payout[0];
                $payout->base = $base;
                $payout->depends = $depends;
                $payout->type = $type;
                $payout->info = $info;
                $payout->sorting = $sorting;
                if ($payout->save()) return $id;
                else return -1;
            }
            
            
            
            
            
        }
        
        return -1;
    }
    
    // сохраняет все записи из строк таблицы начислений полученные в виде массива
    public function actionSaveModalTableArray() {
        if (Yii::$app->request->isGet) {
            $data = json_decode(Yii::$app->request->get('data'), true);
            $salary_id = Yii::$app->request->get('salary_id', -1);
            foreach ($data as $key => $value) {
                
                switch ($data[$key]['mode']) {
                    case 'none':
                        $payout = Payout::findOne($data[$key]['id']);
                        if ($data[$key]['base'] == 'summa') $data[$key]['base'] = 'summa='.$data[$key]['summ'];
                        $payout->base = $data[$key]['base'];
                        $payout->depends = $data[$key]['deps'];
                        $payout->type = $data[$key]['type'];
                        $payout->info = $data[$key]['info'];
                        $payout->sorting = $key;
                        $payout->save();
                            //return $payout->salary_id;
                    break;
                    case 'remove':
                        $payout = Payout::findOne($data[$key]['id']);
                        $payout->delete();
                    break;
                    case 'new':
                        $payout = new Payout();
                        $payout->salary_id = $salary_id;
                        if ($data[$key]['base'] == 'summa') $data[$key]['base'] = 'summa='.$data[$key]['summ'];
                        $payout->base = $data[$key]['base'];
                        $payout->depends = $data[$key]['deps'];
                        $payout->type = $data[$key]['type'];
                        $payout->info = $data[$key]['info'];
                        $payout->sorting = $key;
                        $payout->save();
                    break;
                    default:
                        
                    break;
                }
            }
            return 1;
        }    
        return -1;
    }
    
    // генерируем файл отчета по зарплате, сохраняем и выдаем ссылку на него (или код ошибки)
    public function actionGetSalaryLoadExcelLink() {
        if (Yii::$app->request->isGet) {
            
            // на вхеде должны быть следующие данные:
            // расчетные месяц и год
            // массив ID сотрудников, для которых сформируется отчёт
            // дата начала выборки задач (по умолчанию 1 число расчетного месяца)
            // дата окончания выборки задач (по умолчанию последний день расчетного месяца)
            
            $month = htmlspecialchars(Yii::$app->request->get('month', date("m")));
            $year = htmlspecialchars(Yii::$app->request->get('year', date("Y")));
                        
            $start = htmlspecialchars(Yii::$app->request->get('start', $year.'-'.$month.'-01'));
            $finish = htmlspecialchars(Yii::$app->request->get('finish', $year.'-'.$month.'-'.date("t", strtotime($start))));
            
            $eids = json_decode(Yii::$app->request->get('eids'), true);
            
            // выход, если нет списка сотрудников
            if ($eids == NULL) return 1;
            
            $salary = new Salary($month, $year, $eids);
            $salary->addReport($start, $finish);
            
            //return $this->render('index', ['result' => $salary]);
            
            // СОЗДАЕМ ОБЪЕКТ EXCEL
            
            $phpexcel = new \PHPExcel();
            $page = $phpexcel->setActiveSheetIndex(0);
            $page->setTitle('ЗАРПЛАТА');
            
            // НАБОРЫ СТИЛЕЙ ДЛЯ EXCEL
            // шрифт для документа
            $arDocFont = array(
                'font'  => array(
                    //'bold'  => true,
                    //'color' => array('rgb' => '778899'),
                    //'size'  => 13,
                    'name'  => 'Arial'
                ),
            );
            // заголовок таблицы
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
            $arBorderStyle = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000')
                    )
                ),
            );
            $arSalaryStyle = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000')
                    )
                ),
                'font'  => array(
                    'bold'  => true,
                ),
            );
            $arEmptyStyle = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000')
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ffcccc')
                ),
            );
            $arNumEmptyStyle = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000')
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ffcccc')
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    //'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
            );
            
            
            $page->getDefaultStyle()->applyFromArray($arDocFont);
            
            //$eid = implode(",", $eids);
           
            $offset = 5;
            
            $page->setCellValue('A1', 'Отчет по зарплате');     $page->setCellValue('C1', ShowMonthYear($year.'-'.$month));
            $page->setCellValue('A3', 'Период расчёта:');       $page->setCellValue('C3', ShowDigiDate($start).' - '.ShowDigiDate($finish));
            $phpexcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $phpexcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $phpexcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $phpexcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $phpexcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $phpexcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $phpexcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $phpexcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $phpexcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            $phpexcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
            $phpexcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
            $phpexcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
            $phpexcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
            $phpexcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
            
            $page->setCellValue('A5', '№ п/п');         $page->getStyle('A5')->applyFromArray($arHeadStyle);
            $page->setCellValue('B5', 'ID');            $page->getStyle('B5')->applyFromArray($arHeadStyle);
            $page->setCellValue('C5', 'Фамилия И.О.');  $page->getStyle('C5')->applyFromArray($arHeadStyle);
            $page->setCellValue('D5', 'Должность');     $page->getStyle('D5')->applyFromArray($arHeadStyle);
            $page->setCellValue('E5', 'Оклад');         $page->getStyle('E5')->applyFromArray($arHeadStyle);
            $page->setCellValue('F5', 'Премия');        $page->getStyle('F5')->applyFromArray($arHeadStyle);
            $page->setCellValue('G5', 'ЧасТаб');        $page->getStyle('G5')->applyFromArray($arHeadStyle);
            $page->setCellValue('H5', 'ЧасРаб');        $page->getStyle('H5')->applyFromArray($arHeadStyle);
            $page->setCellValue('I5', 'ЧасСверх');      $page->getStyle('I5')->applyFromArray($arHeadStyle);
            $page->setCellValue('J5', 'Задач');         $page->getStyle('J5')->applyFromArray($arHeadStyle);
            $page->setCellValue('K5', 'ОкладИтог');     $page->getStyle('K5')->applyFromArray($arHeadStyle);
            $page->setCellValue('L5', 'ПремИсхИтог');   $page->getStyle('L5')->applyFromArray($arHeadStyle);
            $page->setCellValue('M5', 'ПремияИтог');   $page->getStyle('M5')->applyFromArray($arHeadStyle);
            $page->setCellValue('N5', 'СуммаИтог');     $page->getStyle('N5')->applyFromArray($arHeadStyle);
            
            
            for ($i = 1; $i <= count($salary->result); $i++) {
                
                
                $phour = $salary->result[$i]['report']['phour'];
                $whour = $salary->result[$i]['report']['whour'];
                $shour = $salary->result[$i]['report']['_shour'];
                $cost = $salary->result[$i]['report']['cost'];
                
                
                $total = 0;
                
                // если есть данные о начислениях, проводим вычисления
                if (array_key_exists('salary', $salary->result[$i])) {
                    
                    $page->setCellValue('A'.($i + $offset), $i);                                        $page->getStyle('A'.($i + $offset))->applyFromArray($arBorderStyle);
                    $page->setCellValue('B'.($i + $offset), $salary->result[$i]['eid']);                $page->getStyle('B'.($i + $offset))->applyFromArray($arBorderStyle);
                    $page->setCellValue('C'.($i + $offset), $salary->result[$i]['fio_short']);          $page->getStyle('C'.($i + $offset))->applyFromArray($arBorderStyle);
                    $page->setCellValue('D'.($i + $offset), $salary->result[$i]['post']);               $page->getStyle('D'.($i + $offset))->applyFromArray($arBorderStyle);
                    $page->setCellValue('G'.($i + $offset), $salary->result[$i]['report']['phour']);    $page->getStyle('G'.($i + $offset))->applyFromArray($arBorderStyle);
                    $page->setCellValue('H'.($i + $offset), $salary->result[$i]['report']['whour']);    $page->getStyle('H'.($i + $offset))->applyFromArray($arBorderStyle);
                    $page->setCellValue('I'.($i + $offset), $salary->result[$i]['report']['_shour']);   $page->getStyle('I'.($i + $offset))->applyFromArray($arBorderStyle);
                    $page->setCellValue('J'.($i + $offset), $salary->result[$i]['report']['cost']);
                        $page->getStyle('J'.($i + $offset))->applyFromArray($arBorderStyle);
                        if (count($salary->result[$i]['report']['warning']) > 0) $page->getStyle('J'.($i + $offset))->applyFromArray($arSalaryStyle);
                    
                    $payment = $salary->result[$i]['salary'][0]['payment'];
                    $award = $salary->result[$i]['salary'][0]['award'];
                    
                    
                    $page->setCellValue('E'.($i + $offset), $salary->result[$i]['salary'][0]['payment']);   $page->getStyle('E'.($i + $offset))->applyFromArray($arBorderStyle);
                    $page->setCellValue('F'.($i + $offset), $salary->result[$i]['salary'][0]['award']);     $page->getStyle('F'.($i + $offset))->applyFromArray($arBorderStyle);
                    
                    $base = 0;
                    $awrd = 0;
                    $ext = 0;
                    $info = '';
                    
                    // цикл по выплатам
                    for ($s = 0; $s < count($salary->result[$i]['salary'][0]['payout']); $s++) {
                        
                        $payout = getPayout($payment, $award,
                                            $salary->result[$i]['salary'][0]['payout'][$s]['info'],
                                            $phour, $whour, $shour, $cost,
                                            $salary->result[$i]['salary'][0]['payout'][$s]['base'],
                                            $salary->result[$i]['salary'][0]['payout'][$s]['depends']);
                        
                        //$salary->result[$i]['salary'][0]['payout'][$s]['info'];
                        
                        $base = $base + $payout['base'];
                        $awrd = $awrd + $payout['award'];
                        $ext = $ext + $payout['ext'];
                        $info = $info.'\n'.$payout['title'];
                       
                    }
                    //$page->setCellValue('O'.($i + $offset), $info);
                    $page->setCellValue('K'.($i + $offset), $base);                     $page->getStyle('K'.($i + $offset))->applyFromArray($arBorderStyle);
                    $page->setCellValue('L'.($i + $offset), $awrd);                     $page->getStyle('L'.($i + $offset))->applyFromArray($arBorderStyle);
                    $page->setCellValue('M'.($i + $offset), $ext);                      $page->getStyle('M'.($i + $offset))->applyFromArray($arBorderStyle);   
                    $page->setCellValue('N'.($i + $offset), Ceil(($base + $awrd + $ext) / 10) * 10);      $page->getStyle('N'.($i + $offset))->applyFromArray($arSalaryStyle);
                    //$page->setCellValue('O'.($i + $offset), $info);
                }
                // нет данных о начислениях    
                else {
                    
                    $page->setCellValue('A'.($i + $offset), $i);                                        $page->getStyle('A'.($i + $offset))->applyFromArray($arEmptyStyle);
                    $page->setCellValue('B'.($i + $offset), $salary->result[$i]['eid']);                $page->getStyle('B'.($i + $offset))->applyFromArray($arEmptyStyle);
                    $page->setCellValue('C'.($i + $offset), $salary->result[$i]['fio_short']);          $page->getStyle('C'.($i + $offset))->applyFromArray($arEmptyStyle);
                    $page->setCellValue('D'.($i + $offset), $salary->result[$i]['post']);               $page->getStyle('D'.($i + $offset))->applyFromArray($arEmptyStyle);
                    $page->setCellValue('G'.($i + $offset), $salary->result[$i]['report']['phour']);    $page->getStyle('G'.($i + $offset))->applyFromArray($arEmptyStyle);
                    $page->setCellValue('H'.($i + $offset), $salary->result[$i]['report']['whour']);    $page->getStyle('H'.($i + $offset))->applyFromArray($arEmptyStyle);
                    $page->setCellValue('I'.($i + $offset), $salary->result[$i]['report']['_shour']);   $page->getStyle('I'.($i + $offset))->applyFromArray($arEmptyStyle);
                    $page->setCellValue('J'.($i + $offset), $salary->result[$i]['report']['cost']);
                        $page->getStyle('J'.($i + $offset))->applyFromArray($arEmptyStyle);
                        if (count($salary->result[$i]['report']['warning']) > 0) $page->getStyle('J'.($i + $offset))->applyFromArray($arSalaryStyle);
                    
                    $page->setCellValue('E'.($i + $offset), '- - -');                                     $page->getStyle('E'.($i + $offset))->applyFromArray($arNumEmptyStyle);
                    $page->setCellValue('F'.($i + $offset), '- - -');                                     $page->getStyle('F'.($i + $offset))->applyFromArray($arNumEmptyStyle);
                    $page->setCellValue('K'.($i + $offset), '- - -');                                     $page->getStyle('K'.($i + $offset))->applyFromArray($arNumEmptyStyle);
                    $page->setCellValue('L'.($i + $offset), '- - -');                                     $page->getStyle('L'.($i + $offset))->applyFromArray($arNumEmptyStyle);
                    $page->setCellValue('M'.($i + $offset), '- - -');                                     $page->getStyle('M'.($i + $offset))->applyFromArray($arNumEmptyStyle);
                    $page->setCellValue('N'.($i + $offset), '- - -');                                     $page->getStyle('N'.($i + $offset))->applyFromArray($arNumEmptyStyle);
                }
                
                //$page->setCellValue('Z'.($i + $offset), $total);
            }
            
            //sleep(1);
            
            
            
            
            
            // ОСНОВНАЯ ПРОГРАММА
            
            
            
            // СОХРАНЯЕМ ФАЙЛ EXCEL НА ДИСК
            
            $objWriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');
            $xls = "salary_".date("YmdHis", time() + 3600 * 3).".xlsx";
            $objWriter->save("../web/files/docs/".$xls);
            
            return '../files/docs/'.$xls;
            //return Yii::$app->request->url;
        }
        return 0;
    }


///ajax/save-modal-table-array?salary_id=' + salary_id + '?data=' + JSON.stringify(arr), getSaveResult); 
//'/ajax/save-modal-table-row?id=' + id + '&salary_id=' + 0 + '&mode=' + mode + '&base=' + base + '&depends=' + deps + '&summa=' + summ +  '&type=' + type + '&info=' + info
// http://signaltv/ajax/insert-paysalary?id=-1&eid=25&sldate=201901&salary=13200&award=5000
    
// /ajax/create-salary-from-template?eid=" + eid + "&sldate=" + sldate + '&salary=' + salary + '&award=' + award + '&summa=' + summa

//    public function actionDuplicatePaySalary() {
//        
//        if (Yii::$app->request->isGet) {
//            $eid = htmlspecialchars(Yii::$app->request->get('eid', ''));
//            $eid = htmlspecialchars(Yii::$app->request->get('sldate', ''));
//            
//            
//        }
//    }
    
    
}
