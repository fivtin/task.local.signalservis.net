<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

use yii\web\Controller;
use Yii;
use app\models\Yandex;
use app\models\Salary;
use app\models\Report;
use app\models\Paysalary;
use app\models\Payout;
use app\models\Employe;

/**
 * Description of ApiController
 *
 * @author vitt
 */
class AjaxController extends ExtController {
    
    
    
    public function actionTable () {
        return 'Ajax: Table';
    }
    public function actionTask () {
        return 'Ajax: Task';
    }

    
    // возращает ближайший дом к введенным координатам
    
    public function actionMapNearHouse () {
        
        if (Yii::$app->request->isGet) {
            
            $x = htmlspecialchars(Yii::$app->request->get('xcor', false));
            $y = htmlspecialchars(Yii::$app->request->get('ycor', false));
            $r = 0;
            if ($x && $y) {
                do {
                    $r = $r + 0.00025;
                    $coord = Yandex::find()->select("*, (power((`xcor`-".$x."), 2) + power((`ycor`-".$y."), 2)) as `sss`")->where('power((`xcor`-'.$x.'), 2) + power((`ycor`-'.$y.'), 2) <= power('.$r.', 2)')-> orderBy('sss')->asArray()->all();
                }
                while (count($coord) == 0);
                $result = json_encode($coord[0]);
                return $result;
            }
        }
        return '';        
    }
    
    // возращает в виде массива зарплату сотрудников за указанный месяц для указанного отдела или для всех
    
    public function actionReturnEmployeesSalaryForMonth () {
        
        
        if (Yii::$app->request->isGet) {
            
            $did = htmlspecialchars(Yii::$app->request->get('did', 0));
            $month = htmlspecialchars(Yii::$app->request->get('month', date("m")));
            $year = htmlspecialchars(Yii::$app->request->get('year', date("Y")));
        }
        
        if ($did == 0) {
            $salary = Salary::find()->asArray()->where(['=', 'sldate', $year.$month])->with('employe')->with('payout')->all();
        }
        else {
            $salary = Salary::find()->asArray()->where(['=', 'sldate', $year.$month])->with('employe')->with('payout')->all();
        }
        return json_encode($salary);
        //return $this->render('index', ['result' => $salary]);
    }
    
    public function actionGetSalaryForMonth () {
        
        $result = '';
        if (Yii::$app->request->isGet) {
            
            $month = htmlspecialchars(Yii::$app->request->get('month', date("m")));
            $year = htmlspecialchars(Yii::$app->request->get('year', date("Y")));
                        
            $start = htmlspecialchars(Yii::$app->request->get('start', $year.'-'.$month.'-01'));
            $finish = htmlspecialchars(Yii::$app->request->get('finish', $year.'-'.$month.'-'.date("t", strtotime($start))));
            
            $salary = new Salary($month, $year);
            $salary->addReport($start, $finish);

            return json_encode($salary->result);
        }
        
        return $result;
        //return $this->render('index', ['result' => $salary->result]);
    }
    
    public function actionCopySalaryFromPrevMonth () {
        
        $result = '';
        if (Yii::$app->request->isGet) {
            $eid = htmlspecialchars(Yii::$app->request->get('eid'));
            $sldate = htmlspecialchars(Yii::$app->request->get('sldate'));
            
            $year = $sldate[0].$sldate[1].$sldate[2].$sldate[3];
            $month = $sldate[4].$sldate[5];
            
            if ($month == '01') {
                $month = 12;
                $year = $year - 1;
                
            }
            else $month = $month - 1;
            if (count($month) == 1) $month = '0'.$month;
            $_sldate = $year.$month;
            
            $paysalary = Paysalary::find()->where(['=', 'eid', $eid])->andWhere(['=', 'sldate', $_sldate])->limit(1)->all();
            $paysalary = $paysalary[0];
            
            //return var_dump($paysalary);
                
            $id = $paysalary->id;
            
            $paysalary->id = null;
            $paysalary->isNewRecord = true;
            $paysalary->sldate = $sldate;
            
            $paysalary->save();
            $new_id = Yii::$app->db->getLastInsertID();
            
            
            
            $payout = Payout::find()->where(['=', 'salary_id', $id])->all();
            
            $i = 0;
            foreach ($payout as $item) {
                $item->id = null;
                $item->isNewRecord = true;
                $item->salary_id = $new_id;
                if ($item->type != 'onetime') {
                    $item->save();
                    $i++;
                }
            }
            $result = 'Добавлено: '.$i.' полей.';
            
        }
        return $result;
    }
    
    public function actionBlockSalary () {
        
        $result = '';
        if (Yii::$app->request->isGet) {
            $eid = htmlspecialchars(Yii::$app->request->get('eid'));
            $sldate = htmlspecialchars(Yii::$app->request->get('sldate'));
            
            $paysalary = Paysalary::find()->where(['=', 'eid', $eid])->andWhere(['=', 'sldate', $sldate])->limit(1)->all();
            if (count($paysalary) > 0) {
                $paysalary = $paysalary[0];
                $paysalary->block = 1;

                // !!!!!!!!!!!
                // нужно убедиться что для записи есть хоть одна запись с начислениями

                $payout = Payout::find()->asArray()->where(['=', 'salary_id', $paysalary->id])->all();
                if (count($payout) > 0) {

                    if ($paysalary->save())
                        $result = 'Успешно! Выплаты зафиксированы в БД.';
                }
                else {
                    $result = 'Ошибка! Для этой записи не сформировано ни одной выплаты.';
                }
            }
            else {
                $result = 'Ошибка! Запись отсутствует.';
            }
            
            
            
        }
        return $result;
    }

    public function actionCreateSalaryFromTemplate() {
        
        if (Yii::$app->request->isGet) {
            $eid = htmlspecialchars(Yii::$app->request->get('eid'));
            $sldate = htmlspecialchars(Yii::$app->request->get('sldate'));
            $salary = htmlspecialchars(Yii::$app->request->get('salary'));
            $award = htmlspecialchars(Yii::$app->request->get('award'));
            $summa = htmlspecialchars(Yii::$app->request->get('summa'));
            
            $paysalary = new Paysalary();
            $paysalary->eid = $eid;
            $paysalary->sldate = $sldate;
            $paysalary->payment = $salary;
            $paysalary->award = $award;
            $paysalary->save();
            $new_id = Yii::$app->db->getLastInsertID();
            
            $payout = Payout::find()->where(['=', 'salary_id', 0])->all();
            

            foreach ($payout as $item) {
                $item->id = null;
                $item->isNewRecord = true;
                $item->salary_id = $new_id;
                if (strpos($item->base, 'summa=')) $item->base = 'summa=' .$summa;
                $item->save();
            }
            return 'Выполнено! Начисления добавлены.';
            
        }
        return '';
    }
    
    public function actionLoadModalForm() {
        if (Yii::$app->request->isGet) {
            $eid = htmlspecialchars(Yii::$app->request->get('eid'));
            $sldate = htmlspecialchars(Yii::$app->request->get('sldate'));
            
            $result = Array();
           
            $employe = Employe::find()->select('eid, fio, fio_short, post')->asArray()->where(['=', 'eid', $eid])->limit(1)->all();
            
            $report = new Report();
            
            
            $result['eid'] = $eid;
            $result['sldate'] = $sldate;
            $result['fio'] = $employe[0]['fio'];
            $result['fio_short'] = $employe[0]['fio_short'];
            $result['post'] = $employe[0]['post'];
            
            $sf = getDateFromSldate($sldate);
            $result['report'] = $report->getDateRepOnEid($sf['start'], $sf['finish'], $eid);
            
            
            $paysalary = Paysalary::find()->asArray()->where(['=', 'eid', $eid])->andWhere(['=', 'sldate', $sldate])->limit(1)->all();
            if (count($paysalary) > 0) {
                
                $result['id'] = $paysalary[0]['id'];
                $result['salary'] = $paysalary[0]['payment'];
                $result['award'] = $paysalary[0]['award'];
                $result['block'] = $paysalary[0]['block'];
                 
                $payout = Payout::find()->asArray()->where(['=', 'salary_id', $paysalary[0]['id']])->all();
                foreach ($payout as $pay) {
                    $result['payout'][] = $pay;
                }
            }
            else {
                
                $result['id'] = -1;
                $result['salary'] = -1;
                $result['award'] = -1;
                $result['block'] = -1;
                $result['payout'] = array();
                
            }
            return json_encode($result);
        }
        return '';
    }
    
// /ajax/create-salary-from-template?eid=" + eid + "&sldate=" + sldate + '&salary=' + salary + '&award=' + award + '&summa=' + summa

//    public function actionDuplicatePaySalary() {
//        
//        if (Yii::$app->request->isGet) {
//            $eid = htmlspecialchars(Yii::$app->request->get('eid', ''));
//            $eid = htmlspecialchars(Yii::$app->request->get('sldate', ''));
//            
//            
//        }
//    }
    
    
}
