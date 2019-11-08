<?php use yii\bootstrap\Html; ?>
<?php $model->tunit->total = 0; ?>
<?php foreach ($model->emplist as $item): ?>
<?php if ($item['select']) $model->tunit->total++; ?>
<p class="employe-hidden" style="margin: 0px; "<?php if (!$item['select'] && $item['hide']) echo ' hidden="true" '; ?>>
<?= Html::checkbox('eid['.$item['eid'].']', $item['select'], ['label' => $item['fio_short'].(($item['did'] != Yii::$app->user->identity->did) ? ' (?)' : ''), 'value' => $item['eid']]) ?>    
<?php if ($item['info'] != '') { ?> <span class="<?= $item['class'] ?>"><?= $item['info'] ?></span> <?php } ?> 
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
<?php if (Yii::$app->user->id == 100) { ?> <pre><?= var_dump($model->emplist) ?></pre><?php } ?>