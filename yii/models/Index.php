<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use Yii;
use yii\base\Model;
//use app\models\Tcalendar;
//use app\models\Task;


/**
 * Description of Main
 *
 * @author vitt
 */

// модель для index.php
// 
// определяет, какую информацию выводить:
// если выбрана дата, то ввыводим данные на эту дату,
// если дата не выбрана - выбираем все активные задачи
// также в зависимости от полученных данных управляет календарем




class Index extends Model {
    
    public $date;            // текущая дата
    public $select = false;  // показывает что дата выбрана пользователем
    public $search = '';     // строка поиска, по умолчанию пустая
    
    const Actions = [0 => 'prev', 1 => 'next', 2 => 'only', 3 => 'set']; // only - надо добавить в urlManager что бы заработало
    const Directs = [0 => 'month', 1 => 'year', 2 => 'today', 3 => 'week', 4 => 'reset']; // 2-4??? так же добавить
    
    
    // проверка действия и пути на корректность (значения присутствуют в массивах) 
    public function validateAction ($action, $direct) {
    
        return (in_array($action, self::Actions) && in_array($direct, self::Directs)) ? true : false;        
    }
    
    // анализирует входные get/?post параметры и выполняет соответствующее действие
    public function processRequest ($request) {

        // !!! нужно ли хранить пареметры даты и выбор даты в сессии??? (НУЖНО, УЖЕ СДЕЛАНО)
        
        $error = false;
        if (empty($request)) { // данная операция не нужна, так как массив проверяется ранее

            $this->select = false;
        }
        
        // на входе (дата)?
        if (isset($request['date'])) {
            
            // проверяем корректность даты и применяем
            if (strtotime($request['date'])) { 

                // корректно
                $this->date = $request['date'];
                $this->select = true;
                Yii::$app->session->set('date', $this->date);
                Yii::$app->session->set('select', true);
            }
            else $this->runDefault();            
        }
        
        // на входе (действие, путь)? и они корректны?
        if (isset($request['action']) && 
            isset($request['direct']) && 
            $this->validateAction($request['action'],
                                 $request['direct'])) {
            
            // выполняем действие            
            
            if ($request['action'] == 'set') {
                if ($request['direct'] == 'today') $this->runDefault(true);
                if ($request['direct'] == 'reset') $this->runDefault();
            }
            else {
            
                // так как старого значения даты у нас нет, то считываем его из сессии
                if (Yii::$app->session->has('date'))
                     $date = Yii::$app->session->get('date');
                else $date = date("Y-m-d");
                $date = strtotime($date);

                // сбрасываем значение выбранного дня
                $this->select = false;

                if ($request['action'] == 'prev')                
                    // prev
                    if ($request['direct'] == 'month')                    
                        // prev-month
                        $date = strtotime('-1 month', $date);
                    else
                        // prev-year
                        $date = strtotime('-1 year', $date);
                else
                    // next
                    if ($request['direct'] == 'month')                    
                        // next-month
                        $date = strtotime('+1 month', $date);
                    else                    
                        // next-year
                        $date = strtotime('+1 year', $date);

                $this->date = date("Y-m-d", $date);
                Yii::$app->session->set('date', $this->date);
                Yii::$app->session->set('select', false);
                $this->select = false;
            }
        }        
        
        if ($error) $this->runDefault();
    }
    
    public function runDefault ($select = false) {
        $this->date = date("Y-m-d");
        $this->select = $select;
        Yii::$app->session->set('date', $this->date);
        Yii::$app->session->set('select', $select);
    }


 

    
}
