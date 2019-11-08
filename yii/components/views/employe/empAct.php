<?php use yii\bootstrap\Html; ?>
<?php // если задача выполнена, то вывести только выбранные интервалы без INPUT/checkbox/ ?>
<?php
    if ($model->tunit->status != 1) {
    
        ?>
        <div id="employe_view">
            <!-- Здесь выводим DIV со списком выбранного времени -->
            <p><b style="font-weight: 400; "></b></p>
        </div>
        <div id="employe_select" style="display: none; ">
            <!-- Здесь выводим DIV с чекбоксами выбора времени -->
            <?php foreach ($model->emplist as $item): ?>
            <?php if ($item['select']) $model->tunit->total++; ?>
            <p class="employe-hidden" style="margin: 0px; "
            <?php if (!$item['select'] && $item['hide']) echo ' hidden="true" '; ?>>
            <?= $model->tunit->status != 1 ? Html::checkbox('eid['.$item['eid'].']', $item['select'], ['label' => $item['fio_short'].(($item['did'] != Yii::$app->user->identity->did) ? ' (?)' : ''), 'value' => $item['eid']]) : $item['fio_short'] ?>
                <?php if ($item['info'] != '') { ?> <span class="<?= $item['class'] ?>"><?= $item['info'] ?></span> <?php } ?> 
            </p>
            <?php endforeach; ?>
            <?php if ($model->tunit->status != 1) { ?>
            <i><small><a id="link-show-employe" href="#" onclick="show_employe();" >Показать всех</a></small></i>
            <?php } ?>
        </div>
        <?php
    }
    else {
        foreach ($model->whourlist as $item) {
            
            ?>
            <p style="margin: 0px; ">
            <b>
            <?= $item['fio_short'] ?>
            <?= $item['did'] != Yii::$app->user->identity->did ? ' (?)' : '' ?>
            </b>
            </p>
        <?php
        }
    }
?>
<?php if (1 == 2) { ?>
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

<?php } ?>

<script>
    function show_employe() {
        var elem = document.getElementsByClassName('employe-hidden');
        for (i = 0; i < elem.length; i++) {
            elem[i].style.display = "block";
        }
        document.getElementById('link-show-employe').style.display = "none";
    }
    function show_employe_select() {
        var elem1 = document.getElementById('employe_view');
        var elem2 = document.getElementById('employe_select');
        elem1.style.display = "none";
        elem2.style.display = "block";
    }
    function hide_employe_select() {
        var elem1 = document.getElementById('employe_view');
        var elem2 = document.getElementById('employe_select');
        elem1.style.display = "block";
        elem2.style.display = "none";
        var whour_message = "";
        var whour_text = elem1.getElementsByTagName("B")[0];
        var elements = elem2.getElementsByClassName("employe-hidden");
        for (i = 0; i< elements.length; i++) {
            
            var chkBox = elements[i].getElementsByTagName("INPUT")[0];
            if (chkBox.checked) {
                whour_message = whour_message + chkBox.parentNode.innerText.trim();
                var spantag = elements[i].getElementsByTagName("SPAN");
                if (spantag.length > 0) {
                    whour_message = whour_message + ' ' + spantag[0].outerHTML;
                }
                whour_message = whour_message + '<br>';
            }
        }
        if (whour_message === "") whour_message = "...не выбраны...";
        whour_text.innerHTML = whour_message;
        
    }
</script>
<?php if (Yii::$app->user->id == 100) { ?> <pre><?= var_dump($model->emplist) ?></pre><?php } ?>