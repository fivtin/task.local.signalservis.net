<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

use app\controllers\ExtController;
use app\models\Salary;
use app\controllers\AjaxController;

/**
 * Description of SalaryController
 *
 * @author vitt
 */
class SalaryController extends ExtController {
    
    
    public function actionIndex() {
        
//        $salary = new Salary;
//        $salary->addReport();
        
        return $this->render('index');//, ['salary' => $salary]);
    }
    
}
