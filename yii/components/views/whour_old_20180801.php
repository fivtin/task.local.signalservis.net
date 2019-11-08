<?php use yii\bootstrap\Html; ?>
<?php // если задача выполнена, то имеет смысл вывести только конкретные интервалы ?>
<?php // и чекбоксы сделать недоступными для редактирования ?>
<?php foreach ($model->whourlist as $item): ?>
<?php $inlist = in_array($item['hid'], $model->hid); ?>                  
<p class="hour-hidden" style="margin: 0px; "<?php if ((($item['status'] != 1) && !$inlist) || (!$inlist && ($model->tunit->status == 1))) echo ' hidden="true" '; ?>>
<?= Html::checkbox('hid['.$item['hid'].']', $inlist, ['label' => $item['htext'], 'value' => $item['hid']]) ?>
</p>
<?php endforeach; ?>
<?php if ($model->tunit->status != 1) { ?>
<i><small><a id="link-show-hour" href="#" onclick="show_hour();" >Показать дополнительные</a></small></i>
<?php } ?>
<script>
    function show_hour() {
        var elem = document.getElementsByClassName('hour-hidden');
        for (i = 0; i < elem.length; i++) {
            elem[i].style.display = "block";
        }
        document.getElementById('link-show-hour').style.display = "none";
    }
    
</script>
<?php if (Yii::$app->user->id == 1) { ?> <pre><?= var_dump($model->whour) ?></pre><?php } ?>