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
            $this->report[$No]['_table'] = 0;
            $this->report[$No]['phour'] = 0;                            // часы по производственному календарю
            $this->report[$No]['whour'] = 0;                            // часы по табелю
            $this->report[$No]['shour'] = 0;                            // праздничные часы
            $this->report[$No]['tab_task'] = $employe['tab_task'];      // передаем способ расчета часов для сотрудника (0 - по табелю, 1 - по задачам)
            
            
            // выполняем подсчет смен
            
            foreach ($this->table as $table) {
                
                if ($employe['eid'] == $table['eid']) {
                    
                    $this->report[$No]['table'][$table['shift']] = $table['scount'];
                }
            }
            
            // подсчитываем количество рабочих часов по сменам с учетом производственного календаря
            // т.е. для короткого дня уменьшаем ссмену на 1 час, а для праздничного дня удваиваем
            
            foreach ($this->days as $day) {
                
                $nofind = true;
                foreach ($this->_table as $table) {
                    
                    if (($day[1] == $table['tsdate']) && ($table['eid'] == $employe['eid'])) {
                    
                        // на указанную дату для сотрудника найдена запись в табеле
                        // анализируем установленную в табеле смену
                        
                        $nofind = false;
                        if ($table['shift'] == '8') {
                            if ($day[0] == 0) $this->report[$No]['phour'] = $this->report[$No]['phour'] + 8;
                            if ($day[0] == 2) $this->report[$No]['phour'] = $this->report[$No]['phour'] + 7;
                            if ($day[0] == 0) $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8;
                            if ($day[0] == 2) $this->report[$No]['whour'] = $this->report[$No]['whour'] + 7;
                            if ($day[0] == 3) $this->report[$No]['whour'] = $this->report[$No]['whour'] + 8; //16;
                            if ($day[0] == 3) $this->report[$No]['shour'] = $this->report[$No]['shour'] + 8;
                            //echo $day[0].' '.$table['tsdate'].'>'.$this->report[$No]['whour'].'_ ';
                        }
                        if ($table['shift'] == 'С') {
                            if ($day[0] == 0) $this->report[$No]['phour'] = $this->report[$No]['phour'] + 8;
                            if ($day[0] == 2) $this->report[$No]['phour'] = $this->report[$No]['phour'] + 7;
                            if ($day[0] == 0) $this->report[$No]['whour'] = $this->report[$No]['whour'] + 12;
                            if ($day[0] == 1) $this->report[$No]['whour'] = $this->report[$No]['whour'] + 12;
                            if ($day[0] == 2) $this->report[$No]['whour'] = $this->report[$No]['whour'] + 12; //11; 08.05.19
                            if ($day[0] == 3) $this->report[$No]['whour'] = $this->report[$No]['whour'] + 12; //24;
                            if ($day[0] == 3) $this->report[$No]['shour'] = $this->report[$No]['shour'] + 12;
                            //echo $day[0].' '.$table['tsdate'].'='.$this->report[$No]['whour'].'_ ';
                        }
                        if ($table['shift'] == 'Д') {
                            if ($day[0] == 0) $this->report[$No]['phour'] = $this->report[$No]['phour'] + 8;
                            if ($day[0] == 2) $this->report[$No]['phour'] = $this->report[$No]['phour'] + 7;
                            if ($day[0] == 0) $this->report[$No]['whour'] = $this->report[$No]['whour'] + 12;
                            if ($day[0] == 1) $this->report[$No]['whour'] = $this->report[$No]['whour'] + 12;
                            if ($day[0] == 2) $this->report[$No]['whour'] = $this->report[$No]['whour'] + 12; //11; 08.05.19
                            if ($day[0] == 3) $this->report[$No]['whour'] = $this->report[$No]['whour'] + 12; //24;
                            if ($day[0] == 3) $this->report[$No]['shour'] = $this->report[$No]['shour'] + 12;
                        }
                        if ($table['shift'] == 'Н') {
                            if ($day[0] == 0) $this->report[$No]['phour'] = $this->report[$No]['phour'] + 8;
                            if ($day[0] == 2) $this->report[$No]['phour'] = $this->report[$No]['phour'] + 7;
                            if ($day[0] == 0) $this->report[$No]['whour'] = $this->report[$No]['whour'] + 12;
                            if ($day[0] == 1) $this->report[$No]['whour'] = $this->report[$No]['whour'] + 12;
                            if ($day[0] == 2) $this->report[$No]['whour'] = $this->report[$No]['whour'] + 12; //11; 08.05.19
                            if ($day[0] == 3) $this->report[$No]['whour'] = $this->report[$No]['whour'] + 12; //24;
                            if ($day[0] == 3) $this->report[$No]['shour'] = $this->report[$No]['shour'] + 12;
                        }
                        if ($table['shift'] == 'О') {
                            
                        }
                        if ($table['shift'] == 'Ч') {
                            
                        }
                        if ($table['shift'] == 'К') {
                            // если расчет часов идет по задачам, то компенсация только за работу в праздничный день
                            if ($employe['tab_task'] == 0) {
                                if ($day[0] == 0) $this->report[$No]['shour'] = $this->report[$No]['shour'] + 8;
                                if ($day[0] == 1) $this->report[$No]['shour'] = $this->report[$No]['shour'] + 8;
                                if ($day[0] == 2) $this->report[$No]['shour'] = $this->report[$No]['shour'] + 7;
                                if ($day[0] == 3) $this->report[$No]['shour'] = $this->report[$No]['shour'] + 16;
                            }
                            else { 
                                // // if ($day[0] == 0) $this->report[$No]['shour'] = $this->report[$No]['shour'] - 8;  это не нужно
                                //if ($day[0] == 1) $this->report[$No]['shour'] = $this->report[$No]['shour'] + 8;
                                if ($day[0] == 3) $this->report[$No]['shour'] = $this->report[$No]['shour'] + 8;
                            }
                        }
                        if ($table['shift'] == 'Б') {
                            
                        }
                        if ($table['shift'] == 'З') {
                            
                        }
                        if ($table['shift'] == 'Х') {
                            
                        }
                    }
                }
                
                if ($nofind) {
                    
                    // на указанную дату для сотрудника не найдена запись в табеле
                    // если это рабочий или предпраздничный день, то считаем рабочие часы по произв.календарю
                    if ($day[0] == 0) $this->report[$No]['phour'] = $this->report[$No]['phour'] + 8;
                    if ($day[0] == 2) $this->report[$No]['phour'] = $this->report[$No]['phour'] + 7;
                }
            }
            
            // выполняем подсчет задач
            foreach ($this->task as $key => $task) {

                $sumcost = 0;
                $empcount = count($task['employe']);
                //$hrcount = count($task['reletm']);
                //$hrcount = sumArray($task['whour'], 'hcount');
                foreach ($task['employe'] as $emp) {
                    if ($employe['eid'] == $emp['eid']) {
                        //$this->report[$No]['hour'] = $this->report[$No]['hour'] + $hrcount;// = sumArray($task['whour'], 'hcount');//count($task['reletm']);
                        $this->report[$No]['hour'] = $this->report[$No]['hour'] + sumArray($task['whour'], 'hcount');
                        //$this->report[$No]['tids'] = $this->report[$No]['tids'].'['.$this->task[$key]['tid'].'] ';//.'['.$this->task[$key]['tid'].']=';
                        foreach ($this->task[$key]['worklist'] as $work) {
                            $sumcost = $sumcost + $work['nrepeat'] * $work['cost'];
                            //$this->report[$No]['tids'] = $this->report[$No]['tids'].' c'.$sumcost.'-e'.$empcount.' ';
                        }
                        if ($empcount > 0) $this->report[$No]['cost'] = $this->report[$No]['cost'] + ($sumcost / $empcount); //(floor($sumcost / $empcount / 0.01)) * 0.01;
                    }
                }
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
                    
                    foreach ($task[$i]['worklist'] as $work) {
                        
                       
                        $cost = $cost + ($work['nrepeat'] * $work['cost']);
                    }
                    
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
                 $this->report = Task::find()->asArray()->where(['>=', 'dttask', $start])->andWhere(['<=', 'dttask', $finish])->orderBy('dttask')->all();
            else $this->report = Task::find()->asArray()->where(['=', 'did', $did])->andWhere(['>=', 'dttask', $start])->andWhere(['<=', 'dttask', $finish])->orderBy('dttask')->all();
            
            //$this->employe = Employe::find()->asArray()->where(['!=', 'status', 0])->andWhere(['=', 'did', Yii::$app->user->identity->did])->orderBy('fio_short')->all();
        }
        else {
            
            // доступ к данным ограничен
            $this->report = Task::find()->asArray()->where(['=', 'did', Yii::$app->user->identity->did])->andWhere(['>=', 'dttask', $start])->andWhere(['<=', 'dttask', $finish])->orderBy('dttask')->all();
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
    
}
