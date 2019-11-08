<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;
use app\controllers\ExtController;
//use app\models\Employe;
use Yii;
use app\models\Report;
use app\models\Task;
use app\models\Log;

/**
 * Description of ReportController
 *
 * @author vitt
 */

// класс контроллера ограничивает доступ пользователей к отчётам
// role[4] = 'x'
// x - доступ запрещен
// l - просмотр своего отдела (limit)
// f - просмотр всех сотрудников (full)


class ReportController extends ExtController {
    
    public function beforeAction($action) {
        
        if (!parent::beforeAction($action)) {
            return false;
        }
        if (Yii::$app->user->identity->role['4'] == 'x') return $this->goHome();
        else { 
            
            Log::recLog('report', $action->id);
            return true;
        }
        
    }
    
    public function actionIndex () {
    
        if (Yii::$app->request->isPost) {
            
            $start = Yii::$app->request->post('start');
            $finish = Yii::$app->request->post('finish');
            if ($start > $finish) {
                
                $tmp = $start;
                $start = $finish;
                $finish = $tmp;
            }
            
            $report = new Report();
            if (Yii::$app->request->post('did') == 0) 
                 $task = $report->getDateRep($start, $finish, 0);
            else $task = $report->getDateRep($start, $finish, Yii::$app->request->post('did'));
            
            // позже заменить в функции возвращаемое значение - только report (сейчас вся модель что бы видеть все получаемые данные)

            return $this->render('index', ['emplist' => $task, 'start' => $start, 'finish' => $finish, 'did' => Yii::$app->request->post('did')]);
        }
        else return $this->render('index');
    }
    
    public function actionPersonal() {
        
        $report = new Report();
        $report->LoadEmploye();
        $start = date("Y-m-01");
        $finish = date("Y-m-t");
        
        if (Yii::$app->request->isGet) {
            
            if (empty(Yii::$app->request->get('start', false)) || empty(Yii::$app->request->get('finish', false)))
                return $this->render('personal', ['emplist' => $report->employe, 'start' => $start, 'finish' => $finish]);
            
            if (Yii::$app->request->get('start')) $start = Yii::$app->request->get('start');
            if (Yii::$app->request->get('finish')) $finish = Yii::$app->request->get('finish');
            if ($start > $finish) {
                
                $tmp = $start;
                $start = $finish;
                $finish = $tmp;
            }
            
            if (Yii::$app->request->get('eid') == 0 ) return $this->render('personal', ['emplist' => $report->employe, 'start' => $start, 'finish' => $finish]);
            else {
                $task = $report->getPersonalRep($start, $finish, Yii::$app->request->get('eid'));
                return $this->render('personal', ['task' => $task, 'emplist' => $report->employe, 'start' => $start, 'finish' => $finish]);
            }
        }
        
        if (Yii::$app->request->isPost) {
            
            $start = Yii::$app->request->post('start');
            $finish = Yii::$app->request->post('finish');
            if ($start > $finish) {
                
                $tmp = $start;
                $start = $finish;
                $finish = $tmp;
            }
            
            if (Yii::$app->request->post('eid') == 0 ) return $this->render('personal', ['emplist' => $report->employe, 'start' => $start, 'finish' => $finish]);
            else {
                $task = $report->getPersonalRep($start, $finish, Yii::$app->request->post('eid'));
                return $this->render('personal', ['task' => $task, 'emplist' => $report->employe, 'start' => $start, 'finish' => $finish]);
            }
        }
        return $this->render('personal', ['emplist' => $report->employe, 'start' => $start, 'finish' => $finish]);
    }
    
    public function actionTask() {
    
        if (Yii::$app->request->isPost) {
            
            $start = Yii::$app->request->post('start');
            $finish = Yii::$app->request->post('finish');
            if ($start > $finish) {
                
                $tmp = $start;
                $start = $finish;
                $finish = $tmp;
            }
            
            $report = new Report();
            if (Yii::$app->request->post('did') == 0) 
                 $task = $report->getTaskRep($start, $finish, 0);
            else $task = $report->getTaskRep($start, $finish, Yii::$app->request->post('did'));
            
            // позже заменить в функции возвращаемое значение - только report (сейчас вся модель что бы видеть все получаемые данные)

            return $this->render('task', ['task' => $task, 'start' => $start, 'finish' => $finish, 'did' => Yii::$app->request->post('did')]);
        }
        else return $this->render('task');
        
//        $task = Task::find()->asArray()->where(['>=', 'dttask', '2017-10-01'])->andWhere(['<=', 'dttask', '2017-10-31'])->with('employe')->orderBy('dttask')->all();
//        return $this->render('task', ['task' => $task]);
    }
    
    
}
