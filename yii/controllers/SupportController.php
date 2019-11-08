<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

use Yii;
use app\models\Yandex;
use app\models\Support;
use app\controllers\ExtController;

/**
 * Description of SupportController
 *
 * @author vitt
 */
class SupportController extends ExtController {
    //put your code here
    
    public function actionAJAX() {
        if (Yii::$app->request->isAjax) {
            
        }
    }
    
    public function actionStat () {
        
        $support = array();
        
        return $this->render('tv', ['support' => $support]);
    }
    
    public function actionTv () {
        
        if (Yii::$app->request->get('param')) {
        $support = Support::find()->
                            asArray()->
                            where(['status' => 1])->
                            andWhere(['>', 'stype', Yii::$app->request->get('param')])->
                            andWhere(['=', 'sservice', 'ТВ'])->
                            orderBy(['stype' => SORT_DESC])->
                            with('xy')->
                            all(); }
        else {
        $support = Support::find()->
                            asArray()->
                            where(['status' => 1])->
                            andWhere(['=', 'sservice', 'ТВ'])->
                            orderBy(['stype' => SORT_DESC])->
                            with('xy')->
                            all(); }
        
        return $this->render('tv', ['support' => $support]);
    }
    
    public function actionInternet () {
        
        $support = Support::find()->
                            asArray()->
                            where(['status' => 1])->
                            andWhere(['=', 'sservice', 'Интернет'])->
                            orderBy(['stype' => SORT_DESC])->
                            with('xy')->
                            all();
        
        return $this->render('internet', ['support' => $support]);
    }

        public function actionIndex () {
        $post = '';
        // если не установлена дата, устанавливаем сегодняшнюю (храним её в сессии)
        if (!Yii::$app->session->get('sdate')) Yii::$app->session->set('sdate', date("Y-m-d"));
        
        if (Yii::$app->request->isPost) {
            
            // добавляется новая заявка
            $post = new Support;
            $post->attributes= Yii::$app->request->post();
            if ($post->validate()) {
                $post->scomment = htmlspecialchars($post->scomment);
                // все данные получили, сохраняем
                $post->save();
                // устанавливаем выбранную дату
                Yii::$app->session->set('sdate', Yii::$app->request->post('sdate'));
                return $this->redirect('');
            }
            else {
                // какая то ошибка в данных, возможно нужно просто поменять дату
                if (strtotime(Yii::$app->request->post('sdate'))) { 

                    // корректно
                    Yii::$app->session->set('sdate', Yii::$app->request->post('sdate'));
                }
            }
        }
        
        if (Yii::$app->request->isGet) {
            
            // удаление заявки
            if (Yii::$app->request->get('remove')) {
                Support::remove(Yii::$app->request->get('remove'));
                Yii::$app->response->redirect('/support');
            }
            if (Yii::$app->request->get('execute')) {
                
                
                Support::execute(Yii::$app->request->get('execute'), Yii::$app->request->get('prompt', ''));
                Yii::$app->response->redirect('/support');
            }
            if (1 == 0) { ?><pre><?= var_dump(Yii::$app->request->get()) ?></pre><?php die; }
        }
        
        $support = Support::find()->
                            asArray()->
                            where(['status' => 0])->
                            //andWhere(['did' => Yii::$app->user->identity->did])->
                            andWhere(['<=', 'sdate' ,Yii::$app->session->get('sdate')])->
                            //andWhere(['>=', 'stime', date("h:s", time())])->
                            orderBy('stime')->
                            with('xy')->
                            all();
//        $support = Support::find()->
//                            asArray()->
//                            where(['>', 'stype', 0])->
//                            andWhere(['<', 'stype', 40])->
//                            andWhere(['<>', 'sreport', ""])->
//                            orderBy('stime')->
//                            with('xy')->
//                            all();
        $yandex = Yandex::find()->asArray()->orderBy('street, home')->all();
        return $this->render('index', ['yandex' => $yandex, 'support' => $support, 'post' => $post]);
    }
    
}
