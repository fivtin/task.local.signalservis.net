<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;
use yii\base\Widget;
use app\models\Relemp;
use app\models\Employe;
use app\models\Tunit;
use app\models\Timesheet;
use Yii;

/**
 * Description of EmployeWidget
 *
 * @author vitt
 */

class EmployeWidget extends Widget {
    
    public $tunit;         // задача, данные о которой надо получить
    public $emplist;       // список всех сотрудников, которых надо отображать

    public function init () {
        
        parent::init();
        
        $this->emplist = Employe::getEmployeOnTask($this->tunit);
    }

    public function run() {
        
        parent::run();
        
        if ($this->tunit->status == 0) 
            return $this->render('employe/empAct', ['model' => $this]);
        else return $this->render('employe/empDone', ['model' => $this]);
    }
}
