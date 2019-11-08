<?php use yii\bootstrap\Html; ?>
<?php // если задача выполнена, то нужно только отобразить список работ ?>
<?php // и сделать его недоступными для редактирования ?>
<?php $cost = 0; ?>
<?php $No = 1; ?>
<input id="filter" name="filter" type="text" class="form-control" onkeyup="render_worklist();">

<table class="table table-striped table-condensed">
    <thead>
    <th>
        №
    </th>
    <th>
        #
    </th>
    <th>
        Вид работ
    </th>    
    <th>
        Кол-во
    </th>
    <th>
        ЕИС
    </th>
    </thead>
    <tbody id="selwork">
    
    </tbody>
</table>
<table class="table table-striped table-condensed">
    <tbody id="allwork">
<?php $cft = 1; ?>
<?php foreach ($model->typework as $item) { ?>
<?php $key = array_search($item['twid'], $model->worklist['work']); ?>
<?php // если задача выполнена, то выводим только отмеченные пункты ?>
<?php
if ($model->tunit->status == 1) {
    if ($key !== false) { ?>
<tr class="with-filter" >
    <td><!-- № пункта -->
        <?= $No ?><?= (Yii::$app->user->identity->role[0] == 'w') ? ' ['.$item['did'].']' : '' ?>
        <input type="hidden" name="cost[<?= $item['twid'] ?>]" value="<?= $item['cost'] ?>" >
    </td>
    <td><!-- чекбокс -->
            <input type="hidden" name="twid[<?= $item['twid'] ?>]" value="<?= $item['twid'] ?>" >  
    </td>
    <td><!-- заголовок -->
    <?= $item['title'] ?>
    <?php if (($model->worklist['info'][$key] != '') && (Yii::$app->user->identity->role[2] != 'x')) { ?><br><small><?= $model->worklist['info'][$key] ?></small><?php } ?>
    <input type="hidden" name="info[<?= $item['twid'] ?>]" value="<?= $model->worklist['info'][$key] ?>">
    </td>
    <td><!-- количество -->
        <?= $model->worklist['nrep'][$key] ?>
        <input type="hidden" title="<?= $item['info'] ?>" name="nrep[<?= $item['twid'] ?>]" value="<?= $model->worklist['nrep'][$key] ?>">
    </td>
    <td><!-- стоимость -->
        
        <input type="hidden" title="<?= $item['cost'] ?>" name="wcost[<?= $item['twid'] ?>]" value="<?= $model->worklist['cost'][$key] ?>"> 
        <?php 
            if (Yii::$app->user->identity->role[2] != 'x') echo $model->worklist['cost'][$key];
        ?>
 
    </td>
    <?php if ($model->worklist['status'][$key] == 1) $cost = $cost + $model->worklist['cost'][$key] * $model->worklist['nrep'][$key]; ?>
    <?php if ($model->worklist['status'][$key] == 9) $cft = $cft * $model->worklist['cost'][$key]; ?>
    <?php $No++; ?>
</tr> 
<?php
    }
}
else { ?>
<tr class="with-filter" data-id="<?= $item['cid'] ?>" data-position="<?= $No ?>" data-move="0" >
    <td><!-- № пункта -->
        <?= $No ?><?= ((Yii::$app->user->identity->role[0] == 'w') && (Yii::$app->user->identity->did != $item['did'])) ? ' ['.$item['did'].']' : '' ?>
        <input type="hidden" name="cost[<?= $item['twid'] ?>]" value="<?= $item['cost'] ?>" >
    </td>
    <?php if ($key === false) {
        
        // этот элемент не выбран
        ?>
        <td><!-- чекбокс -->
            <input type="checkbox" name="twid[<?= $item['twid'] ?>]" value="<?= $item['twid'] ?>" id="check<?= $No ?>" onchange="renew_table();">
        </td>    
        <td><!-- заголовок -->
            <label for="check<?= $No ?>" title="<?= $item['detail'] ?>"><?= $item['title'] ?></label><br>
            <?php 
                if (Yii::$app->user->identity->role[2] == 'w') { ?><input type="text" name="info[<?= $item['twid'] ?>]" value=""> <?php }
                else { ?><input type="hidden" name="info[<?= $item['twid'] ?>]" value=""> <?php }
            ?>
        </td>
        <td><!-- количество -->
            <input type="number" title="<?= $item['info'] ?>" name="nrep[<?= $item['twid'] ?>]" min="1" max="99" size="2" maxlength="2" value="1">
        </td>
        <td><!-- стоимость -->
            
            <?php 
                if (Yii::$app->user->identity->role[2] == 'w') { ?>
                <input type="number" title="<?= $item['cost'] ?>" name="wcost[<?= $item['twid'] ?>]" min="-9" max="99" step="0.01" size="2" maxlength="2" value="<?= $item['cost'] ?>"><?php }
                else { ?>
                <input type="hidden" title="<?= $item['cost'] ?>" name="wcost[<?= $item['twid'] ?>]" value="<?= $item['cost'] ?>">
                <?php
                }
                ?>
        </td>
        <!--  -->
        <?= ''//Html::hiddenInput('wid['.$item['wid'].']', $item['wid']) ?>
        <?= ''//Html::hiddenInput('twid['.$item['twid'].']', $item['twid']) ?>
        <?= ''//Html::hiddenInput('info['.$item['twid'].']', $model->worklist['info'][$key]) ?>
        <?= ''//Html::hiddenInput('wcost['.$item['twid'].']', $item['cost']) ?>
        <?php
    }
    else {
        
        // элемент выбран
        ?>
        <td><!-- чекбокс -->
            <input type="checkbox" name="twid[<?= $item['twid'] ?>]" value="<?= $item['twid'] ?>" id="check<?= $No ?>" checked onchange="renew_table();" >  
        </td>
        <td class="col-md-6"><!-- заголовок -->
            <label for="check<?= $No ?>" title="<?= $item['detail'] ?>"><?= $item['title'] ?></label><br>
            <?php 
                if (Yii::$app->user->identity->role[2] == 'w') { ?><input type="text" name="info[<?= $item['twid'] ?>]" value="<?= $model->worklist['info'][$key] ?>"><?php }
                else { ?>
                <input type="hidden" name="info[<?= $item['twid'] ?>]" value="<?= $model->worklist['info'][$key] ?>">
                <?php
                }
                ?>
        </td>
        <td><!-- количество -->
            <input type="number" title="<?= $item['info'] ?>" name="nrep[<?= $item['twid'] ?>]" min="1" max="99" size="2" maxlength="2" value="<?= $model->worklist['nrep'][$key] ?>">
        </td>
        <td><!-- стоимость -->
            <?php 
                if (Yii::$app->user->identity->role[2] == 'w') { ?><input type="number" title="<?= $item['cost'] ?>" name="wcost[<?= $item['twid'] ?>]" min="-9" max="99" step="0.01" size="3" maxlength="3"  value="<?= $model->worklist['cost'][$key] ?>"> <?php }
                else { ?>
                <input type="hidden" title="<?= $item['cost'] ?>" name="wcost[<?= $item['twid'] ?>]" value="<?= $model->worklist['cost'][$key] ?>">    
                <?php
                }
                ?>
        </td>
        <?php if ($model->worklist['status'][$key] == 1) $cost = $cost + $model->worklist['cost'][$key] * $model->worklist['nrep'][$key]; ?>
        <?php if ($model->worklist['status'][$key] == 9) $cft = $cft * $model->worklist['cost'][$key]; ?>
        <?php
    }
    ?>
    
    <?php $No++; ?>
</tr>    
<?php
}
?>    

<?php } ?>        
    </tbody>
</table>
<?php if (Yii::$app->user->identity->role[2] == 'w') { ?>
<?php $cost = $cost * $cft; ?>
<?= 'Всего: '.$cost ?>
<?php if ($model->tunit->total != 0) echo ' / '.number_format(($cost / $model->tunit->total), 2)/*((floor($cost / $model->tunit->total / 0.01)) * 0.01) */; ?>
<?php } ?>
<script>
function flt_proccess(value) {
//    value = value.toLowerCase();
//    var elem = document.getElementsByClassName('with-filter');
//    for (i = 0; i < elem.length; i++) {
//        if (value == '') elem[i].style.display = 'table-row';
//        else {
//            var items = elem[i].querySelectorAll('TD');
//            var msg = items[2].textContent.toLowerCase();
//            if ((msg.indexOf(value) != -1) || (items[1].querySelector('INPUT').checked)) elem[i].style.display = 'inline-block';
//            else elem[i].style.display = 'none';
//        }
//        
//    }
    render_worklist();
}
</script>