<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\base\Model;
use app\models\Year;

/**
 * Description of Tcalendar
 *
 * @author vitt
 */

class Calendar extends Model {
    
    
    public $days = array();    // массив дней с доп. полями
    public $start;   // дата начала для поиска
    public $finish;  // дата окончания для поиска
    public $first;   // дата начала отчетного периода включительно
    public $last;    // дата окончания расчетного периода включительно
    public $month;   // месяц отчетного периода
    public $year;    // год отчетного периода
    
    public $countSF; // кол-во дней между start и finish
    public $countFL; // кол-во дней между first и last

    private $ltStart;
    private $ltFinish;
    private $ltFirst;
    private $ltLast;


    // ФУНКЦИИ ДЛЯ ПОЛУЧЕНИЯ РАЗЛИЧНЫХ ДОПОЛНИТЕЛЬНЫХ ДАННЫХ ДЛЯ КАЛЕНДАРЯ
    //
    //
    

    public function countDaySF() {
        
       return ($this->ltFinish - $this->ltStart) / (24 * 3600) ;
    }

    // при создании календаря формирование списка дней
    public function __construct ($month = 0, $year = 0) {
        
        parent::__construct();
        
        if ($month == 0) $month = date("m");
        if ($year == 0) $year = date("Y");
        
        $this->month = $month;
        $this->year = $year;
        
        $ltToday = mktime(12, 0, 0, date("m"), date("d"), date("Y"));                 // метка времени текущего дня
        $this->ltFirst = mktime(12, 0, 0, $month, 1, $year);                          // метка времени первого дня месяца
        $this->ltLast = mktime(12, 0, 0, $month, date("t", $this->ltFirst), $year);   // метка времени последнего дня месяца
        
        $this->ltStart = $this->ltFirst - (3600 * 7 * 24);                            // метка времени даты начала вывода графика
        $this->ltFinish = $this->ltLast + (3600 * 7 * 24);                            // метка времени даты окончания вывода графика


//        //$this->date = $indate;
//        //$clDay   = $this->date[8].$this->date[9];                               // число
//        //$clMonth = $this->date[5].$this->date[6];                               // месяц
//        //$clYear  = $this->date[0].$this->date[1].$this->date[2].$this->date[3]; // год
//        
//        $ltDate =  mktime(12, 0, 0, $clMonth, $clDay, $clYear);                 // метка времени текущего дня
//        $ltFirst = mktime(12, 0, 0, $clMonth, 1, $clYear);                      // метка времени первого дня месяца
//        $ltLast = mktime(12, 0, 0, $clMonth, date("t", $ltDate), $clYear);      // метка времени последнего дня месяца
//        $wfirst = date("N", $ltFirst);                                          // день недели первого дня месяца
////        if ($wfirst == 0) $wfirst = 7;
////        if ($wfirst == 1) $wfirst = $wfirst + 8;                              // сдвигаем первый день на неделю
////        if ($wfirst == 2) $wfirst = $wfirst + 9;                              //   если он приходится на понедельник, вторник
//        if ($wfirst < 4) $wfirst = $wfirst + 7;                                 //     или среду
//        $start =  $ltFirst - (86400 * ($wfirst - 1));                           // метка времени первого выводимого дня
//        // считается как первый день месяца минус смещение = день недели * кол-во секунд в сутках
        
        // формируем дату начала и дату окончания в запросе, а также дату начала и окончания периода
        $this->first = date("Y-m-d", $this->ltFirst);
        $this->last = date("Y-m-d", $this->ltLast);
        
        $this->start =  date("Y-m-d", $this->ltStart);
        $this->finish = date("Y-m-d", $this->ltFinish);  //+ (86400 * 41)
        
        $curDate = date("Y-m-d");  // текущая дата для формирования списка "2017-12-25"
//        $curDay = date("j");        // номер текущего дня для формирования списка "4"

        $this->countSF = ($this->ltFinish - $this->ltStart) / (24 * 3600);
        $this->countFL = ($this->ltLast - $this->ltFirst) / (24 * 3600);
        
        
        for ($i = 0; $i <= $this->countSF; $i++) {
            $t = $this->ltStart + ($i * 86400);
            $this->days[$i]['showDay'] = date("j", $t);          // число месяца для отображения "25"
            $this->days[$i]['dtLink'] = date("Y-m-d", $t);       // дата дня формирования для ссылки "2017-02-16"
            if ($t < $this->ltFirst || $t > $this->ltLast)
                $this->days[$i]['curMonth'] = false;
            else $this->days[$i]['curMonth'] = true;             // относится ли день к выводимому месяцу
            
            // устанавливаем статус дня из календаря года
            // 0 - рабочий день, 1 - выходной, 2 - предпразничный, 3 - праздничный

            
            $this->days[$i]['HDay'] = Year::getHDay($t); // HollyDay
            
            // для текущего месяца нужно отметить рамку совпадения дня и выделение фоном сегодняшней даты
            // считаем по умолчанию несовпадение всего
//            $this->days[$i]['equal'] = false;
            $this->days[$i]['today'] = false;
            
            if ($this->days[$i]['curMonth'] && ($this->days[$i]['dtLink'] == $curDate))
                $this->days[$i]['today'] = true;
                
//                // проверяем совпадение числа месяца
//                if ($this->days[$i]['showDay'] == $curDay) {
//                    $this->days[$i]['equal'] = true;
//                    
//                    // и в этом случае проверяем точное совпадение даты
//                    if ($this->days[$i]['dtLink'] == $curDate)
//                        $this->days[$i]['today'] = true;
//                }                
//            }
        }
        
    }

}