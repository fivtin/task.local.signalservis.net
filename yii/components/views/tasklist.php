<?php if (!empty($model->tlist)) : ?>
<?php $dttask = ''; ?>
<?php $did = 0; ?>
<?php foreach ($model->tlist as $task) : ?>
        <?php
            $showHR = false;
            if ((Yii::$app->user->identity->uid == 1) && ($task['did'] != 1) && ($task['did'] != $did)) {
                $did = $task['did'];
                echo '<hr><hr><hr>';
                //$showHR = true;
            }    
        
            if ($dttask != $task['dttask']) {
                
                $dttask = $task['dttask'];
                echo '<center><h4>'.ShowDate($task['dttask']).'</h4></center>';
            }
        ?>
<a href="/task/<?= $task['tid'] ?>" title="<?= $task['user']['name'].' '.date('d.m.Y', strtotime($task['dtcreate'])) ?>" >
            <div class="panel panel-<?= getTaskStatus($task) ?>">
            <div class="panel-heading">
              <!-- Номер и дата задачи и её статус цветом -->
              <h3 class="panel-title"><!-- ДАННЫЕ --><?= ShowDate($task['dttask'], true) ?><span style="float:right; ">[<?= $task['tid'] ?>]</span></h3>
            </div>
            <div class="panel-body">
                <!-- описание задачи - ДАННЫЕ -->
                <?= $task['title'] ?><br>
                <div class="show-time" style="float: left; ">
                <small>
                    <?php // сюда вносим список времени из задачи
                        foreach ($task['whour'] as $whour) {
                        ?>
                        <span><?= $whour['htext'] ?></span>
                        <br>
                        <?php
                        }
                    ?>
            </small>
            </div>    
            <div style="float: right; "> 
            <small>
                <?php // сюда вносим список сотрудников из задачи
                    foreach ($task['employe'] as $employe) {
                        echo $employe['fio_short'].' <br>';
                    }
                ?>
              </small>
            </div>
            </div>
        </div>
        </a>
        <?= ($showHR) ? '<hr>' : '' ?>
<?php endforeach; ?>
<?php else : ?>
<div class="alert alert-info alert-dismissible" role="alert">
<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<?= !$model->select ? 'Нет активных задач.' : 'Нет задач на '. ShowDate($model->date).'.' ?>
</div>
<?php endif; ?>
<script>
    function hide_8hour() {
        var elem = document.getElementsByClassName('show-time');
        for (i = 0; i < elem.length; i++) {
            
            var span = elem[i].getElementsByTagName("span");
            if (span.length == 8) {
                
                elem[i].innerHTML = '<small>рабочий день</small>';
//                for (y = 0; y < span.length; y++) {
//                    
//                    span[y].style.display = 'none';
//                    
//                }
            }
        }
    }
    
hide_8hour();

</script>