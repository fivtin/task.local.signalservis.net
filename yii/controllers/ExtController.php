<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;
use yii\web\Controller;
use Yii;

/**
 * Description of ExtController
 *
 * @author vitt
 */

// контроллер ограничивает доступ к сайту для неавторизованных пользователей
// по Yii::$app->user->isGuest

class ExtController extends Controller {
    
    public function beforeAction($action) {
        
        //var_dump($action->id); die;
        
        if ($action->id == 'new_year') return true;
        if ($action->id == 'hb') return true;
        if ($action->id == 'hb2019') return true;
        
        if (!parent::beforeAction($action)) {
            return false;
        }
        if ((Yii::$app->user->isGuest) && ($action->actionMethod != "actionLogin")) {
            Yii::$app->response->redirect('/task/login');
            return false;
        }
        else return true;
    }
    
}
