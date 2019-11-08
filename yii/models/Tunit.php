<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\base\Model;
//use app\models\Employe;
use app\models\Relemp;
//use app\models\Whour;
use app\models\Reletm;
use app\models\Work;
use app\models\Task;
use Yii;
use app\models\Log;

/**
 * Description of Item
 *
 * @author vitt
 */

// ЭЛЕМЕНТ "ЗАДАЧА" содержащий все данные задачи: список сотрудников, список интервалов, список работ
// передается в виджет отображающий задачу

class Tunit extends Model {
    
    public $tid;             // ID задачи
    public $dttask;          // дата выполнения задачи "2017-09-22"
    public $title = '';      // описание задачи
    public $descr = '';      // краткое описание состава задачи
    public $status = 0;      // статус задачи: 0 - не выполнена, 1 - выполнена, 2 - ???удалена(с данными), 9 - ???шаблон
    public $uid;             // id создавшего задачу (для ограничения редактирования)
    public $error = false;   // если в процессе заполнения полей при создании объекта
                             // возникла ошибка, устанавливаем в true
    public $copy = 0;        // если создаем из копии, то здесь хранится tid копируемой задачи
    
    
    public $hid;//   = array();  // массив времени выполнения (не исп. при отправке данных виду, а для загрузки данных из формы)
    public $eid;//   = array();  // массив сотрудников        (не исп. при отправке данных виду, а для загрузки данных из формы)
    //public $wid;//   = array();  // массив работ задачи       (не исп. при отправке данных виду, а для загрузки данных из формы)
    public $twid;//   = array(); // массив типработы[работ] задачи       (не исп. при отправке данных виду, а для загрузки данных из формы)
    public $info;//   = array(); // массив инфо[работ] задачи       (не исп. при отправке данных виду, а для загрузки данных из формы)
    public $wcost;//  = array(); // массив стоимости[работ] задачи    (не исп. при отправке данных виду, а для загрузки данных из формы)
    public $nrep;//   = array(); // массив количество выполнений (не исп. при отправке данных виду, а для загрузки данных из формы)
    // последние 6 пунктов используются при получении и обработке данных из формы
    // ???здесь они как справочные значения, в итоговой модели не нужны???
    // а необходимы специальные методы для сохранения этих данных
    
    public $total = 0;   // ??? хранит количество выбранных сотрудников
    public $cost = 0;    // ??? хранит общую стоимость работ
    public $one = 0;     // ??? стоимость работ на одного сотрудника (окр. 0,25)
    // пока не ясно, нужны ли эти поля...

//    public function load($data, $formName = null) {
//        parent::load($data, $formName);
//        
//        
//    }
    
    const Actions = [0 => 'save', 1 => 'remove', 2 => 'delete', 3 => 'copy', 4 => 'restore'];
    const StatusText = [0 => '', 1 => ': выполнена', 2 => ': просрочена', 3 => ': запланирована', 4 => ': не все данные', 5 => ''];

    
    // проверка действия на корректность (значение присутствует в массиве) 
    public function validateAction ($action) {
    
        return in_array($action, self::Actions) ? true : false;        
    }
    
    private function clearRelations ($tid) {
        
        Relemp::deleteAll(['tid' => $tid]);
        Reletm::deleteAll(['tid' => $tid]);
          Work::deleteAll(['tid' => $tid]);
    }
    
    public function processRequest ($request) {
        
        
        
    }
    
    public function getCopy($tid) {
        
        $this->tid = 0;
        $this->copy = $tid;
        $this->uid = Yii::$app->user->identity->uid;
        $this->title = htmlspecialchars($this->title);
        $this->descr = htmlspecialchars($this->descr);
        $this->dttask = Year::getNextWDay();//date("Y-m-d", time()+ 86400);
        $this->status = 0;
        Log::recLog('copy', 'tid='.$this->copy);
        // !!! дополнительно копировать список работ из work, но с базовыми данными из typework
        // $works = Work::find->with(typework)-all
        // $works copy to Work
        return $this;
    }
    
    public function done() {
        
        // !!! выполнить проверку:
        // !!! нельзя закрыть задачу без исполнителей/времени/работ
        if ((count($this->hid) > 0) && (count($this->eid) > 0) && (count($this->twid) > 0)) $this->status = 1;
        if ($this->save() && ($this->status == 1)) {
            Log::recLog('done', 'tid='.$this->tid);
            return true;
        }
        else {
            Log::recLog('change', 'tid='.$this->tid);
            return false;
        }
    }
    
    public function restore() {
        
        // устанавливаем статус = 0
        // сохраняем
        
        $task = Task::findOne($this->tid);
        $task->status = 0;

        if ($task->save()) {
            Log::recLog('restore', 'tid='.$this->tid);
            return true;
        }
        else {
            Log::recLog('restore_error', 'tid='.$this->tid);
            return false;
        }
    }
    
    
    public function delete() {
        
        // !!! нельзя удалить выполненную задачу
        //     а также проверка в правах на удаление
        if (($this->status != 1) && true) {
            $this->clearRelations($this->tid);
            $task = Task::findOne($this->tid);
            $task->delete();
            Log::recLog('delete', 'tid='.$this->tid);
            return true;
        }
        else return false;
    }
    
    public function save() {
        
        // !!! реализовать проверку прав на выполнение операции
        if ($this->tid == 0) {
            
            $task = new Task();
            $task->uid = Yii::$app->user->identity->uid;
            $task->did = Yii::$app->user->identity->did;
            $create = true;
            
        }
        else {
            
            $task = Task::findOne($this->tid);
            
            // очищаем связи с исполнителями/временем
            $this->clearRelations($this->tid);
            
            // при редактировании сохраняем uid создавшего запись
            //$task->uid = $this->uid;
            //
            // при редактировании записываем uid редактировавшего запись, далее триггером в MySQL он перезаписывается uid`ом автора записи
            $task->uid = $task->uid = Yii::$app->user->identity->uid;
            
            //Relemp::deleteAll(['tid' => $this->tid]);
            //Reletm::deleteAll(['tid' => $this->tid]);
            //  Work::deleteAll(['tid' => $this->tid]);
            $create = false;
        }
        if ((Yii::$app->user->id != 1) && (Yii::$app->user->id != 13) && (($create && ($this->dttask < date('Y-m-d'))) ||
            (!$create && ($this->dttask != $task->dttask) && ($this->dttask < date('Y-m-d')))))
             $chkDT = false;
        else $chkDT = true;
        
        $task->dttask = $this->dttask;
        $task->title = htmlspecialchars($this->title);
        $task->descr = htmlspecialchars($this->descr);
        $task->status = $this->status;
        //$task->did = Yii::$app->user->identity->did;
        
        if ($chkDT) {
            // создаем/сохраняем задачу
            $task->save();
            $this->tid = $task->tid;
        
            if ($create) Log::recLog('create', 'tid='.$this->tid);
            else Log::recLog('change', 'tid='.$this->tid);


            if ($this->hid != array()) {

                // вносим новые связи времени задачи
                $reletm = new Reletm();
                foreach ($this->hid as $hid) {

                    $reletm->isNewRecord = true;
                    $reletm->tid = $this->tid;
                    $reletm->hid = $hid;
                    $reletm->save();
                }
            }

            if ($this->eid != array()) {

                // вносим новые связи исполнителей
                $relemp = new Relemp();
                foreach ($this->eid as $eid) {

                    $relemp->isNewRecord = true;
                    $relemp->tid = $this->tid;
                    $relemp->eid = $eid;
                    $relemp->save();
                }
            }

            if ($this->twid != array()) {

                // вносим новые связи работ
                $work = new Work();

                foreach ($this->twid as $twid =>$key) {

                    $work->isNewRecord = true;
                    $work->tid = $this->tid;
                    $work->wid = null;
                    $work->twid = $this->twid[$key];
                    $work->info = $this->info[$key];
                    $work->cost = $this->wcost[$key];
                    $work->nrepeat = $this->nrep[$key];

                    $work->save();
                }
            }
            return true;
        }
        else return false;
    }
    
    public function rules () {
        return [
            [['tid', 'title', 'dttask'], 'required'],
            [['title', 'descr'], 'trim'],
            [['dttask'], 'date', 'format' => 'php:Y-m-d'],
            [['tid', 'status'], 'integer'],
            [['title'], 'string','min' => 8, 'max' => 128],
            [['uid', 'hid', 'eid', 'wid','twid', 'info', 'wcost', 'nrep'], 'safe'],
            //[['tid'], 'default', 'value' => 0],
        ];
    }
    
    public function __construct ($tid = 0, $date = 0, $copy = 0) {
        
        parent::__construct();
        
        if ($tid === 'new') {
            // новая задача
            //$task = Task::getOnTidWithRel(-Yii::$app->user->identity->did);
            $this->tid = 0;
            if ($copy != 0) {
                
                $task = Task::getPatternOnTid($copy);
                //var_dump($task); die;
                if ($task) {
                    $this->title = $task['title'];
                    $this->descr = $task['descr'];
                }
                $this->copy = $copy;
                Log::recLog('copy', $copy);
            }
            if ($date == 0)
                 $this->dttask = Year::getNextWDay(); // функция поиска ближайшего рабочего дня
            else $this->dttask = $date;
            $this->uid = Yii::$app->user->identity->uid;
        }
        else {            
            //$task = Task::getOnTidWithRel($tid);
            $task = Task::getOnTid($tid);
            if (!$task) $this->error = true;
            else {
                $this->tid = $tid;
                $this->dttask = $task['dttask'];
                $this->title = $task['title'];
                $this->descr = $task['descr'];
                $this->status = $task['status'];
                $this->uid = $task['uid'];
            }
        }
    }  
    
    public function getStatus ($class = false) {
        //echo '<pre>'.var_dump($this).'</pre>'; die;
        if ($this->tid == 0) return "primary";//getStatus (0, ($this->dttask + 86400), true, $class);
        if ((Relemp::find()->where(['tid' => $this->tid])->count() == 0) || 
            (Reletm::find()->where(['tid' => $this->tid])->count() == 0) ||
            (Work::find()->where(['tid' => $this->tid])->count() == 0))
            $rel = false;
        else $rel = true;
        return getStatus($this->status, $this->dttask, $rel, $class);        
    }
    
    public function getStatusText() {
        
        if ($this->tid == 0) return self::StatusText[0];
        else 
        return self::StatusText[self::getStatus()];
    }
    
    // старая функция - не нужна
    private function loadData () {
        
        $this->total = 0;
        $this->cost = 0;
        for ($x = 0; $x < count($this->employe); $x++) {

            $f = false;
            for ($y = 0; $y < count($this->relemp); $y++) {
                if ($this->employe[$x]['eid'] == $this->relemp[$y]['eid']) { $f = true; break; }
            }
            if ($f) {
                $this->employe[$x]['On'] = 1;
                $this->total++;
            }
            else $this->employe[$x]['On'] = 0;
        }
        for ($x = 0; $x < count($this->whour); $x++) {
            
            $f = false;
            for ($y = 0; $y < count($this->reletm); $y++) {
                if ($this->whour[$x]['hid'] == $this->reletm[$y]['hid']) { $f = true; break; }
            }
            if ($f) $this->whour[$x]['On'] = 1; else $this->whour[$x]['On'] = 0;
        }
        
        foreach ($this->work as $value) {
            $this->cost = $this->cost + $value['cost'];
        }
        if ($this->total != 0) $this->one = ((floor($this->cost / $this->total / 0.25)) * 0.25);
    }

    // старая функция - не нужна
    public function x__construct ($id = 0) {
        
        parent::__construct();
        
        // !!! для новой записи (id = 0) по идее не нужно загружать списки работ, выбранных сотрудников и интервалы
        // ПРОДУМАТЬ ПЕРЕДАЧУ ШАБЛОНА В КОНСТРУКТОР НОВОЙ ЗАДАЧИ
        
        $this->tid = $id;
        $this->employe = Employe::find()->select('eid, fio_short, status')->asArray()->where(['did' => Yii::$app->user->identity->did])->orderBy('fio')->all(); // !!! параметр 2 заменить на номер отдела из профиля
        $this->whour   = Whour::find()->select('hid, htext, status')->asArray()->where(['did' => Yii::$app->user->identity->did])->orderBy('hour')->all();
        
        if ($id != 0) {
            $this->work    = Work::find()->with('typework')->asArray()->where(['tid' => $this->tid])->all();
            $this->relemp  = Relemp::find()->select('eid')->asArray()->where(['tid' => $this->tid])->all();
            $this->reletm  = Reletm::find()->select('hid')->asArray()->where(['tid' => $this->tid])->all();
        }
        
        $this->loadData();
    }
}
