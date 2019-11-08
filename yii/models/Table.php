<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\base\Model;
use app\models\Calendar;
use Yii;
use app\models\Timesheet;
use app\models\Employe;
use app\models\TsComment;

/**
 * Description of Table
 *
 * @author vitt
 *
 * This is the model class "table".
 *
 * @property integer $month
 * @property integer $year
 * @property integer $did
 * @property array $employe
 * @property array $calendar
 * @property array $day
 * @property array $tsheet
 */

class Table extends Model {
    
    public $month;           // месяц для табеля
    public $year;            // год для табеля
    public $did;             // отдел для табеля
    
    public $employe = array();
    public $calendar = array();
    public $day = array();
    public $tsheet = array();
    public $comment = array();
    
    private $eids;


    public function __construct() {
        
        parent::__construct();
        
        
    }
    
    private function getArrayEid() {
        $this->eids = array();
        foreach ($this->employe as $item) {
            $this->eids[] = $item['eid'];
        }
        sort($this->eids);
    }
    
    public function rules() {
        
        return [
            [['did'], 'integer'],
            [['month', 'year'], 'string'],
            [['day'], 'safe'],
            ['did', 'default', 'value' => Yii::$app->user->identity->did],
            ['month', 'default', 'value' => date("m")],
            ['year', 'default', 'value' => date("Y")],
        ];
    }
    
    public function refresh() {
        
        $this->calendar = new Calendar($this->month, $this->year);
        if ($this->did == 0)
            $this->employe = Employe::find()->asArray()->where(['<>', 'status', 0])->orderBy('fio_short')->all();
        else $this->employe = Employe::find()->asArray()->where(['<>', 'status', 0])->andWhere(['=', 'did', $this->did])->orderBy('dgroup, fio_short')->all();
        $this->getArrayEid();
        $this->tsheet = Employe::find()->asArray()->joinWith(['timesheet' => function ($query) {
        $query->onCondition(['>=', 'timesheet.tsdate', $this->calendar->start])->andOnCondition(['<=', 'timesheet.tsdate', $this->calendar->finish]);
    },])->joinWith([
    'ts_comment' => function ($query) {
        $query->onCondition(['>=', 'ts_comment.cmdate', $this->calendar->first])->andOnCondition(['<=', 'ts_comment.cmdate', $this->calendar->last]);
    },
])->
            //where(['>=', 'timesheet.tsdate', $this->calendar->start])->andWhere(['<=', 'timesheet.tsdate', $this->calendar->finish])->
            where(['IN', 'employe.eid', $this->eids])->
            orderBy('dgroup, fio_short')->
            all();
        //$this->comment = Employe::find()->select('employe.eid, ts_comment.eid, ts_comment.cmdate, ts_comment.comment')->asArray()->joinWith('ts_comment')->where(['>=', 'cmdate', $this->calendar->start])->andWhere(['<=', 'cmdate', $this->calendar->finish])->all();
        //$this->comment = TsComment::find()->asArray()->
        
    }
    
    public function save() {
        
        $start = $this->year.'-'.$this->month.'-01';
        $lt = strtotime($this->year.'-'.$this->month.'-01');
        $finish = date("Y-m-t", $lt);
            
            foreach ($this->day as $key => $item) {

                // удаляем записи для eid за отчетный период
                Timesheet::deleteAll(['AND', ['=', 'eid', $key], ['>=', 'tsdate', $start], ['<=', 'tsdate', $finish]]);
                
                $new = new Timesheet();
                foreach ($item as $key2 => $value) {
                    if ($value != "") { 
                        $new->isNewRecord = true;
                        $new->eid = $key;
                        $new->tsdate = $key2;
                        $new->shift = $value;
                        $new->tsid = null;
                        $new->save();
                    }
                }
            }        
    }
    
}
