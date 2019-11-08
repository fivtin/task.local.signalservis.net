<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\db\ActiveRecord;
use yii\web\Session;
use Yii;

/**
 * Description of Year
 *
 * @author vitt
 */
class Year extends ActiveRecord {
    
    public static function tableName() {
        
        return 'year';
    }
    
    
    // Функция возвращает символьную последовательность описаний дней недели для выбранного года конкретного отдела
    // поиск идет в сессии, если нет, то в базе, если нет то создается автоматически
    // 
    // начиная с первого дня года в виде 000110001...1200 - где 0 - рабочий день, 1 - выходной день, 2 - предпразничный, 3 - праздничный
    // на входе функции нужный год или 2000(по умолчанию) для получения текущего года
    // sYear = Search Year - искомый год
    
    public function getYear365 ($sYear = 0) {

//        Yii::$app->session->removeAll();
        
        // если данных на входе нет, то используем текущий год
        if ($sYear == 0) $sYear = date("Y");

//        $sName = 'Year_'.$sYear.'_'.Yii::$app->user->identity->did; // sName = Session Name - имя переменной сессии
        $sName = 'Year_'.$sYear;
        
        if (!Yii::$app->session->has($sName)) {
            
            // если переменная не определена в сессии, то пробуем считать из базы данных            
            //$db_year = self::find()->where(['year' => $sYear])->andWhere(['did' => Yii::$app->user->identity->did])->limit(1)->all();
 //           $db_year = self::find()->where(['year' => $sYear])->andWhere(['did' => 1])->limit(1)->all();
            $db_year = self::find()->where(['year' => $sYear])->limit(1)->all();
            
            if (!$db_year) {
                
                // если в базе нет записи, то формируем ее автоматически
                $y_str = '';
                $y_start = mktime(12, 0, 0, 1, 1, $sYear); // метка времени начала года
		$y_end = 365 + date("L", $y_start); // метка времени конца года		
		for ($turn = 0; $turn < $y_end; $turn++) {
                    $HDay = date("w", ($y_start + ($turn * 86400)));
                    if (($HDay == 0) || ($HDay == 6))
                             $y_str = $y_str.'1';
                    else $y_str = $y_str.'0';
                }
                
                // вносим в сессию результат
                Yii::$app->session->set($sName, $y_str);
            }
            else {
                
                // иначе вносим в сессию данные
                Yii::$app->session->set($sName, $db_year[0]['days']);
            }
        }
        
        return Yii::$app->session->get($sName);        
    }
    
    
    // функция возращает статус дня для входящей даты (метка времени) HollyDay
    
    public function getHDay($ltInput) { // Label Time Input - метка времени входящая 
        
        $Year = date("Y", $ltInput);
        $Day = date("z", $ltInput);
        $HDays = self::getYear365($Year);
        return $HDays[$Day];
    }
    
    // функция возращает дату следующего рабочего или предпраздничного дня
    
    public function getNextWDay() {
        
        $date = time();
        do {
            $date = $date + 86400;
            $hday = self::getHDay($date);
        }
        while (($hday == "1") OR ($hday == "3"));
        return date("Y-m-d", $date);
    }
    
    // функция возращает массив с последовательностью статусов дня для указанного интервала включительно
    
    public function getDaysArray($start, $finish) { // даты в формате ГГГГ-ММ-ДД
        
        $result = array();
        $ltStart = strtotime($start." 12:00");
        $ltFinish = strtotime($finish." 12:00");
        $ltCurrent = $ltStart;
        $i = 0;
        while ($ltCurrent <= $ltFinish) {
            
            $result[$i][0] = self::getHDay($ltCurrent);
            $result[$i][1] = date("Y-m-d", $ltCurrent);
            $i++;
            $ltCurrent = $ltCurrent + 86400;
        }
        //$days = 
        
        return $result;
    }
    
    
}
