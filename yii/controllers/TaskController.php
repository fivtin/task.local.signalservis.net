<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;
//use yii\web\Controller;
use app\controllers\ExtController;
use app\models\Task;
use app\models\Note;
use app\models\Tunit;
use app\models\Employe;
//use app\models\Work;
//use app\models\Relemp;
//use app\models\Whour;
//use app\models\Reletm;
use Yii;
use app\models\Login;
use yii\web\Session;
//use app\models\Year;
use app\models\Index;
use app\models\Log;
use yii\filters\AccessControl;


use app\models\Typework; // временно для newtask
//use app\models\Employe;  // временно для newtask
use app\models\Equipment; // временно для newtask

/**
 * Description of TaskController
 *
 * @author vitt
 */

class TaskController extends ExtController {
    // класс ExtController проверяет авторизацию на сайте
    
    // возвращает список задач на конкретную дату
    // или если дата не выбрана то список активных задач
    public function actionIndex() {
        
        $index = new Index;
        $notes = Note::find()->asArray()->where(['uid' => Yii::$app->user->id, 'status' => 0])->andWhere(['!=', 'prompt', 0])->andWhere(['<= ', 'notedate', date("Y-m-d")])->all();
        if (Yii::$app->request->isGet && !empty(Yii::$app->request->get())) {
              $index->processRequest(Yii::$app->request->get());

        }
        // если на входе нет параметров, 
        else {
            // то анализируем значение сессии "select"
            if ((Yii::$app->session->has('select')) && Yii::$app->session->get('select')) {
                $index->date = Yii::$app->session->get('date');
                $index->select = true;
            }
            else $index->runDefault();
        }
        if ($index->select) Log::recLog('main', 'date='.$index->date);
        else Log::recLog('main', 'refresh, date='.$index->date);
        return $this->render('index', ['index' => $index, 'notes' => $notes]);
       
    }
    
    // выводит данные задачи по её ID - tid
    //
    public function actionView () {

        //if (Yii::$app->user->isGuest) return Yii::$app->response->redirect('/task/login');
        
        // если на входе id то ищем данные для задачи,
        // если на входе new, то создаем новую задачу
        // иначе отображаем главную страницу
        
        if (Yii::$app->request->isGet) {
            $tunit = new Tunit(Yii::$app->request->get('id'),
                               Yii::$app->request->get('date') ? Yii::$app->request->get('date') : 0,
                               Yii::$app->request->get('copy') ? Yii::$app->request->get('copy') : 0
                              );
            
            if ($tunit->error) return $this->goHome();
            else { 
                
                Log::recLog('view', Yii::$app->request->get('id'));
                return $this->render('view', ['tunit' => $tunit]);                
            }
        }
        else return $this->goHome();
    }
    
    public function actionSearch() {
        
        if (Yii::$app->request->post('search') == '') return $this->goHome();
        
        $notes = Note::find()->asArray()->where(['uid' => Yii::$app->user->id, 'status' => 0])->andWhere(['!=', 'prompt', 0])->andWhere(['<= ', 'notedate', date("Y-m-d")])->all();
        //$notes = Note::find()->asArray()->where(['uid' => Yii::$app->user->id, 'status' => 0])->andWhere(['<= ', 'notedate', date("Y-m-d")])->all();
        
        // если передано число, то пытаемся найти запись с таким индексом
        if (is_numeric(Yii::$app->request->post('search'))) {
            Log::recLog('search', Yii::$app->request->post('search'));
            return Yii::$app->response->redirect('/task/'.Yii::$app->request->post('search'));
        }
        // иначе пробуем найти строку
        else {
            $index = new Index;
            $index->search = Yii::$app->request->post('search');
            Log::recLog('search', Yii::$app->request->post('search'));
            return $this->render('index', ['index' => $index, 'notes' => $notes]);
        }
    }
    
    public function actionAction () {
        
        // выполняются действия с задачей
        //       (нужно переместить все операции в контроллер view и
        //        выполнять операции в зависимости от принятых данных:
        //        post или get)
        //
        // save - сохраняем данные после проверки, иначе переходим с этими данными обратно в форму + сообщение об ошибке
        // remove - удаляем задачу с указанным tid и все её данные (task, relemp, reletm, work)
        // copy - создаем новую задачу с данными из указанной (только работы)
        // done - ??? отмечаем как выполненную ??? (лучше просто использовать чекбокс[статус]
        // cancel - ничего не выполняем и переходим на главную страницу
        
        if (Yii::$app->request->isPost) {
        
            if (Yii::$app->request->post('action') == 'cancel') return $this->goHome();
        
            $tunit = new Tunit(); // new Tunit('remove', Yii::$app->request->post('tid'));
            
            // !!! может передавать action в конструктор и там уже анализировать/выполнять ???
            // !!! или просто передавать весь массив post ???
            // if (!$tunit->validateAction(Yii::$app->request->post('action'))) return $this->goHome();
            $tunit->attributes = Yii::$app->request->post();
        
            if (Yii::$app->request->post('action') == 'remove') {
            
                if ($tunit->delete()) {
                    
                    // сообщение об удачном удалении, переход на главную
                    Yii::$app->session->setFlash('success', 'Запись успешно удалена.');
                    return $this->goHome();
                }
                else {
                    
                    // сообщение об ошибке
                    
                    Yii::$app->session->setFlash('danger', 'При удалении возникла ошибка. Возможно нет прав на выполнение данной операции.');
                    return $this->goHome();
                }
                
            }
            
            if (Yii::$app->request->post('action') == 'save') {

                if ($tunit->validate() && $tunit->save()) { 
                
                    // 
                    Yii::$app->session->setFlash('success', 'Изменения сохранены.');
                    return $this->goHome();
                }
                else {
                    
                    // сообщение об ошибке, переход на страницу редактирования

            
            
                    Yii::$app->session->setFlash('danger', 'При сохранении записи возникла ошибка. Проверьте правильность заполнения формы.');
                    Log::recLog('error', 'tid='.$tunit->tid);
                    //Yii::$app->session->setFlash('actionResult', 'При сохранении записи возникла ошибка. Возможно нет прав на выполнение данной операции.');
                    return $this->render('view', ['tunit' => $tunit]);
                }
            
            
            }
        
            if (Yii::$app->request->post('action') == 'copy') {
            
                // ??? проверить возможность копирования ???
                $tunit = $tunit->getCopy(Yii::$app->request->post('tid'));
                
                return $this->render('view', ['tunit' => $tunit]);
            }
            
            if (Yii::$app->request->post('action') == 'done') {
            
                // 
                if ($tunit->done()) {
                    
                    Yii::$app->session->setFlash('success', 'Задача закрыта.');
                    return $this->goHome();
                }
                else {
                    Yii::$app->session->setFlash('danger', 'При сохранении записи возникла ошибка. Возможно нет прав на выполнение данной операции.');
                    return $this->render('view', ['tunit' => $tunit]);
                }
            }
            
            if (Yii::$app->request->post('action') == 'restore') {
            
                // 
                if ($tunit->restore()) {
                    
                    Yii::$app->session->setFlash('info', 'Отметка о выполнении задачи снята.');
                    //$tunit->status = 0;
                    //$tunit->title = htmlspecialchars($tunit->title);
                    //$tunit->descr = htmlspecialchars($tunit->descr);
                    return $this->redirect('/task/'.$tunit->tid);
                    //return $this->render('view', ['tunit' => $tunit]);
                }
                else {
                    Yii::$app->session->setFlash('danger', 'Не удалось снять отметку о выполнении задачи.');
                    return $this->redirect('/task/'.$tunit->tid);
                    //return $this->render('view', ['tunit' => $tunit]);
                }
            }
            
            
        
        //$tunit->load(Yii::$app->request->post(), '');
        
        
        
        return Yii::$app->response->redirect('index');
        ?><pre><?php var_dump(Yii::$app->request->post()); die;
        
        }
        else
            return $this->goHome();
    }
    
    public function actionTest () {
        
        return $this->render('test');
    }
    
    public function actionLogin () {
        
        if (!Yii::$app->user->isGuest) return $this->goHome();//Yii::$app->response->redirect('/');
        
        $login_model = new Login();
        
        if (Yii::$app->request->post('Login')) {
            
//            var_dump(\Yii::$app->request->post('Login'));
//            die();
            $login_model->attributes = Yii::$app->request->post('Login');
            
            if ($login_model->validate()) {
                
                Yii::$app->user->login($login_model->getUser(), 3600 * 24);
                Log::recLog('login', Yii::$app->user->identity->login);
                if (Yii::$app->user->identity->did == 7) return $this->redirect(['/stv']);
                if (Yii::$app->user->identity->did != 5) return $this->goHome();
                else $this->redirect(['/support']);
//                var_dump("Мы прошли валидацию.");
//                die();
            }
        }
        
        return $this->render('login', ['login_model' => $login_model]);
    }
    
    public function actionLogout () {
        
        if (!Yii::$app->user->isGuest) {
            
            Yii::$app->user->logout();
            return $this->redirect(['login']);
        }
        
    }
    
    public function actionSelect () {
        
        return $this->render('select');
    }
    
    public function actionInfo () {
        
        return $this->render('info');
    }
    
    public function actionNewtask () {
        
        $tw = Typework::find()->asArray()->where(['status' => 1])->orderBy('title')->all();
        $emp = Employe::find()->asArray()->where(['status' => 1])->orderBy('fio_short')->all();
        $cat = Equipment::find()->asArray()->orderBy('title')->all();
        return $this->render('newtask', ['tw' => $tw, 'emp' => $emp, 'cat' => $cat]);
    }
    
/*    public function actionNew_year () {
        $this->layout = 'new_year';
        
        Log::recLogNY();
        return $this->render('new_year');
        
    }
    
    public function actionHb () {
        Log::recLogNY('hb');
        return $this->render('hb');
        
    }
    public function actionHb2019 () {
        Log::recLogNY('hb2019');
        return $this->render('hb2019');
    }
*/
    
    public function actionTypework () {
        
        $twork = Typework::find()->select('twid, title, detail, info')->asArray()->where(['did' => Yii::$app->user->identity->did, 'status' => 1])->orderBy('title')->all();
        Log::recLog('view', 'type work info');
        return $this->render('typework', ['twork' => $twork]);
    }

}
