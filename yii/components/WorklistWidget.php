<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;
use yii\bootstrap\Widget;
use app\models\Work;
use app\models\Tunit;
use app\models\Typework;

/**
 * Description of WorklistWidget
 *
 * @author vitt
 */
class WorklistWidget extends Widget {
    
    public $tunit;
    public $worklist = array();
    public $typework = array();


    public function init () {
        
        parent::init();
        
        if (\Yii::$app->user->identity->role[0] == 'w')
            
            $this->typework = Typework::find()->
                                    asArray()->
                                    where(['status' => 1])->
                                    orWhere (['status' => 9])->
                                    orderBy('rating, title')->
                                    all();
        else
            $this->typework = Typework::find()->
                                    asArray()->
                                    where(['status' => 1])->
                                    orWhere (['status' => 9])->
                                    andWhere(['did' => \Yii::$app->user->identity->did])->
                                    orderBy('rating, title')->
                                    all();
        
        if ($this->tunit->tid != 0) $this->worklist = Work::getWorkOnTid($this->tunit->tid);
            else $this->worklist = Work::getWorkOnTid($this->tunit->copy);//$this->worklist = array('work' => array(), 'cost' => array(), 'info' => array());
    }

    public function run() {
        
        parent::run();
        
        return $this->render('worklist', ['model' => $this]);
    }

}
