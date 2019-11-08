<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

use app\controllers\ExtController;
use app\models\Task;
use Yii;
use app\models\Log;

/**
 * Description of PatternController
 *
 * @author vitt
 */
class PatternController extends ExtController {
    
    public function actionIndex() {
        
        // шаблоны берутся из таблицы задач, если доступ полный, то выводятся все шаблоны, иначе только шаблоны своего отдела
        // то есть фактически эту проверку нужно перенести в модель Task
        Log::recLog('view', 'pattern');
        if (Yii::$app->user->identity->role[0] == 'w')
            $pattern = Task::find()->asArray()->where(['status' => 5])->orderBy('dttask, title')->all();
        else
            $pattern = Task::find()->asArray()->where(['status' => 5])->andWhere(['did' => Yii::$app->user->identity->did])->orderBy('dttask, title')->all();
        return $this->render('index', ['pattern' => $pattern]);
    }
}
