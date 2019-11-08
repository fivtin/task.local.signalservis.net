<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

/**
 * Description of StvController
 *
 * @author vitt
 */
class StvController extends ExtController {
    //put your code here
    
    public function actions() {
        
        $this->layout = 'stv';
    }
    
    public function actionIndex () {
        
        return $this->render('index');
    }
    
    public function actionTicker () {
        
        return $this->render('ticker');
    }
    
    public function actionGratters () {
        
        return $this->render('gratters');
    }

}
