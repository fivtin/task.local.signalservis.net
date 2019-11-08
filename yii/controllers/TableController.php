<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;
use Yii;
use app\controllers\ExtController;
use app\models\Log;
use app\models\Employe;
use app\models\Calendar;
use app\models\Timesheet;
use app\models\Table;
use app\models\TsComment;

/**
 * Description of TableController
 *
 * @author vitt
 */
class TableController extends ExtController {
    
    // ограничиваем доступ через контроллер
    public function beforeAction($action) {
        
        if (!parent::beforeAction($action)) {
            return false;
        }
        if (Yii::$app->user->identity->role['5'] == 'x') return $this->goHome();
        else { 
            
            //Log::recLog('table', $action->id);
            return true;
        }
        
    }
    
    public function actionSave() {
        
        // выполняем проверку и сохранение данных
        // создаем табель
        // переходим на главную
        
        //$calendar = new Calendar();
        
        $table = new Table();
        $table->attributes = Yii::$app->request->post();
        if ($table->validate() === false) die;
        Log::recLog('table', 'save='.$table->did.' '.$table->year.'-'.$table->month);
        $table->save();
        $table->refresh();
        
        return $this->render('index', ['table' => $table]);
    }
    
    public function actionAjax() {
        if (Yii::$app->request->isGet) {
            $day = Yii::$app->request->get('day');
            $eid = key($day);
            $cmdate = key(array_shift($day));
            $comment = TsComment::findOne(['eid' => $eid, 'cmdate' => $cmdate]);
            if (!$comment) {
                $comment = new TsComment();
                $comment->uid = Yii::$app->user->id;
                $comment->eid = $eid;
                $comment->cmdate = $cmdate;
            }
            $comment->comment = Yii::$app->request->get('comment');
            if ($comment->comment == '') {
                if ($comment->delete()) echo 'Комментарий удален.';
            }
            else 
               if ($comment->save()) echo 'OK Комментарий сохранён.';
               else echo 'Ошибка БД: Комментарий не сохранён.';
            //var_dump($comment);
            // при удалении не логгируется uid
        }
    }
    
    
    public function actionIndex() {
        
        $table = new Table();
        $table->attributes = Yii::$app->request->post();
        $table->attributes = Yii::$app->request->get();
        $table->validate();
        $table->refresh();
        Log::recLog('table', 'view='.$table->did.' '.$table->year.'-'.$table->month);
        
        return $this->render('index', ['table' => $table]);
        
        // ЧАСТЬ НИЖЕ НЕ РАБОТАЕТ
        
        // если есть данные на входе, то анализируем их
        if (Yii::$app->request->isPost && (Yii::$app->request->post('action')) && (Yii::$app->request->post('action') == 'view')) {
            $calendar = new Calendar(Yii::$app->request->post('month'), Yii::$app->request->post('year'));
        }
        else $calendar = new Calendar();
        
        //$tsheet = Timesheet::find()->asArray()->where(['>=', 'tsdate', $calendar->start])->andWhere(['<=', 'tsdate', $calendar->finish])->all();
        $tsheet = Employe::find()->asArray()->joinWith('timesheet')->where(['>=', 'timesheet.tsdate', $calendar->start])->andWhere(['<=', 'timesheet.tsdate', $calendar->finish])->all();

        $employe = Employe::find()->asArray()->where(['<>', 'status', 0])->andWhere(['=', 'did', Yii::$app->request->post('did', Yii::$app->user->identity->did)])->orderBy('dgroup, fio_short')->all();
//Yii::$app->user->identity->did
        if (Yii::$app->request->isPost && (Yii::$app->request->post('action')) && (Yii::$app->request->post('action') == 'save')) {
            
            $post = Yii::$app->request->post();
            $days = Yii::$app->request->post('day');

//            foreach ($days as $key => $item) {
//
//                // удаляем записи для eid за отчетный период
//                Timesheet::deleteAll(['AND', ['=', 'eid', $key], ['>=', 'tsdate', Yii::$app->request->post('first', date("Y-m-01"))], ['<=', 'tsdate', Yii::$app->request->post('last', date("Y-m-t"))]]);
//
//                foreach ($item as $key2 => $value) {
//                    if ($value != "") { 
//                        $new = new Timesheet();
//                        $new->eid = $key;
//                        $new->tsdate = $key2;
//                        $new->shift = $value;
//                        $new->save();
//                    }
//                }
//            }
        } else $post = '';
        
        return $this->render('index', ['employe' => $employe, 'calendar' => $calendar, 'tsheet' => $tsheet, 'post' => $post, 'did' => Yii::$app->request->post('did', Yii::$app->user->identity->did)]);
    }
    
}
