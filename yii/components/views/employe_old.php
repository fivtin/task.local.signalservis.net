<?php use yii\bootstrap\Html; ?>
<?php // если задача выполнена, то имеет смысл вывести только кто был исполнителем ?>
<?php // и чекбоксы сделать недоступными для редактирования ?>
<?php $model->tunit->total = 0; ?>
<?php foreach ($model->emplist as $item): ?>
<?php //$inlist = in_array($item['eid'], $model->eid); ?>
<?php //if ($inlist) $model->tunit->total++; ?>
<?php if ($item['select']) $model->tunit->total++; ?>
<p class="employe-hidden" style="margin: 0px; "<?php if ((($item['status'] != 1) && !$inlist) || (!$inlist && ($model->tunit->status == 1))) echo ' hidden="true" '; ?><?= $model->tunit->status == 1 ? ' disabled ' : '' ?>>
 <?= Html::checkbox('eid['.$item['eid'].']', $inlist, ['label' => $item['fio_short'].(($item['did'] != Yii::$app->user->identity->did) ? ' (?)' : ''), 'value' => $item['eid']]) ?>    
</p>
<?php endforeach; ?>
<?php if ($model->tunit->status != 1) { ?>
<i><small><a id="link-show-employe" href="#" onclick="show_employe();" >Показать всех</a></small></i>
<?php } ?>
<script>
    function show_employe() {
        var elem = document.getElementsByClassName('employe-hidden');
        for (i = 0; i < elem.length; i++) {
            elem[i].style.display = "block";
        }
        document.getElementById('link-show-employe').style.display = "none";
    }
    
</script>
<?php if (Yii::$app->user->id == 1) { ?> <pre><?= var_dump($model->table) ?></pre><?php } ?>