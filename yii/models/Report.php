<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\base\Model;
use app\models\Employe;
use app\models\Relemp;
use Yii;
use app\models\Timesheet;
use app\models\TsComment;

/**
 * Description of Report
 *
 * @author vitt
 */
class Report extends Model {
    
    public $employe;
    public $table;
    public $task;
    public $report;
    public $personal;
    public $days;           // массив статусов дней за период отчета
    public $_table;         // массив всех значений табеля для выбранных сотрудников за отчетный период

    public $speed; // хранит время выполнения обработки

    private $eids;

    private function getArrayEid() {
        $this->eids = array();
        foreach ($this->employe as $item) {
            $this->eids[] = $item['eid'];
        }
        sort($this->eids);
    }


//    public function __construct($LoadEmploye = false, $did = 0) {
//        
//        parent::__construct();
//        if ($LoadEmploye) {
//            if (Yii::$app->user->identity->role[4] == 'f') {
//
//            // полный доступ    
//            // уточняем, получили ли данные о номере отдела
//                if ($did == 0)
//                     $this->employe = Employe::find()->asArray()->where(['!=', 'status', 0])->orderBy('fio_short')->all();
//                else $this->employe = Employe::find()->asArray()->where(['!=', 'status', 0])->andWhere(['=', 'did', $did])->orderBy('fio_short')->all();
//            }
//            else {
//
//                // доступ к данным ограничен
//                $this->employe = Employe::find()->asArray()->where(['!=', 'status', 0])->andWhere(['=', 'did', Yii::$app->user->identity->did])->orderBy('fio_short')->all();
//            }
//        }
//    }

    // возвращает список сотрудников с выполненными работами и часами работы за указанный промужуток времени
    
    public function getDateRep($start, $finish, $did = 0) {
        
        $this->speed = microtime(true);

        // плучаем список сотрудников
        $this->employe = Employe::getEmployeTable(date("Y-m-d"), $did);
        $this->getArrayEid();
        
        $this->table = Timesheet::find()->select('eid, shift, count(shift) as scount')->asArray()->where(['IN', 'eid', $this->eids])->andWhere(['>=', 'tsdate', $start])->andWhere(['<=', 'tsdate', $finish])->groupBy('eid, shift')->having('eid')->all();
        $this->_table = Timesheet::find()->select('eid, tsdate, shift')->asArray()->where(['IN', 'eid', $this->eids])->andWhere(['>=', 'tsdate', $start])->andWhere(['<=', 'tsdate', $finish])->all();
        
        $this->days = Year::getDaysArray($start, $finish);
        
        // получаем список задач за отчетный период
        $this->task = Task::find()->asArray()->with('employe', 'worklist', 'whour')->where(['=', 'status', 1])->andWhere(['>=', 'dttask', $start])->andWhere(['<=', 'dttask', $finish])->all();
        
        $No = 0;
        foreach ($this->employe as $employe) {
            
//            if ($employe['eid'] != 25) continue;

            $this->report[$No]['eid'] = $employe['eid'];                // id сотрудника
            $this->report[$No]['ffio'] = $employe['fio'];               // фио
            $this->report[$No]['sfio'] = $employe['fio_short'];         // короткое фио
            $this->report[$No]['post'] = $employe['post'];              // должность
            $this->report[$No]['did'] = $employe['did'];                // отдел
            $this->report[$No]['shift'] = $employe['shift'];            // график по табелю на текущий момент
            $this->report[$No]['cost'] = 0;                             // суммарное количество работ
            $this->report[$No]['hour'] = 0;                             // суммарно отработанное время по задачам
            //$this->report[$No]['tids'] = '';                            // 
            $this->report[$No]['table'] = array();                      // 
//            $this->report[$No]['_table'] = 0;
            $this->report[$No]['phour'] = 0;                            // часы по производственному календарю
            $this->report[$No]['whour'] = 0;                            // часы по табелю
            $this->report[$No]['shour'] = 0;                            // праздничные часы
            $this->report[$No]['tab_task'] = $employe['tab_task'];      // передаем способ расчета часов для сотрудника (0 - по табелю, 1 - по задачам)
            
            $this->report[$No]['_shour'] = 0;                           // переработка времени по новому расчету
            $this->report[$No]['warning'] = array();                    // массив для хранения номеров задач, в которых используется поправочный коэффициент
            
// =================================================================================================================================================================================

            // проходим циклом по дням календаря и табелю для каждого сотрудника 
            // 1 - по дням
            foreach ($this->days as $day) {
                
                $have_tasks = false; // устанавливаем, что на указанную дату пока не найдены задачи
                
                // 1.1 - проходим по задачам
                $task_hour = 0;
                foreach ($this->task as $key => $task) {
                    
                    // ищем совпадение в задаче для сотрудника на указанную дату
                    
                    foreach ($task['employe'] as $emp) {
                        
                        if (($emp['eid'] == $employe['eid']) && ($task['dttask'] == $day[1])) {
                        
                            // определяем стоимость и продолжительность задачи
                            $task_hour = $task_hour + sumArray($task['whour'], 'hcount');         // общая стоимость задачи
                            $task_sumcost = 0;                                       // общая стоимость задачи
                            $task_empcount = count($task['employe']);                // количество сотрудников в задаче
                            $cft = 1;
                            foreach ($this->task[$key]['worklist'] as $work) {
                                //echo var_dump($work); die;
                                if ($work['typework']['status'] == 9) {
                                    $this->report[$No]['warning'][] = $task['tid'];
                                    $cft = $cft * $work['cost'];
                                }
                                    
                                else
                                    $task_sumcost = $task_sumcost + $work['nrepeat'] * $work['cost'];
                            }
                            //$this->report[$No]['whour'] = $this->report[$No]['whour'] + $task_hour;                  // время по задачам
                            
                            $this->report[$No]['cost'] = ($this->report[$No]['cost'] + $task_sumcost / $task_empcount * $cft);  // стоимость по задачам

                        }
                        
                    }
                    $this->report[$No]['cost'] = number_format($this->report[$No]['cost'], 2);
                    if ($task_hour > 0) $have_tasks = true; // есть задачи с указанием времени выполнения
                }
//                echo $day[1].' = '.$task_hour.'<br>';
//                if ($this->report[$No]['eid'] == 13) echo $task_hour.' ';
                // прошли по задачам
                
                // 1.2 - по табелю для сотрудника и указанной даты
                
                $day_eid_shift = '';
                foreach ($this->_table as $table) {

                    if (($day[1] == $table['tsdate']) && ($table['eid'] == $employe['eid'])) {

                        // есть запись
                        $day_eid_shift = $table['shift'];
                    }
                }
                //$this->report[$No][$day[1]] = $day_eid_shift;
                
                // сейчас у нас есть все данные по сотруднику:
                // 1. смена
                // 2. время по задачам за день
                // 3. стоимость суммарная за день
                // 4. информация о текущем дне по производственному календарю
                
                // АНАЛИЗ ДНЯ
                
                // ОСНОВНОЙ МОМЕНТ РАСЧЕТА - НАЛИЧИЕ ЗАДАЧ НА ДЕНЬ (с указанием времени выполнения)
                
                if ($have_tasks) {
                    
                    // А. ЕСТЬ ЗАДАЧИ (время считается полностью из задач)
                    // рабочий день
                    if ($day[0] == 0) {

                        $this->report[$No]['phour'] = $this->report[$No]['phour'] + 8;

                        if ($day_eid_shift == '8') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8; //$task_hour;
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 8 + $task_hour;
                        }
                        if ($day_eid_shift == 'Д') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12 + $task_hour;
                        }
                        if ($day_eid_shift == 'Н') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12 + $task_hour;
                        }
                        if ($day_eid_shift == 'С') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12 + $task_hour;
                        }
                        if ($day_eid_shift == 'К') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + $task_hour;
                        }
                        if ($day_eid_shift == 'Ч') {

                        }
                        if ($day_eid_shift == 'О') {

                        }
                        if ($day_eid_shift == 'Б') {

                        }
                        if ($day_eid_shift == 'З') {

                        }
                        if ($day_eid_shift == 'Х') {

                        }
                        if ($day_eid_shift == '') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + $task_hour;
                        }

                    }

                    // выходной день
                    if ($day[0] == 1) {

                        if ($day_eid_shift == '8') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 8 + $task_hour * 2;
                        }
                        if ($day_eid_shift == 'Д') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12 + $task_hour;
                        }
                        if ($day_eid_shift == 'Н') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12 + $task_hour;
                        }
                        if ($day_eid_shift == 'С') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0; //$task_hour;
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12 + $task_hour;
                        }
                        if ($day_eid_shift == 'К') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + $task_hour; // * 2; *
                        }
                        if ($day_eid_shift == 'Ч') {

                        }
                        if ($day_eid_shift == 'О') {

                        }
                        if ($day_eid_shift == 'Б') {

                        }
                        if ($day_eid_shift == 'З') {

                        }
                        if ($day_eid_shift == 'Х') {

                        }
                        if ($day_eid_shift == '') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + $task_hour;
                        }
                    }
                
                    // короткий день
                    if ($day[0] == 2) {

                        $this->report[$No]['phour'] = $this->report[$No]['phour'] + 7;

                        if ($day_eid_shift == '8') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 7 + $task_hour;
                        }
                        if ($day_eid_shift == 'Д') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 11 + $task_hour;
                        }
                        if ($day_eid_shift == 'Н') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 11 + $task_hour;
                        }
                        if ($day_eid_shift == 'С') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 11 + $task_hour;
                        }
                        if ($day_eid_shift == 'К') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + $task_hour;
                        }
                        if ($day_eid_shift == 'Ч') {

                        }
                        if ($day_eid_shift == 'О') {

                        }
                        if ($day_eid_shift == 'Б') {

                        }
                        if ($day_eid_shift == 'З') {

                        }
                        if ($day_eid_shift == 'Х') {

                        }
                        if ($day_eid_shift == '') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7;
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + $task_hour;
                        }
                    }
                
                    // праздничный день ??????? ПРАВИТЬ И СЧИТАТЬ
                    if ($day[0] == 3) {

                        if ($day_eid_shift == '8') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + $task_hour * 2;
                        }
                        if ($day_eid_shift == 'Д') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12 + $task_hour * 2;
                        }
                        if ($day_eid_shift == 'Н') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12 + $task_hour + 4;
                        }
                        if ($day_eid_shift == 'С') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12 + $task_hour * 2;
                        }
                        if ($day_eid_shift == 'К') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + $task_hour * 2;
                        }
                        if ($day_eid_shift == 'Ч') {

                        }
                        if ($day_eid_shift == 'О') {

                        }
                        if ($day_eid_shift == 'Б') {

                        }
                        if ($day_eid_shift == 'З') {

                        }
                        if ($day_eid_shift == 'Х') {

                        }

                    }
                }
                else {
                    
                    // Б. НЕТ ЗАДАЧ (время считается из табеля /переработка только праздничные дни/)
                    // рабочий день
                    if ($day[0] == 0) {

                        $this->report[$No]['phour'] = $this->report[$No]['phour'] + 8;

                        if ($day_eid_shift == '8') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8;
                            // нет задач где они должны быть (для сотрудников для которых учет ведется по задачам и день выставлен в табеле как рабочий)
                            if ($this->report[$No]['tab_task'] == 1) 
                                $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 8;
                        }
                        if ($day_eid_shift == 'Д') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8;
                        }
                        if ($day_eid_shift == 'Н') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8;
                        }
                        if ($day_eid_shift == 'С') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8;
                            // нет задач где они должны быть (для сотрудников для которых учет ведется по задачам и день выставлен в табеле как рабочий)
                            if ($this->report[$No]['tab_task'] == 1) 
                                $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12;
                        }
                        if ($day_eid_shift == 'К') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 8;
                        }
                        if ($day_eid_shift == 'Ч') {

                        }
                        if ($day_eid_shift == 'О') {

                        }
                        if ($day_eid_shift == 'Б') {

                        }
                        if ($day_eid_shift == 'З') {

                        }
                        if ($day_eid_shift == 'Х') {

                        }
                        if ($day_eid_shift == '') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8;
                        }

                    }

                    // выходной день
                    if ($day[0] == 1) {

                        if ($day_eid_shift == '8') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0;
                        }
                        if ($day_eid_shift == 'Д') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0;
                        }
                        if ($day_eid_shift == 'Н') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0;
                        }
                        if ($day_eid_shift == 'С') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0;
                            // нет задач где они должны быть (для сотрудников для которых учет ведется по задачам и день выставлен в табеле как рабочий)
                            if ($this->report[$No]['tab_task'] == 1) 
                                $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12;
                        }
                        if ($day_eid_shift == 'К') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 8;
                        }
                        if ($day_eid_shift == 'Ч') {

                        }
                        if ($day_eid_shift == 'О') {

                        }
                        if ($day_eid_shift == 'Б') {

                        }
                        if ($day_eid_shift == 'З') {

                        }
                        if ($day_eid_shift == 'Х') {

                        }
                    }
                
                    // короткий день
                    if ($day[0] == 2) {

                        $this->report[$No]['phour'] = $this->report[$No]['phour'] + 7;

                        if ($day_eid_shift == '8') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7;
                            // нет задач, где они должны быть (для сотрудников для которых учет ведется по задачам и день выставлен в табеле как рабочий)
                            if ($this->report[$No]['tab_task'] == 1) 
                                $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 7;
                        }
                        if ($day_eid_shift == 'Д') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7;
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 1;
                        }
                        if ($day_eid_shift == 'Н') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7;
                        }
                        if ($day_eid_shift == 'С') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7;
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 1;
                        }
                        if ($day_eid_shift == 'К') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 7;
                        }
                        if ($day_eid_shift == 'Ч') {

                        }
                        if ($day_eid_shift == 'О') {

                        }
                        if ($day_eid_shift == 'Б') {

                        }
                        if ($day_eid_shift == 'З') {

                        }
                        if ($day_eid_shift == 'Х') {

                        }
                        if ($day_eid_shift == '') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7;
                        }
                    }
                
                    // праздничный день
                    if ($day[0] == 3) {

                        if ($day_eid_shift == '8') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0;
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 16;
                        }
                        if ($day_eid_shift == 'Д') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0;
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 12;//24;
                        }
                        if ($day_eid_shift == 'Н') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0;
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 12;
                        }
                        if ($day_eid_shift == 'С') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0;
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 12;
                        }
                        if ($day_eid_shift == 'К') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 16;
                        }
                        if ($day_eid_shift == 'Ч') {

                        }
                        if ($day_eid_shift == 'О') {

                        }
                        if ($day_eid_shift == 'Б') {

                        }
                        if ($day_eid_shift == 'З') {

                        }
                        if ($day_eid_shift == 'Х') {

                        }

                    }
                }
                

                
                
                
                
                
                

// =================================================================================================================================================================================
            
            
                // выполняем подсчет смен

                foreach ($this->table as $table) {

                    if ($employe['eid'] == $table['eid']) {

                        $this->report[$No]['table'][$table['shift']] = $table['scount'];
                    }
                }
//            if ($this->report[$No]['eid'] == 25) echo $this->report[$No]['whour'].'='.$this->report[$No]['_shour'].'<br>';
            }
           
            $No++;
        }

        $this->speed = microtime(true) - $this->speed;
        return $this;
    }
    
    // возвращает список работ для сотрудника за указанный период времени
    
    public function getPersonalRep($start, $finish, $eid) {
        
        
        $this->report = array();
        
        // если не полный доступ, то нужно сделать проверку соответствия номера отдела
        if (Yii::$app->user->identity->role[4] == 'f') {
            
            $employe = Employe::find()->asArray()->where(['=', 'eid', $eid])->limit(1)->one();
        }
        else {
            $employe = Employe::find()->asArray()->where(['=', 'eid', $eid])->andWhere(['=', 'did', Yii::$app->user->identity->did])->limit(1)->one();
        }
        if (!empty($employe)) {
            //var_dump($employe); die;
            $this->report['employe'] = $employe;
            $this->report['table'] = Timesheet::find()->asArray()->where(['=', 'eid', $eid])->andWhere(['>=', 'tsdate', $start])->andWhere(['<=', 'tsdate', $finish])->orderBy('tsdate')->all();
            $this->report['comment'] = TsComment::find()->asArray()->where(['=', 'eid', $eid])->andWhere(['>=', 'cmdate', $start])->andWhere(['<=', 'cmdate', $finish])->orderBy('cmdate')->all();
            $task = Task::find()-> asArray()-> where(['>=', 'dttask', $start])->andWhere(['<=', 'dttask', $finish])->with('worklist', 'relemp', 'whour')->orderBy('dttask, task.tid')->all();

//            $task = Task::find()->
//                    asArray()->
//                    select('task.tid, task.dttask, task.title, task.status, work.nrepeat, work.cost, (SELECT COUNT(`relemp`.`tid`) FROM `relemp` WHERE `task`.`tid`=`relemp`.`tid`) as `empcount`')->
//                    where(['>=', 'dttask', $start])->
//                    andWhere(['<=', 'dttask', $finish])->
//                    leftJoin('relemp', '{{task}}.tid={{relemp}}.tid')->
//                    leftJoin('employe', '{{relemp}}.eid={{employe}}.eid')->
//                    andWhere(['=', '{{employe}}.eid', $eid])->
//                    //with('workrep', 'relemp')->
//                    leftJoin('work', '{{task}}.tid={{work}}.tid')->
//                    orderBy('dttask, task.tid')->
//                    all();
            // получили список работ, начинаем обработку
            // счетчик для заполнения результирующего отчета
            $c = 0; 
            
            for ($i = 0; $i < count($task); $i++) {
                
                // принимаем, что сотрудник пока не присутствует
                $present = false;
                $cost = 0; // стоимость работ задачи сотрудника
                
                // ищем в списке связей с сотрудниками eid сотрудника
                foreach ($task[$i]['relemp'] as $relemp) {
                    
                    // если присутствует, то устанавливаем флаг и анализируем список работ
                    if ($relemp['eid'] == $eid) {
                        
                        $present = true;
                        break;
                    }
                }
                    
                // в случае если сотрудник присутствует, считаем работы и добавляем задачу в итоговый отчет
                if ($present) {
                    
                    $empcount = count($task[$i]['relemp']);
                    $workcount = sumArray($task[$i]['whour'], 'hcount');//count($task[$i]['reletm']);sumArray($task[$i]['whour'], 'hcount');
                    
                    // считаем количество часов работы
                    foreach ($task[$i]['relemp'] as $relemp) {
                    
                        // если присутствует, то устанавливаем флаг и анализируем список работ
                        if ($relemp['eid'] == $eid) {

                            $present = true;
                            break;
                        }
                    }
                    $warning = false;
                    $cft = 1;
                    foreach ($task[$i]['worklist'] as $work) {
                        
                        if ($work['typework']['status'] == 9) {
                            $cft = $cft * $work['cost'];
                            //$warning[] = $task[$i]['tid'];
                            $warning = true;
                        }
                        else 
                            $cost = $cost + ($work['nrepeat'] * $work['cost']);
                    }
                    $cost = $cost * $cft;
                    
                    if ($empcount > 0) $cost = number_format (($cost / $empcount), 2); 
                    // ??? нужна ли проверка empcount на ноль:
                        // если сотрудника нет в списке, то все равно эта часть не выполняется,
                        // а если есть, то в любом случае количество будет больше нуля
                    
                    $c++;
                    $this->report['task'][$c]['tid'] = $task[$i]['tid'];
                    $this->report['task'][$c]['dttask'] = $task[$i]['dttask'];
                    $this->report['task'][$c]['title'] = $task[$i]['title'];
                    $this->report['task'][$c]['status'] = $task[$i]['status'];
                    $this->report['task'][$c]['hour'] = $workcount;
                    $this->report['task'][$c]['empcount'] = $empcount;
                    $this->report['task'][$c]['workcount'] = $workcount;
                    $this->report['task'][$c]['cost'] = $cost;
                    $this->report['task'][$c]['warning'] = $warning;
                }
                    
            }
            
            // !!! ошибка подсчета работ - переделать функцию
            
            //$this->report['task'] = $task;
        }
        return $this->report;
        ?><pre><?= var_dump($task) ?></pre><?php die;
    }
    
    //  возвращает список задач за указанный промужуток времени
    
    public function getTaskRep($start, $finish, $did = 0) {
        // сначала анализируем права пользователя на доступ к отчетам
        // l - только данные своего отдела
        // f - все данные + возможность выбора отдела
        
        $this->report = array();
        
        if (Yii::$app->user->identity->role[4] == 'f') {
            
            // уточняем, получили ли данные о номере отдела
            if ($did == 0)
                 $this->report = Task::find()->asArray()->with('worklist')->where(['>=', 'dttask', $start])->andWhere(['<=', 'dttask', $finish])->orderBy('dttask')->all();
            else $this->report = Task::find()->asArray()->with('worklist')->where(['=', 'did', $did])->andWhere(['>=', 'dttask', $start])->andWhere(['<=', 'dttask', $finish])->orderBy('dttask')->all();
            
            //$this->employe = Employe::find()->asArray()->where(['!=', 'status', 0])->andWhere(['=', 'did', Yii::$app->user->identity->did])->orderBy('fio_short')->all();
        }
        else {
            
            // доступ к данным ограничен
            $this->report = Task::find()->asArray()->with('worklist')->where(['=', 'did', Yii::$app->user->identity->did])->andWhere(['>=', 'dttask', $start])->andWhere(['<=', 'dttask', $finish])->orderBy('dttask')->all();
        }
        
        // обработка по поиску применения поправочного коэффициента
        
        foreach ($this->report as $key => $task) {
            $this->report[$key]['warning'] = false;
            foreach ($task['worklist'] as $work) {
                if ($work['typework']['status'] == 9) {
                    $this->report[$key]['warning'] = true;
                }
            }
        }
        
        return $this->report;
    }
    
    public function LoadEmploye($did = 0) {
        
         if (Yii::$app->user->identity->role[4] == 'f') {
            
            // полный доступ            
            // уточняем, получили ли данные о номере отдела
            if ($did == 0)
                 $this->employe = Employe::find()->asArray()->where(['!=', 'status', 0])->orderBy('fio_short')->all();
            else $this->employe = Employe::find()->asArray()->where(['!=', 'status', 0])->andWhere(['=', 'did', $did])->orderBy('fio_short')->all();
        }
        else {
            
            // доступ к данным ограничен
            $this->employe = Employe::find()->asArray()->where(['!=', 'status', 0])->andWhere(['=', 'did', Yii::$app->user->identity->did])->orderBy('fio_short')->all();
        }
    }
    
    public function getDateRepOnEid($start, $finish, $eid) {
        
        $this->speed = microtime(true);
        
        // плучаем список сотрудников
        $this->employe = Employe::find()->asArray()->where(['=', 'eid', $eid])->all();
        
        $this->table = Timesheet::find()->select('eid, shift, count(shift) as scount')->asArray()->where(['=', 'eid', $eid])->andWhere(['>=', 'tsdate', $start])->andWhere(['<=', 'tsdate', $finish])->groupBy('eid, shift')->having('eid')->all();
        $this->_table = Timesheet::find()->select('eid, tsdate, shift')->asArray()->where(['=', 'eid', $eid])->andWhere(['>=', 'tsdate', $start])->andWhere(['<=', 'tsdate', $finish])->all();
        
        $this->days = Year::getDaysArray($start, $finish);
        
        // получаем список задач за отчетный период
        $this->task = Task::find()->asArray()->with('employe', 'worklist', 'whour')->where(['=', 'status', 1])->andWhere(['>=', 'dttask', $start])->andWhere(['<=', 'dttask', $finish])->all();
        
        $No = 0;
        foreach ($this->employe as $employe) {
            
//            if ($employe['eid'] != 25) continue;

            $this->report[$No]['eid'] = $employe['eid'];                // id сотрудника
            $this->report[$No]['ffio'] = $employe['fio'];               // фио
            $this->report[$No]['sfio'] = $employe['fio_short'];         // короткое фио
            $this->report[$No]['post'] = $employe['post'];              // должность
            $this->report[$No]['did'] = $employe['did'];                // отдел
            //$this->report[$No]['shift'] = $employe['shift'];            // график по табелю на текущий момент
            $this->report[$No]['cost'] = 0;                             // суммарное количество работ
            $this->report[$No]['hour'] = 0;                             // суммарно отработанное время по задачам
            //$this->report[$No]['tids'] = '';                            // 
            $this->report[$No]['table'] = array();                      // 
//            $this->report[$No]['_table'] = 0;
            $this->report[$No]['phour'] = 0;                            // часы по производственному календарю
            $this->report[$No]['whour'] = 0;                            // часы по табелю
            $this->report[$No]['shour'] = 0;                            // праздничные часы
            $this->report[$No]['tab_task'] = $employe['tab_task'];      // передаем способ расчета часов для сотрудника (0 - по табелю, 1 - по задачам)
            
            $this->report[$No]['_shour'] = 0;                           // переработка времени по новому расчету
            
// =================================================================================================================================================================================

            // проходим циклом по дням календаря и табелю для каждого сотрудника 
            // 1 - по дням
            foreach ($this->days as $day) {
                
                $have_tasks = false; // устанавливаем, что на указанную дату пока не найдены задачи
                
                // 1.1 - проходим по задачам
                $task_hour = 0;
                foreach ($this->task as $key => $task) {
                    
                    // ищем совпадение в задаче для сотрудника на указанную дату
                    
                    foreach ($task['employe'] as $emp) {
                        
                        if (($emp['eid'] == $employe['eid']) && ($task['dttask'] == $day[1])) {
                        
                            // определяем стоимость и продолжительность задачи
                            $task_hour = $task_hour + sumArray($task['whour'], 'hcount');         // общая стоимость задачи
                            $task_sumcost = 0;                                       // общая стоимость задачи
                            $task_empcount = count($task['employe']);                // количество сотрудников в задаче
                            foreach ($this->task[$key]['worklist'] as $work) {
                                //echo var_dump($work); die;
                                if ($work['typework']['status'] == 9) {
                                    $this->report[$No]['warning'][] = $task['tid'];
                                    $cft = $cft * $work['cost'];
                                }
                                    
                                else
                                    $task_sumcost = $task_sumcost + $work['nrepeat'] * $work['cost'];
                            }
                            //$this->report[$No]['whour'] = $this->report[$No]['whour'] + $task_hour;                  // время по задачам
                            $this->report[$No]['cost'] = $this->report[$No]['cost'] + $task_sumcost/$task_empcount;  // стоимость по задачам

                        }
                        
                    }
                    $this->report[$No]['cost'] = number_format($this->report[$No]['cost'], 2);
                    if ($task_hour > 0) $have_tasks = true; // есть задачи с указанием времени выполнения
                }
//                echo $day[1].' = '.$task_hour.'<br>';
//                if ($this->report[$No]['eid'] == 13) echo $task_hour.' ';
                // прошли по задачам
                
                // 1.2 - по табелю для сотрудника и указанной даты
                
                $day_eid_shift = '';
                foreach ($this->_table as $table) {

                    if (($day[1] == $table['tsdate']) && ($table['eid'] == $employe['eid'])) {

                        // есть запись
                        $day_eid_shift = $table['shift'];
                    }
                }
                //$this->report[$No][$day[1]] = $day_eid_shift;
                
                // сейчас у нас есть все данные по сотруднику:
                // 1. смена
                // 2. время по задачам за день
                // 3. стоимость суммарная за день
                // 4. информация о текущем дне по производственному календарю
                
                // АНАЛИЗ ДНЯ
                
                // ОСНОВНОЙ МОМЕНТ РАСЧЕТА - НАЛИЧИЕ ЗАДАЧ НА ДЕНЬ (с указанием времени выполнения)
                
                if ($have_tasks) {
                    
                    // А. ЕСТЬ ЗАДАЧИ (время считается полностью из задач)
                    // рабочий день
                    if ($day[0] == 0) {

                        $this->report[$No]['phour'] = $this->report[$No]['phour'] + 8;

                        if ($day_eid_shift == '8') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8; //$task_hour;
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 8 + $task_hour;
                        }
                        if ($day_eid_shift == 'Д') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12 + $task_hour;
                        }
                        if ($day_eid_shift == 'Н') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12 + $task_hour;
                        }
                        if ($day_eid_shift == 'С') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12 + $task_hour;
                        }
                        if ($day_eid_shift == 'К') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + $task_hour;
                        }
                        if ($day_eid_shift == 'Ч') {

                        }
                        if ($day_eid_shift == 'О') {

                        }
                        if ($day_eid_shift == 'Б') {

                        }
                        if ($day_eid_shift == 'З') {

                        }
                        if ($day_eid_shift == 'Х') {

                        }
                        if ($day_eid_shift == '') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + $task_hour;
                        }

                    }

                    // выходной день
                    if ($day[0] == 1) {

                        if ($day_eid_shift == '8') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 8 + $task_hour * 2;
                        }
                        if ($day_eid_shift == 'Д') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12 + $task_hour;
                        }
                        if ($day_eid_shift == 'Н') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12 + $task_hour;
                        }
                        if ($day_eid_shift == 'С') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0; //$task_hour;
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12 + $task_hour;
                        }
                        if ($day_eid_shift == 'К') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + $task_hour; // * 2; *
                        }
                        if ($day_eid_shift == 'Ч') {

                        }
                        if ($day_eid_shift == 'О') {

                        }
                        if ($day_eid_shift == 'Б') {

                        }
                        if ($day_eid_shift == 'З') {

                        }
                        if ($day_eid_shift == 'Х') {

                        }
                        if ($day_eid_shift == '') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + $task_hour;
                        }
                    }
                
                    // короткий день
                    if ($day[0] == 2) {

                        $this->report[$No]['phour'] = $this->report[$No]['phour'] + 7;

                        if ($day_eid_shift == '8') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 7 + $task_hour;
                        }
                        if ($day_eid_shift == 'Д') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 11 + $task_hour;
                        }
                        if ($day_eid_shift == 'Н') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 11 + $task_hour;
                        }
                        if ($day_eid_shift == 'С') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 11 + $task_hour;
                        }
                        if ($day_eid_shift == 'К') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + $task_hour;
                        }
                        if ($day_eid_shift == 'Ч') {

                        }
                        if ($day_eid_shift == 'О') {

                        }
                        if ($day_eid_shift == 'Б') {

                        }
                        if ($day_eid_shift == 'З') {

                        }
                        if ($day_eid_shift == 'Х') {

                        }
                        if ($day_eid_shift == '') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7;
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + $task_hour;
                        }
                    }
                
                    // праздничный день ??????? ПРАВИТЬ И СЧИТАТЬ
                    if ($day[0] == 3) {

                        if ($day_eid_shift == '8') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + $task_hour * 2;
                        }
                        if ($day_eid_shift == 'Д') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12 + $task_hour * 2;
                        }
                        if ($day_eid_shift == 'Н') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12 + $task_hour + 4;
                        }
                        if ($day_eid_shift == 'С') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0; //$task_hour; *
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12 + $task_hour * 2;
                        }
                        if ($day_eid_shift == 'К') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + $task_hour * 2;
                        }
                        if ($day_eid_shift == 'Ч') {

                        }
                        if ($day_eid_shift == 'О') {

                        }
                        if ($day_eid_shift == 'Б') {

                        }
                        if ($day_eid_shift == 'З') {

                        }
                        if ($day_eid_shift == 'Х') {

                        }

                    }
                }
                else {
                    
                    // Б. НЕТ ЗАДАЧ (время считается из табеля /переработка только праздничные дни/)
                    // рабочий день
                    if ($day[0] == 0) {

                        $this->report[$No]['phour'] = $this->report[$No]['phour'] + 8;

                        if ($day_eid_shift == '8') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8;
                            // нет задач, где они должны быть (для сотрудников для которых учет ведется по задачам и день выставлен в табеле как рабочий)
                            if ($this->report[$No]['tab_task'] == 1) 
                                $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 8;
                        }
                        if ($day_eid_shift == 'Д') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8;
                        }
                        if ($day_eid_shift == 'Н') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8;
                        }
                        if ($day_eid_shift == 'С') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8;
                            // нет задач где они должны быть (для сотрудников для которых учет ведется по задачам и день выставлен в табеле как рабочий)
                            if ($this->report[$No]['tab_task'] == 1) 
                                $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12;
                        }
                        if ($day_eid_shift == 'К') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 8;
                        }
                        if ($day_eid_shift == 'Ч') {

                        }
                        if ($day_eid_shift == 'О') {

                        }
                        if ($day_eid_shift == 'Б') {

                        }
                        if ($day_eid_shift == 'З') {

                        }
                        if ($day_eid_shift == 'Х') {

                        }
                        if ($day_eid_shift == '') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8;
                        }

                    }

                    // выходной день
                    if ($day[0] == 1) {

                        if ($day_eid_shift == '8') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0;
                        }
                        if ($day_eid_shift == 'Д') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0;
                        }
                        if ($day_eid_shift == 'Н') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0;
                        }
                        if ($day_eid_shift == 'С') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0;
                            // нет задач где они должны быть (для сотрудников для которых учет ведется по задачам и день выставлен в табеле как рабочий)
                            if ($this->report[$No]['tab_task'] == 1) 
                                $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 12;
                        }
                        if ($day_eid_shift == 'К') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 8;
                        }
                        if ($day_eid_shift == 'Ч') {

                        }
                        if ($day_eid_shift == 'О') {

                        }
                        if ($day_eid_shift == 'Б') {

                        }
                        if ($day_eid_shift == 'З') {

                        }
                        if ($day_eid_shift == 'Х') {

                        }
                    }
                
                    // короткий день
                    if ($day[0] == 2) {

                        $this->report[$No]['phour'] = $this->report[$No]['phour'] + 7;

                        if ($day_eid_shift == '8') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7;
                            // нет задач, где они должны быть (для сотрудников для которых учет ведется по задачам и день выставлен в табеле как рабочий)
                            if ($this->report[$No]['tab_task'] == 1) 
                                $this->report[$No]['_shour'] = $this->report[$No]['_shour'] - 7;
                        }
                        if ($day_eid_shift == 'Д') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7;
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 1;
                        }
                        if ($day_eid_shift == 'Н') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7;
                        }
                        if ($day_eid_shift == 'С') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7;
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 1;
                        }
                        if ($day_eid_shift == 'К') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 7;
                        }
                        if ($day_eid_shift == 'Ч') {

                        }
                        if ($day_eid_shift == 'О') {

                        }
                        if ($day_eid_shift == 'Б') {

                        }
                        if ($day_eid_shift == 'З') {

                        }
                        if ($day_eid_shift == 'Х') {

                        }
                        if ($day_eid_shift == '') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7;
                        }
                    }
                
                    // праздничный день
                    if ($day[0] == 3) {

                        if ($day_eid_shift == '8') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0;
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 16;
                        }
                        if ($day_eid_shift == 'Д') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0;
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 12;//24;
                        }
                        if ($day_eid_shift == 'Н') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0;
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 12;
                        }
                        if ($day_eid_shift == 'С') {
                            $this->report[$No]['whour'] = $this->report[$No]['whour'] + 0;
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 12;
                        }
                        if ($day_eid_shift == 'К') {
                            $this->report[$No]['_shour'] = $this->report[$No]['_shour'] + 16;
                        }
                        if ($day_eid_shift == 'Ч') {

                        }
                        if ($day_eid_shift == 'О') {

                        }
                        if ($day_eid_shift == 'Б') {

                        }
                        if ($day_eid_shift == 'З') {

                        }
                        if ($day_eid_shift == 'Х') {

                        }

                    }
                }
                

                
                
                
                
                
                

// =================================================================================================================================================================================
            
            
                // выполняем подсчет смен

                foreach ($this->table as $table) {

                    if ($employe['eid'] == $table['eid']) {

                        $this->report[$No]['table'][$table['shift']] = $table['scount'];
                    }
                }
//            if ($this->report[$No]['eid'] == 25) echo $this->report[$No]['whour'].'='.$this->report[$No]['_shour'].'<br>';
            }
           
            $No++;
        }

        $this->speed = microtime(true) - $this->speed;
        return $this->report[0];
    }

    
}
