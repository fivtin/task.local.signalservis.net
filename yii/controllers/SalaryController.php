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
use Yii;
use app\models\Log;

/**
 * Description of SalaryController
 *
 * @author vitt
 */
class SalaryController extends ExtController {
    
    
    public function actionIndex() {
        
//        $salary = new Salary;
//        $salary->addReport();

        if ((Yii::$app->user->id == 1) || (Yii::$app->user->id == 8)) {
        
            Log::recLog('salary', 'index');
            return $this->render('index');//, ['salary' => $salary]);
        }
        else {
        
            Log::recLog('salary', 'error');
            Yii::$app->response->redirect('/');
        }
    }
}
