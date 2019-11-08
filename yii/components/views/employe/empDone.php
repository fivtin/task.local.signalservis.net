<?php $model->tunit->total = 0; ?>
<?php foreach ($model->emplist as $item): ?>
<?php if ($item['select']) $model->tunit->total++; ?>
<p style="margin-bottom: 4px; ">
<?= $item['fio_short'] ?>
<?= ($item['did'] != Yii::$app->user->identity->did) ? ' (?)' : '' ?>    
<?php if ($item['info'] != '') { ?> <span class="<?= $item['class'] ?>"><?= $item['info'] ?></span> <?php } ?> 
</p>
<?php endforeach; ?>

<?php if (Yii::$app->user->id == 100) { ?> <pre><?= var_dump($model->emplist) ?></pre><?php } ?>