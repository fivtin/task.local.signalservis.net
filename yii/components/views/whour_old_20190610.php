<?php use yii\bootstrap\Html; ?>
<?php // если задача выполнена, то имеет смысл вывести только конкретные интервалы ?>
<?php // и чекбоксы сделать недоступными для редактирования ?>
<?php if (1 == 2) { foreach ($model->whourlist as $item): ?>
<p class="hour-hidden" style="margin: 0px; "
<?php if (!$item['select'] && $item['hide']) echo ' hidden="true" '; ?>>
<?= $model->tunit->status != 1 ? Html::checkbox('hid['.$item['hid'].']', $item['select'], ['label' => $item['htext'], 'value' => $item['hid']]) : $item['htext'] ?>
<?= $item['did'] != Yii::$app->user->identity->did ? ' (?)' : '' ?>
</p>
<?php endforeach; } ?>
<?php if ($model->tunit->status != 1) { ?>
<!-- <i><small><a id="link-show-hour" href="#" onclick="show_hour();" >Показать дополнительные</a></small></i> -->
<?php } ?>

<?php
    if (Yii::$app->user->id == 100) { 
        ?> <pre><?= ''/*var_dump($model->whourlist) */ ?></pre><?php 
    }
    if (Yii::$app->user->id == 1) { 

        ?>
        <div id="whour_view">
            <!-- Здесь выводим DIV со списком выбранного времени -->
            <p><b></b></p>
            <!-- <i><small><a id="link-show-hour" href="#" onclick="show_hour_select();" >Выбрать время</a></small></i> -->
        </div>
        <div id="whour_select" style="display: none; ">
            <!-- Здесь выводим DIV с чекбоксами выбора времени -->
            <?php foreach ($model->whourlist as $item): ?>
            <p class="hour-hidden" style="margin: 0px; "
            <?php if (!$item['select'] && $item['hide']) echo ' hidden="true" '; ?>>
            <?= $model->tunit->status != 1 ? Html::checkbox('hid['.$item['hid'].']', $item['select'], ['label' => $item['htext'], 'value' => $item['hid']]) : $item['htext'] ?>
            <?= $item['did'] != Yii::$app->user->identity->did ? ' (?)' : '' ?>
            </p>
            <?php endforeach; ?>
            <?php if ($model->tunit->status != 1) { ?>
            <i><small><a id="link-show-hour" href="#" onclick="show_hour();" >Показать дополнительные</a></small></i>
            <?php } ?>
        </div>
        <?php
    }
?>
<script>
    function show_hour() {
        var elem = document.getElementsByClassName('hour-hidden');
        for (i = 0; i < elem.length; i++) {
            elem[i].style.display = "block";
        }
        document.getElementById('link-show-hour').style.display = "none";
    }
    function show_hour_select() {
        var elem1 = document.getElementById('whour_view');
        var elem2 = document.getElementById('whour_select');
        elem1.style.display = "none";
        elem2.style.display = "block";
    }
    function hide_hour_select() {
        var elem1 = document.getElementById('whour_view');
        var elem2 = document.getElementById('whour_select');
        elem1.style.display = "block";
        elem2.style.display = "none";
        var whour_message = "";
        var whour_text = elem1.getElementsByTagName("B")[0];
        var elements = elem2.getElementsByClassName("hour-hidden");
        for (i = 0; i< elements.length; i++) {
            
            var chkBox = elements[i].getElementsByTagName("INPUT")[0];
            if (chkBox.checked) {
                whour_message = whour_message + chkBox.parentNode.innerText.trim() + '<br>';
                
            }
        }
        if (whour_message == "") whour_message = "...не выбрано...";
        whour_text.innerHTML = whour_message;
        
    }
    
</script>