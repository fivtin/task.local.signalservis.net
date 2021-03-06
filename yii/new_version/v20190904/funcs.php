<?php

$Month_r = array( 
	"1" => "Январь", 
	"2" => "Февраль", 
	"3" => "Март", 
	"4" => "Апрель", 
	"5" => "Май", 
	"6" => "Июнь", 
	"7" => "Июль", 
	"8" => "Август", 
	"9" => "Сентябрь", 
	"10" => "Октябрь", 
	"11" => "Ноябрь", 
	"12" => "Декабрь");
$Month_end = array( 
	"1" => "январ", 
	"2" => "феврал", 
	"3" => "март", 
	"4" => "апрел", 
	"5" => "ма", 
	"6" => "июн", 
	"7" => "июл", 
	"8" => "август", 
	"9" => "сентябр", 
	"10" => "октябр", 
	"11" => "ноябр", 
	"12" => "декабр");	
$DayOfWeek = array(
	"1" => "Понедельник",
	"2" => "Вторник",
	"3" => "Среда",
	"4" => "Четверг",
	"5" => "Пятница",
	"6" => "Суббота",
	"7" => "Воскресенье");
$DayStatus = array(
	"0" => "рабочий день",
	"1" => "выходной день",
	"2" => "предпразничный день");


function getStatus($status, $date, $rel, $class = false) {
// возвращает статус задачи
// status - выполнена/не выполнена
// date - дата задачи
// rel - есть ли связь с исполнителями, временем (и работами)
// class - вернуть название класса или просто значение?
    
    $PnTaskStyle = array(
         0 => "default", // не исп.
         1 => "success", // выполнена
         2 => "danger",  // просрочена
         3 => "primary", // запланирована
         4 => "warning", // запланирована без исполнителя
         5 => "info");   // не исп.

    $st = 0;
    $today = date("Y-m-d");
    
    if ($date < $today) {
        
        // прошлые задачи
        if ($status) {
            
            // выполнена
            if ($st != 2) $st = 1;
        }
        else {
            
            // не выполнена
            $st = 2;
        }
    }
    else {
        
        // текущие задачи
        if ($status) {
            
            // выполнена
            $st = 1;
        }
        else {
            
            // не выполнена
            if ($rel) {
                
                // есть исполнители
                if ($st != 4) $st = 3;
            }
            else {
                
                // нет исполнителей
                $st = 4;
                
            }            
        }
    }
    
    if ($class)
        return $PnTaskStyle[$st];
    else return $st;
}


function getTaskStatus ($task) {
    
    if ((count($task['employe']) > 0) && (count($task['whour']) > 0) && (count($task['works']) >0)) $e = true; else $e = false;
        
    return getStatus($task['status'], $task['dttask'], $e, true);   
}

// генерирует строку случайных символов
function generate_str ($lenght, $flag = 0) {
// длинна строки задается перемен. $lenght
    
$result = '';										
    switch ($flag) { 
        case 0:  $patstr = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz01234567890'; break;      // соль
        case 1:  $patstr = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890'; break;                                //
        case 2:  $patstr = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz01234567890.:/'; break;   //
        case 3:  $patstr = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; break;                 // для начала строки с буквы
    }

    for ($i = 1; $i < $lenght + 1; $i++)
        $result = $result.substr($patstr, rand(0, iconv_strlen($patstr) - 1), 1);
    
    return $result;  
}    

function EmployeInArray($value, $array) {
    
    $result = false;
    foreach ($array as $item) {
        if ($value == $item['eid']) $result = true;
    }
    return $result;
}

function WhourInArray($value, $array) {
    
    $result = false;
    foreach ($array as $item) {
        if ($value == $item['hid']) $result = true;
    }
    return $result;
}

function getFullDate($inDate = 0, $withDoW = 0) {
        // inDate - Input Date, withDoW - With Day of Week
        
}



function debug0($arr) {
        echo '<pre>'. print_r($arr, true).'</pre>';
}
    
/// !!! УДАЛИТЬ ПОСЛЕ ИЗМЕНЕНИЯ ЛОГИКИ ВЫВОДА ИНФОРМАЦИИ О ЗАДАЧЕ
function InEmployeList ($value, $array) {
    $result = false;
    foreach ($array as $item) {
        if ($item['eid'] == $value) $result = true;
    }
    return $result;
}

/// !!! УДАЛИТЬ ПОСЛЕ ИЗМЕНЕНИЯ ЛОГИКИ ВЫВОДА ИНФОРМАЦИИ О ЗАДАЧЕ
function InHourList ($value, $array) {
    $result = false;
    foreach ($array as $item) {
        if ($item['hid'] == $value) $result = true;
    }
    return $result;
}

// функция вычисления метки времени по значению времени
/// НУЖНА ЛИ ДАННАЯ ФУНКЦИЯ ???

function makeTL($inTime = 0) {
// makeTL - Make Time Label,  inTime - Input Time - время в Unix-формате
    
    if ($InTime == 0) $InTime = time();
    $clDay = date("j", $InTime);
    $clMon = date("n", $InTime);
    $clYr =  date("Y", $InTime);
    return mktime(12, 0, 0, $clMon, $clDay, $clYr);
}

// возращает дату вида "день_недели(true) дд месяца гггг"
function ShowDate ($value, $withDay = false) {
    if ($value == '') return '';
    $month = array(
        '01' => 'января', '02' => 'февраля', '03' => 'марта', '04' => 'апреля',
        '05' => 'мая', '06' => 'июня', '07' => 'июля', '08' => 'августа',
        '09' => 'сентября', '10' => 'октября', '11' => 'ноября', '12' => 'декабря'
    );
    $weekday = array(
        1 => 'Понедельник', 2 => 'Вторник', 3 => 'Среда', 4 => 'Четверг',
        5 => 'Пятница', 6 => 'Суббота', 7 => 'Воскресенье'
    );
    $m = $value[5].$value[6];    
    $result = $value[8].$value[9].' '.$month[$m].' '.$value[0].$value[1].$value[2].$value[3].' года';
    
    if ($withDay) {
        $w = date("N", strtotime($value));
        $result = $weekday[$w].', '.$result;
    }
    return $result;
}
// возращает дату вида "дд.мм.гггг" - для отчетов
function ShowDigiDate($value) {
    if ($value == '') return '';
    else return $value[8].$value[9].'.'.$value[5].$value[6].'.'.$value[0].$value[1].$value[2].$value[3];
    
}

// возращает дату "МЕСЯЦ гггг года"
function ShowMonthYear($value) {
    if ($value == '') return '';
    $only = array(
        '01' => 'ЯНВАРЬ', '02' => 'ФЕВРАЛЬ', '03' => 'МАРТ', '04' => 'АПРЕЛЬ',
        '05' => 'МАЙ', '06' => 'ИЮНЬ', '07' => 'ИЮЛЬ', '08' => 'АВГУСТ',
        '09' => 'СЕНТЯБРЬ', '10' => 'ОКТЯБРЬ', '11' => 'НОЯБРЬ', '12' => 'ДЕКАБРЬ'
    );
    $m = $value[5].$value[6];
    $m = $only[$m];
    $result = $m.' '.$value[0].$value[1].$value[2].$value[3].' года';
    return $result;
}
// возращает дату вида "дд месяца гггг года" или "Месяц гггг года" (false) - для календаря
function ShowCalDate ($value, $select = false) {
    if ($value == '') return '';
    $only = array(
        '01' => 'Январь', '02' => 'Февраль', '03' => 'Март', '04' => 'Апрель',
        '05' => 'Май', '06' => 'Июнь', '07' => 'Июль', '08' => 'Август',
        '09' => 'Сентябрь', '10' => 'Октябрь', '11' => 'Ноябрь', '12' => 'Декабрь'
    );
    $full = array(
        '01' => 'января', '02' => 'февраля', '03' => 'марта', '04' => 'апреля',
        '05' => 'мая', '06' => 'июня', '07' => 'июля', '08' => 'августа',
        '09' => 'сентября', '10' => 'октября', '11' => 'ноября', '12' => 'декабря'
    );
    
    $m = $value[5].$value[6];
    if ($select) {
        $m = $full[$m];
        $result = $value[8].$value[9].' '.$m.' '.$value[0].$value[1].$value[2].$value[3].' года';
    }
    else {
        $m = $only[$m];
        $result = $m.' '.$value[0].$value[1].$value[2].$value[3].' года';
    }
    
    return $result;
}

function DateReport($value) {
    if ($value == '') return '';
    return $value[8].$value[9].'.'.$value[5].$value[6].'.'.$value[0].$value[1].$value[2].$value[3];
}

function getPnStyle ($status, $date) {
    if ($status == 1) return 'primary';
    else {
        if ($date < time()) return 'danger';
        else {
            return 'success';
        }
    }
}

function validDate($value) {
    
    $d = $value[5].$value[6];
    $m = $value[8].$value[9];
    $y = $value[0].$value[1].$value[2].$value[3];
    
    return checkdate($m, $d, $y);
    
}

function sumArray($array, $field) {
    
    $result = 0;
    foreach ($array as $item) {
        $result = $result + $item[$field];
    }
    return $result;
}

function findArrayEid($eid, $array) {
        
        $result = false;
        foreach ($array as $item) {
            if ($item['eid'] == $eid) $result = $item;//['timesheet'];
        }
    return $result;
}

function findShiftFromDay($day, $array) {
    $result = '';
        foreach ($array as $item) {
            if ($item['tsdate'] == $day) $result = $item['shift'];
        }
    return $result;
}

function findCommentFromDay($day, $array) {
    $result = '';
        foreach ($array as $item) {
            if ($item['cmdate'] == $day) $result = $item['comment'];
        }
    return $result;
}

//функция определения расстояния между двумя точками на php

function distance($lat1,$lng1,$lat2,$lng2) //(x1,y1,x2,y2)

     { 

        $lat1=deg2rad($lat1); 

        $lng1=deg2rad($lng1); 

        $lat2=deg2rad($lat2); 

        $lng2=deg2rad($lng2); 

        $delta_lat=($lat2 - $lat1); 

        $delta_lng=($lng2 - $lng1); 

        return round( 6378137 * acos( cos( $lat1 ) * cos( $lat2 ) * cos( $lng1 - $lng2 ) + sin( $lat1 ) * sin( $lat2 ) ) ); 

     }
     
// функция вывода отладочной информации (только для uid=1)

function debug($value) {
    if (Yii::$app->user->id == 1) return '<pre>'.var_dump($value).'</pre>'; else return '';
}

// 

function getExcelIndex ($start = 65, $offset = 0) {
    $result = 'A';
        if (($start + $offset) < 91) return chr($start + $offset);
        else {
            //$rm = 91 - $offset - $start;
            return 'A'.chr($start + $offset - 26);
        }
    return $result;
}

// возращает название отдела по его коду, если указан параметр true, то возращается краткое название

function getDepTitle ($did, $short = false) {
    
    if ($short) {
        if ($did == 1) return 'Монтаж';
        if ($did == 2) return 'Линии';
        if ($did == 3) return '';
        if ($did == 4) return '';
        if ($did == 5) return 'ТехПод';
        if ($did == 6) return 'АбонОт';
    }
    else {
        if ($did == 1) return 'Монтажный';
        if ($did == 2) return 'Линейный';
        if ($did == 3) return '';
        if ($did == 4) return '';
        if ($did == 5) return 'ТехПоддержка';
        if ($did == 6) return 'Абонентский';
    }
}

// получаем значения result[start, finish] в формате - "YYYY-MM-DD" из даты в формате "YYYYMM"
function getDateFromSldate ($sldate = '') {
    
    $result = array();
    if ($sldate == '') $sldate = date('Ym');
    $month = $sldate[4].$sldate[5];
    $year = $sldate[0].$sldate[1].$sldate[2].$sldate[3];
    
    $result['start'] = $year.'-'.$month.'-01';
    $result['finish'] = date('Y-m-t', strtotime($result['start']));
    
    return $result;
}
