<style>
a {
        text-decoration: none;
    }
    
    .clWork {
        color: black;        
    }
    
    .clHolly {
        color: red;
    }
    
    .clAnother {
        color: gray;
        font-size: small;
    }
    
    .clPreHD {
        color: green;
    }
    
    .clCelebration {
        text-decoration: underline;
    }
    
    .tskDone {
        background-image: url(/images/cGreen.png);
    }
    
    .tskActive {
        background-image: url(/images/cBlue.png);
    }
    
    .tskWarning {
        background-image: url(/images/cOrange.png);
    }
    
    .tskOverdue {
        background-image: url(/images/cRed.png);
    }
    
    .dayToday {
        background-color: #bbddff;
    }
    
    .dayEqually {
        border: 1px solid gray;
        background-position: -1px -1px;
    }
    
    .clItem {
        float: left;
        width: 32px;
        height: 32px;
    }
    
    .clTitle {
        float: left;
        width: 32px;
        height: 40px;
        text-decoration: underline;
    }
    
    .selDate {
        font-weight: bold;
        font-size: large;
    }
    
</style>
<?php
// !!! ЗДЕСЬ НУЖНА СЕКЦИЯ ДЛЯ ОПРЕДЕЛЕНИЯ РЕЖИМА ОТОБРАЖЕНИЯ

// режим вывода задач wT = true
if (isset($model->params['withTasks'])) $wT = true; else $wT = false;

$styleT = array('1'=> ' tskDone', '2' => ' tskOverdue', '3' => ' tskActive', '4' => ' tskWarning');
?>
<center><h4><?= ShowCalDate($model->date, $model->select) ?></h4>
    <div style="line-height: 32px; width: 248px; border: solid 1px #f3f3f3; padding: 8px; height: 248px; text-align: center; ">
        <div class="clTitle" >
            ПН
        </div>
        <div class="clTitle" >
            ВТ
        </div>
        <div class="clTitle" >
            СР
        </div>
        <div class="clTitle" >
            ЧТ
        </div>
        <div class="clTitle" >
            ПТ
        </div>
        <div class="clTitle clHolly" >
            СБ
        </div>
        <div class="clTitle clHolly" >
            ВС
        </div>      
        <?php for ($i = 0; $i < 42; $i++) {
            $class = '';
        if (!$model->calendar['days'][$i]['curMonth']) $class = $class.' clAnother';  // нужно отобразить серым шрифтом 
        else {
            if ($model->calendar['days'][$i]['HDay'] == 1) $class = 'clHolly'; // нужно отобразить красным шрифтом
            if ($model->calendar['days'][$i]['HDay'] == 2) $class = 'clPreHD'; // нужно отобразить зеленым шрифтом
            if ($model->calendar['days'][$i]['HDay'] == 3) $class = 'clHolly clCelebration'; // нужно отобразить красным шрифтом
        } 
        ?>
        <div class="clItem
            <?php if ($model->calendar['days'][$i]['today']) echo ' dayToday'; 
                  if ($model->calendar['days'][$i]['equal']) echo ' dayEqually';
                  
                  // если установлен режим вывода задач, то выводим стиль для этого
                  if (($wT) && ($model->params['withTasks'][$i] !=  0)) echo $styleT[$model->params['withTasks'][$i]];
                  if (($model->select) && ($model->date == $model->calendar['days'][$i]['dtLink'])) echo ' selDate';
                  
            ?>" >
        <a class="<?= $class ?>" href="/<?= $model->calendar['days'][$i]['dtLink'] ?>"><?= $model->calendar['days'][$i]['showDay']; ?></a>
        </div>

        <?php } ?>
    </div>
</center>
<div style="clear: both; ">&nbsp;</div>