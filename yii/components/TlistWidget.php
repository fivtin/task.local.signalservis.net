<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;
use yii\base\Widget;
use app\models\Task;

/**
 * Description of TasklistWidget
 *
 * @author vitt
 */

class TlistWidget extends Widget {
 
    public $date;
    public $select = false;
    public $search = '';
    public $tlist;
    
    public function init() {
        
        parent::init();
        if ($this->date === NULL) $this->date = date("Y-m-d");
    }
    
    public function run() {
        
        parent::run();
        if ($this->search != '') $this->tlist = Task::getSearchWithRel($this->search);
        else 
            if ($this->select) 
                 $this->tlist = Task::getOnDateWithRel($this->date);
            else $this->tlist = Task::getActiveWithRel();
        return $this->render('tasklist', ['model' => $this]);
    }
    
}
