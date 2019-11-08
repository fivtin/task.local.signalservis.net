<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;
use Yii;
use app\controllers\ExtController;

/**
 * Description of ListController
 *
 * @author vitt
 */

// контроллер ограничивает доступ пользователей к справочникам
// role[3] = 'x'
// x - доступ запрещен
// r - просмотр
// w - изменение (!!! пока не реализовано)


class ListController extends ExtController {
    
    public function beforeAction($action) {
        
        if (!parent::beforeAction($action)) {
            return false;
        }
        if (Yii::$app->user->identity->role['3'] == 'x') return $this->goHome ();
        else return true;
    }
}
