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

class Tcalendar extends Model {
    
    
    public $days;    // массив дней с доп. полями
    public $start;   // дата начала для поиска
    public $finish;  // дата окончания для поиска
    private $date;   // дата, для которой строится календарь
    
    // ФУНКЦИИ ДЛЯ ПОЛУЧЕНИЯ РАЗЛИЧНЫХ ДОПОЛНИТЕЛЬНЫХ ДАННЫХ ДЛЯ КАЛЕНДАРЯ
    //
    //
    // 1. ВОЗРАЩАЕТ МАССИВ СТАТУСОВ ЗАДАЧ ДЛЯ КАЛЕНДАРЯ
    // 
    // массив для каждого дня содержит 1 число:
    // 0 - нет задач,
    // 1 - все задачи выполнены,
    // 2 - есть просроченные задачи (status = 0 && dttask < CurrentDate)
    // 3 - запланированная задача,
    // 4 - есть хоть одна запланированная задача без исполнителей и времени

    public function getTaskStatus () {
        
        // инициализируем переменную в которой возращается результат
        $result = array();
        // получаем массив задач для диапазона календаря
        $tasks = Task::getTaskForCalendar($this->start, $this->finish);
        // устанавливаем текущую дату для анализа просроченных задач
        $curDate = date("Y-m-d");
        
        // запускаем цикл для всех дат из календаря
        for ($i = 0; $i < 42; $i++) {
        
        $result[$i] = 0; // по умолчанию устанавливаем статус "нет задач"
        
        // обходим по кругу список полученных задач
            foreach ($tasks as $task) {
                
                // критерий отбора из списка задач - совпадение даты днязадачи и даты задачи
                if ($this->days[$i]['dtLink'] == $task['dttask']) {
                    
                    // сначала определяем меньше ли дата текущей (прошлые дни)
                    if ($this->days[$i]['dtLink'] < $curDate) {
                        
                        // прошедшее время
                        // достаточно только проанализировать выполнение задачи,
                        // так как в случае невыполнения пометить как просроченную,
                        // независимо от того есть ли исполнители и/или время выполнения
                        if ($task['status'] == 0) $result[$i] = 2;  // при обнаружении невыполненной задачи устанавливаем статус "2"
                        
                        else if ($result[$i] != 2) $result[$i] = 1; // при обнаружении выполненной задачи устанавливаем статус "1",
                                                                    // но только в случае если ранее не встретились невыполненные задачи
                    }
                    else {
                        
                        // текущая или будущая дата
                        // здесь помимо выполнения конкретной задачи 
                        // также учитывается наличие других задач без исполнителей и/или времени
                        // если мы уже имеем задачу без исполнителей/времени, то дальнейший анализ не имеет смысла
                        if ($result[$i] != 4) {
                            
                            // пока нет задач без исполнителей/времени
                            if ($task['status'] == 0) {
                                
                                // задача не выполнена, нужен дополнительный анализ
                                // если нет исполнителей или времени, устанавливаем соответствующий статус
                                if ((count($task['relemp']) == 0) || (count($task['reletm']) == 0) || count($task['works']) == 0) $result[$i] = 4;
                                else $result[$i] = 3;
                            }
                            else {
                                
                                // задача выполнена,
                                // устанавливаем статус только в случае, если до этого был статус "нет задач"
                                // в других случаях оставляем текущий статус
                                if ($result[$i] == 0) $result[$i] = 1;
                            }
                        }
                    }
                    
                    // удаляем обработанный элемент массива задач для ускорения работы
                    unset($tasks[key($tasks)]);
                }
            }        
        }
        
        return $result;
    }
    

    public function setTasks ($tasks) {

        // текущая дата для определения просроченных заявок status = 0 && dttask < curDate
        $curDate = date("Y-m-d");
        
        // запускаем цикл для всех дней массива days
        for ($i = 0; $i < 42; $i++) {
            
            $this->days[$i]['stTask'] = 0;              // статус задач для дня
            // 0 - нет задач, 1 - все задачи выполнены, 2 - есть просроченные задачи (status = 0 && dttask < CurrentDate)
            // 3 - запланированная задача, 4 - есть хоть одна запланированная задача без исполнителей
            // ??? может сразу описывать класс для отображения ???
            
            // обходим список задач
            foreach ($tasks as $task) {
            
                // если совпадает дата, то анализируем что получили для установки параметра статуса days[]['stTask']
                if ($this->days[$i]['dtLink'] == $task['dttask']) {
                    
                    // сначала определяем меньше ли дата текущей (прошлые дни)
                    if ($this->days[$i]['dtLink'] < $curDate) {
                        
                        // прошедшее время
                        if ($task['status'] == 0) $this->days[$i]['stTask'] = 2;
                        else if ($this->days[$i]['stTask'] != 2) $this->days[$i]['stTask'] = 1;
                    }
                    else {
                        
                        // текущая или будущая дата
                        if ($task['status'] == 0) { 
                            
                            // задача еще не выполнена, определяем есть ли исполнители
                            
                            if (count($task['relemp']) > 0) {
                                if ($this->days[$i]['stTask'] != 4) $this->days[$i]['stTask'] = 3; // есть исполнители и еще нет задач без исполнителей
                            } 
                            else $this->days[$i]['stTask'] = 4; // нет исполнителей
                            
                        }
                        else if (($this->days[$i]['stTask'] != 3) && 
                                 ($this->days[$i]['stTask'] != 4)) $this->days[$i]['stTask'] = 1;
                        // задача выполнена, и нет открытых задач (с исполнителями или без)
                        
                    if (($task['status'] == 0) &&($this->days[$i]['stTask'] != 4) && (count($task['reletm']) == 0)) $this->days[$i]['stTask'] = 4;
                        
                    }
                    
                }
            }            
        }

    }

    // при создании календаря формирование списка дней
    public function __construct ($indate = 0) {
        
        parent::__construct();
        
        if ($indate == 0) $indate = date("Y-m-d");
        
        $this->date = $indate;
        $clDay   = $this->date[8].$this->date[9];                               // число
        $clMonth = $this->date[5].$this->date[6];                               // месяц
        $clYear  = $this->date[0].$this->date[1].$this->date[2].$this->date[3]; // год
        
        $ltDate =  mktime(12, 0, 0, $clMonth, $clDay, $clYear);                 // метка времени текущего дня
        $ltFirst = mktime(12, 0, 0, $clMonth, 1, $clYear);                      // метка времени первого дня месяца
        $ltLast = mktime(12, 0, 0, $clMonth, date("t", $ltDate), $clYear);      // метка времени последнего дня месяца
        $wfirst = date("N", $ltFirst);                                          // день недели первого дня месяца
//        if ($wfirst == 0) $wfirst = 7;
//        if ($wfirst == 1) $wfirst = $wfirst + 8;                              // сдвигаем первый день на неделю
//        if ($wfirst == 2) $wfirst = $wfirst + 9;                              //   если он приходится на понедельник, вторник
        if ($wfirst < 4) $wfirst = $wfirst + 7;                                 //     или среду
        $start =  $ltFirst - (86400 * ($wfirst - 1));                           // метка времени первого выводимого дня
        // считается как первый день месяца минус смещение = день недели * кол-во секунд в сутках
        
        // формируем дату начала и дату окончания в запросе к списку задач
        $this->start =  date("Y-m-d", $start);
        $this->finish = date("Y-m-d", $start + (86400 * 41));  
        
        $curDate = date("Y-m-d");  // текущая дата для формирования списка "2017-12-25"
        $curDay = date("j");        // номер текущего дня для формирования списка "4"

        for ($i = 0; $i < 42; $i++) {
            $t = $start + ($i * 86400);
            $this->days[$i]['showDay'] = date("j", $t);          // число месяца для отображения "25"
            $this->days[$i]['dtLink'] = date("Y-m-d", $t);       // дата дня формирования для ссылки "2017-02-16"
            if ($t < $ltFirst || $t > $ltLast)
                $this->days[$i]['curMonth'] = false;
            else $this->days[$i]['curMonth'] = true;             // относится ли день к текущему месяцу
            
            // устанавливаем статус дня из календаря года
            // 0 - рабочий день, 1 - выходной, 2 - предпразничный, 3 - праздничный

            
            $this->days[$i]['HDay'] = Year::getHDay($t); // HollyDay
            
            // для текущего месяца нужно отметить рамку совпадения дня и выделение фоном сегодняшней даты
            // считаем по умолчанию несовпадение всего
            $this->days[$i]['equal'] = false;
            $this->days[$i]['today'] = false;
            
            if ($this->days[$i]['curMonth']) {
                
                // проверяем совпадение числа месяца
                if ($this->days[$i]['showDay'] == $curDay) {
                    $this->days[$i]['equal'] = true;
                    
                    // и в этом случае проверяем точное совпадение даты
                    if ($this->days[$i]['dtLink'] == $curDate)
                        $this->days[$i]['today'] = true;
                }                
            }
        }
        
        //$this->setTasks(Task::getTaskForCalendar($this->start, $this->finish));
    }

}