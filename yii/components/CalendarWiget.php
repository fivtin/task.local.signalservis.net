<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;
use yii\base\Widget;
use app\models\Tcalendar;

/**
 * Description of CalendarWiget
 *
 * @author vitt
 */
class CalendarWiget extends Widget {
    
    // календарь строится автоматически исходя из входных параметров
    // date - дата для которой строится календарь
    // select - показывает, нужно ли отметить дату
    // params - список дополнительных параметров в виде массива
    // напр. ['mode' => 'withTasks']
    
    public $date;
    public $select = false;
    public $params = array();
    public $mode;
    public $calendar;

    public function init() {
        
        parent::init();
        if ($this->date === NULL) $this->date = date("Y-m-d");
        $this->calendar = new Tcalendar($this->date);
        
    }
    
    public function run() {
        
        parent::run();
        if ($this->mode == 'withTasks')
        $this->params[$this->mode] = $this->calendar->getTaskStatus();
        
        return $this->render('calendar', ['model' => $this]);
//'date' => $this->date, 'select' => $this->select, 'days' => $this->days, 'params' => $this->params]);
    }
}
