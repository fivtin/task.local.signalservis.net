<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;
use yii\base\Widget;
use app\models\Reletm;
use app\models\Whour;
use app\models\Tunit;

/**
 * Description of WhourWidget
 *
 * @author vitt
 */

class WhourWidget extends Widget {
    
    public $tunit;
    public $whourlist;


    public function init () {
        
        parent::init();

        $this->whourlist = Whour::getWhourOnTask($this->tunit);
    }

    public function run() {
        
        parent::run();
        return $this->render('whour', ['model' => $this]);
    }
}