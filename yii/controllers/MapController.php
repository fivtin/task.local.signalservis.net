<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;
use app\controllers\ExtController;

/**
 * Description of MapController
 *
 * @author vitt
 */
class MapController extends ExtController {
    
    public function actionIndex () {
        
        $this->layout = 'map';
        return $this->render('index');
    }
}
