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
                $coord = json_encode($coord[0]);
                return $coord;
            }
        }
        return '';        
    }
    
}
