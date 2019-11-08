<?php

namespace app\models;

use Yii;
use app\models\Task;
use app\models\Departament;
use app\models\Timesheet;
use app\models\TsComment;

/**
 * This is the model class for table "employe".
 *
 * @property integer $eid
 * @property string $fio
 * @property string $fio_short
 * @property integer $did
 * @property integer $status
 */
class Employe extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'employe';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fio', 'fio_short', 'did', 'status', 'tab_task'], 'required'],
            [['did', 'status', 'tab_task'], 'integer'],
            [['dgroup'], 'string', 'max' => 3],
            [['dgroup', 'post', 'note'], 'default', 'value' => ''],
            [['fio', 'fio_short', 'post'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'eid' => 'ID',
            'fio' => 'ФИО полностью',
            'fio_short' => 'Фамилия И.О.',
            'did' => 'Отдел',
            'post' => 'Должность',
            'dgroup' => 'Позиция в табеле',
            'note' => 'Примечания',
            'tab_task' => 'Задача/Табель',
            'status' => 'Статус',
        ];
    }
    
    private function getArrayEid($array) {
        $result = array();
        foreach ($array as $item) {
            $result[] = $item['eid'];
        }
        return $result;
    }


    public function getEmplist ($eids) {
        
        if (Yii::$app->user->identity->role[0] == 'w1')
        
            return self::find()->
                          asArray()->
                          select('eid, fio_short, did, status')->
                          where(['>', 'status', 0])->
                          orWhere(['IN', 'eid', $eids])->
                          orderBy(['fio_short' => SORT_ASC])->
                          all();
        else
            
            return self::find()->
                          asArray()->
                          select('eid, fio_short, did, status')->
                          where(['>', 'status', 0])->
                          andWhere(['did' => Yii::$app->user->identity->did])->
                          orWhere(['IN', 'eid', $eids])->
                          orderBy(['fio_short' => SORT_ASC])->
                          all();

    }
    
    // EmployeWidget - получаем список сотрудников с табелем для вывода в задаче
    public function getEmployeOnTask($task) {
        
        $emplist = array();
        // возвращает массив с полями [eid],[fio_shot],[info],[class],[status],[hide],[select],[shift]
        
        
        // если задача выполнена, то нужно найти всех исполнителей и их табель за день задачи
        // и не важно к какому отделу относятся сотрудники
        if ($task->status == 1) {
            
            // выполнена (нужен список сотрудников этой задачи с их табелем на дату задачи)
            $relemp = Relemp::getEidOnTid($task->tid); // список сотрудников в задаче
            $employe = self::find()->select('eid, did, fio_short, status')->asArray()->where(['IN', 'eid', $relemp])->orderBy('fio_short')->all();
            $table = Timesheet::find()->asArray()->where(['tsdate' => $task->dttask])->andWhere(['IN', 'eid', $relemp])->all();
        }
        else {
            
            // не выполнена (если задача новая, то нужен список сотрудников отдела с табелем на дату задачи,
            //               иначе список сотрудников отдела дополненный из списка сотрудников задачи с табелями)
            if ($task->tid == 0) {
                
                // новая задача (нужен только список сотрудников отдела)
                $relemp = array();
                $employe = self::find()->select('eid, did, fio_short, status')->asArray()->where(['>', 'status', 0])->andWhere(['did' => Yii::$app->user->identity->did])->orderBy('fio_short')->all();
                $eids = self::getArrayEid($employe);
                $table = Timesheet::find()->asArray()->where(['tsdate' => $task->dttask])->andWhere(['IN', 'eid', $eids])->all();
            }
            else {
                // существующая задача
                $relemp = Relemp::getEidOnTid($task->tid); // список сотрудников в задаче
                if (count($relemp) > 0)
                    $employe = self::find()->
                               select('eid, did, fio_short, status')->
                               asArray()->
                               where(['>', 'status', 0])->
                               andWhere(['did' => Yii::$app->user->identity->did])->
                               orWhere(['IN', 'eid', $relemp])->
                               orderBy('fio_short')->
                               all();
                else $employe = self::find()->
                                select('eid, did, fio_short, status')->
                                asArray()->
                                where(['>', 'status', 0])->
                                andWhere(['did' => Yii::$app->user->identity->did])->
                                orderBy('fio_short')->
                                all();
                $eids = self::getArrayEid($employe);
                $table = Timesheet::find()->asArray()->where(['tsdate' => $task->dttask])->andWhere(['IN', 'eid', $eids])->all();
            }
        }
        
        // заполняем поля списка сотрудников согласно табеля
        $i = 0;
        foreach ($employe as $itemE) {

            $emplist[$i]['eid'] = $itemE['eid'];
            $emplist[$i]['did'] = $itemE['did'];
            $emplist[$i]['fio_short'] = $itemE['fio_short'];
            $emplist[$i]['info'] = '';
            $emplist[$i]['class'] = '';
            $emplist[$i]['status'] = $itemE['status'];
            if ($itemE['status'] > 1)
                $emplist[$i]['hide'] = true;
            else $emplist[$i]['hide'] = false;
            $emplist[$i]['select'] = false;
            $emplist[$i]['shift'] = '';
            foreach ($table as $itemT) {
                if ($itemE['eid'] == $itemT['eid']) {
                    $emplist[$i]['shift'] = $itemT['shift'];
                    if ($itemT['shift'] == "О") { $emplist[$i]['info'] = '(отпуск)'; $emplist[$i]['class'] = 'leave'; $emplist[$i]['hide'] = true; }
                    if ($itemT['shift'] == "Ч") { $emplist[$i]['info'] = '(отпуск ЧАЭС)'; $emplist[$i]['class'] = 'leave'; $emplist[$i]['hide'] = true; }
                    if ($itemT['shift'] == "Б") { $emplist[$i]['info'] = '(больничный)'; $emplist[$i]['class'] = 'leave'; $emplist[$i]['hide'] = true; }
                    if ($itemT['shift'] == "З") { $emplist[$i]['info'] = '(заявление)'; $emplist[$i]['class'] = 'leave'; $emplist[$i]['hide'] = true; }
                    if ($itemT['shift'] == "С") { $emplist[$i]['info'] = 'Cмена'; $emplist[$i]['class'] = 'shift'; }
                    if ($itemT['shift'] == "Н") { $emplist[$i]['info'] = 'Ночь'; $emplist[$i]['class'] = 'shift-night'; }
                    if ($itemT['shift'] == "Д") { $emplist[$i]['info'] = 'День'; $emplist[$i]['class'] = 'shift-day'; }
                    
                    
                }
            }
            foreach ($relemp as $itemR) {
                if ($itemR == $itemE['eid'])
                    $emplist[$i]['select'] = true;
            }
            $i++;
        }
        return $emplist;
    }
    
    public function getTask() {
        
        return $this->hasMany(Task::className(), ['tid' => 'tid'])->viaTable('relemp', ['eid' => 'eid'])->where(['=', 'task.status', 1])->with('worklist','relemp');
    }
    
    public function getDepartament() {
        return $this->hasMany(Departament::className(), ['did' => 'did']);
    }
    
    public function getReport($start, $finish, $withDID = false) {
        
        return self::find()->
                     asArray()->
                     //where(['>=', 'dttask', $start])->
                     //andWhere(['<=', 'dttask', $finish])->
                     where(['!=', '{{employe}}.status', 0])->
                //andWhere(['>=', '{{task}}.dttask', $start])->
                //andWhere(['<=', '{{task}}.dttask', $finish])->
                     //andWhere(['=', 'did', 2])->//Yii::$app->user->identity->did])->
                     with('task')->
                     orderBy('fio_short')->
                     all();        
    }
    
    public function getReports($start, $finish, $withDID = false) {
        
        return self::find()->
                //select('employe.*')->
                     asArray()->
                     with('task')->
                     //leftJoin('task', '`task`.`tid`=`relemp`.`tid`')->
                //where(['>=', '{{task}}.dttask', '2017-10-01'])->
                     orderBy('fio_short')->
                     all();        
    }
    
    public function getEmployeOnRule($rule = 'x') {
        
        if ($rule == 'x') return array();
        if ($rule == 'f') return self::find()->asArray()->select('eid, fio_short')->where(['!=', 'status', 0])->orderBy('fio_short')->all();
        if ($rule == 'l') return self::find()->asArray()->select('eid, fio_short')->where(['!=', 'status', 0])->andWhere(['=', 'did', Yii::$app->user->identity->did])->orderBy('fio_short')->all();
        return array();
    }

    public function getTimesheet() {
        
        return $this->hasMany(Timesheet::className(), ['eid' => 'eid']);
    }
    
    public function getTs_comment() {
        
        return $this->hasMany(TsComment::className(), ['eid' => 'eid']);
    }
    
    
    // возращает список сотрудников с табелем на указанную дату
    // Report model
    public function getEmployeTable($date, $did = 0) {
        
        // проверяем, разрешен ли полный доступ
        // f - все данные + возможность выбора отдела
        if ((Yii::$app->user->identity->role[4] == 'f') || (Yii::$app->user->id == 2)) {
            
            // уточняем, получили ли данные о номере отдела
            if ($did == 0)
                 if (Yii::$app->user->id != 2) $employe = self::find()->asArray()->where(['!=', 'status', 0])->orderBy('fio_short')->all();
                 else $employe = self::find()->asArray()->where(['!=', 'status', 0])->andWhere(['!=', 'did', 2])->orderBy('fio_short')->all();
            else $employe = self::find()->asArray()->where(['!=', 'status', 0])->andWhere(['=', 'did', $did])->orderBy('fio_short')->all();
            
        }
        else {
            
            // доступ к данным ограничен
            $employe = self::find()->asArray()->where(['!=', 'status', 0])->andWhere(['=', 'did', Yii::$app->user->identity->did])->orderBy('fio_short')->all();
            
        }
        
        
//        if ($did == 0)
//            $employe = self::find()->select('eid, did, fio_short')->asArray()->where(['<>', 'status', 99])->orderBy('fio_short')->all();
//        else
//            $employe = self::find()->select('eid, did, fio_short')->asArray()->where(['did' => $did])->andWhere(['<>', 'status', 99])->orderBy('fio_short')->all();
        $eids = self::getArrayEid($employe);
        
        $tsheet = Timesheet::find()->asArray()->where(['tsdate' => $date])->andWhere(['IN', 'eid', $eids])->all();
        
        for ($i = 0; $i < count($employe); $i++) {
            $employe[$i]['shift'] = '';
            for ($t = 0; $t < count($tsheet); $t++) {
                if ($employe[$i]['eid'] == $tsheet[$t]['eid']) {
                    $employe[$i]['shift'] = $tsheet[$t]['shift'];
                    
                }
            }
        }
        return $employe;
    }
    
    
}
