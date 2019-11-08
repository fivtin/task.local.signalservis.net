<?php if ($tunit->status == 1000) {
    ?>
    <pre>
    <?= var_dump($tunit) ?>
    </pre>
    <?php die;
    }
?>


<?php //use yii\bootstrap\Html; ?>
<?php use yii\helpers\Html; ?>
<?php use app\components\EmployeWidget; ?>
<?php use app\components\WhourWidget; ?>
<?php use app\components\WorklistWidget; ?>
<?php use yii\bootstrap\Modal; ?>
<style>
    label {
        font-weight: normal;
    }
    
    a:hover {
        text-decoration: none;
    }
</style>

<?php //$full[0]['dttask'] == '' ? $full[0]['dttask'] = '2017-09-01' : null ?>
<?php ''//($tunit->tid > 0) ? $title = "Задача № ".$tunit->tid : $title = "Новая задача"; ?>
<?php
if ($tunit->tid == 0) $title = "Статус";
else {
    if ($tunit->status == 1) $title = 'Статус';
    else $title = 'Статус';
}


?>
<?php $this->title = "Сигнал: ".$title; ?>
<?= Html::beginForm("/task/action") ?>
<?= Html::hiddenInput('tid', $tunit['tid']) ?>
<?= Html::hiddenInput('dttask11', $tunit['dttask']) ?>
<?= Html::hiddenInput('status', $tunit['status']) ?>
<?= Html::hiddenInput('uid', $tunit['uid']) ?>
<div class="container">
    
        
    
    
    <div class="row">
        <?= ''//$this->render('flashes.php') ?>
        <?php
            if ((rand(0, 10) > 5) && ($tunit->status == -1) && (Yii::$app->user->id == '1')) { //&& ((Yii::$app->user->id == '3')  || (Yii::$app->user->id == '1'))) {
                ?>
                    <div class="text-center alert alert-danger" role="alert">
                        <button class="close" data-dismiss="alert">×</button>
                        <div style="line-height: 1em;">
                        <p>ЧИТАТЬ ОБЯЗАТЕЛЬНО !!!</p>
                        <span>После любого знака препинания (кроме отдельных случаев, см. ниже) ставится пробел.</span><br>
                        <span>Перед знаками препинания пробелы не ставятся.</span><br>
                        <span>Тире выделяется в предложении пробелами с двух сторон.</span><br>
                        <span>Сокращение слова дефисом выполняется без пробелов (например: "кол-во").</span><br>
                        <span>В конце предложения ставится ТОЧКА.</span><br>
                        <hr style="margin: 4px 16px;">
                        <span>Пробел не ставится после кавычек и скобок в начале слова.</span><br>
                        <span>Пробел не ставится после закрывающих кавычек и скобок.</span><br>
                        <span>Пробел не ставится после точки при сокращении слова, если дальше идет знак препинания (например: "1 под., 2 этаж ").</span><br>
                        </div>
                    </div>
                <?php
            }
        ?>
        <div class="col-md-12">
            <?= $this->render('flashes.php') ?>
            <div class="panel panel-<?= $tunit->getStatus(true) ?>">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-md-6 text-left">
                            <h3 class="float-md-left panel-title"><?= $title.$tunit->getStatusText() ?></h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <h3 class="float-md-right panel-title"><?= ShowDate($tunit->dttask, true) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-10">
                            <span class="float-md-left">
                                <?= ''//Html::input('date', 'dttask', $tunit->dttask) ?>                            
                                <label class="control-label col-md-3">Дата задачи<input class="form-control" type="date" name="dttask" value="<?= $tunit->dttask ?>" required ></label>
                                <?= ''//Html::input('text', 'title', $tunit->title) ?>
                                <label class="control-label col-md-9">Описание задачи<input class="form-control" type="text" name="title" value="<?= $tunit->title ?>" size="96" maxlength="128" required placeholder="Введите описание задачи." minlength="6" ></label>
                                <?php //if (Yii::$app->user->identity->role[7] == 'w') { ?>
                                <label class="control-label col-md-12">Описание работы<textarea style="max-width: 888px; " class="form-control" name="descr" cols="128" rows="2" placeholder="Краткое описание работ"><?= $tunit->descr ?></textarea></label>
                                <?php //} ?>
                            </span>
                        </div>
                        <div class="col-md-2 text-right">
                            <table class="table-condensed">
                                <?php
                                if ($tunit->status != 1) { ?>
                                <tr>
                                    <td>Сохранить</td>
                                    <td>                                        
                                        <button type="submit" name="action" value="save" class="btn btn-success btn-sm glyphicon glyphicon-download-alt" title="Сохранить изменения">
                                        </button>    
                                        <?php // нельзя сохранить уже выполненную задачу <html средства> в которой не заполнены дата и описание...</html> ?>
                                    </td>                                    
                                </tr>
                                <?php } ?>
                                <tr>
                                    <td>Отменить</td>
                                    <td>
                                        <button type="submit" name="action" value="cancel" class="btn btn-danger btn-sm glyphicon glyphicon-remove" title="Отменить изменения" formnovalidate >
                                        </button>
                                    </td>
                                </tr>
                                <?php
                                if (($tunit->status == 1) && ($tunit->tid != 0)) { ?>
                                <tr>
                                    <td>Копировать</td>
                                    <td>                                       
                                        <button type="submit" name="action" value="copy" class="btn btn-primary btn-sm glyphicon glyphicon-duplicate" title="Копировать в новую">
                                        </button>
                                        <?php // нельзя копировать невыполненную задачу... и копировать новую задачу ?>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php
                                if (($tunit->status == 0) && ($tunit->tid != 0)) { ?>
                                <tr>
                                    <td>Выполнить</td>
                                    <td>
                                        <button type="submit" name="action" value="done" class="btn btn-primary btn-sm glyphicon glyphicon-wrench"  title="Отметить как выполненую" onclick=' return confirm("Отметить задачу как выполненную?")'>
                                        </button>
                                        <?php // нельзя выполнить задачу в которой не заполнены все поля... ?>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php
                                if (($tunit->status == 1) && (Yii::$app->user->identity->role[0] == 'w')) { ?>
                                <tr>
                                    <td>Восстановить</td>
                                    <td>
                                        <button type="submit" name="action" value="restore" class="btn btn-success btn-sm glyphicon glyphicon-cog" title="Восстановить задачу" onclick=' return confirm("Вы действительно хотите снять отметку о выполнении?")'>
                                        </button>
                                        <?php // нельзя восстановить невыполненную задачу... и не имея полного доступа к задачам... ?>
                                    </td>
                                </tr>
                                <?php } ?>
                                
                                <?php
                                if (($tunit->status != 1) && ($tunit->tid != 0)) { ?>
                                <tr>
                                    <td>Удалить</td>
                                    <td>
                                        <button type="submit" name="action" value="remove" class="btn btn-warning btn-sm glyphicon glyphicon-trash" title="Удалить задачу" onclick=' return confirm("Вы действительно хотите удалить задачу?")'>
                                        </button>
                                        <?php // нельзя удалить выполненную задачу... и удалить вновь создаваемую задачу... ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="panel panel-success">
              <div class="panel-heading">
                <h3 class="panel-title">Часы работы</h3>
              </div>
                <div class="panel-body" style="min-height: 120px; " onmouseleave=" hide_hour_select();" onmouseover="show_hour_select();">
              <?php echo WhourWidget::widget(['tunit' => $tunit]); ?>
              </div>
            </div>
            <div class="panel panel-warning">
              <div class="panel-heading">
                <h3 class="panel-title">Сотрудники</h3>
              </div>
              <div class="panel-body" style="min-height: 120px; " onmouseleave=" hide_employe_select();" onmouseover="show_employe_select();">
              <?php $emp_count = 0; ?>
              <?php echo EmployeWidget::widget(['tunit' => $tunit]); ?>
              </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="panel panel-danger">
              <div class="panel-heading">
                <h3 class="panel-title">Работы</h3>
              </div>
                <div class="panel-body" style="height: 600px; overflow: scroll;">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <?php if ($tunit->status == 0) { ?>
                            <p><a class="category" href="#" data-id="3" select= "0" onclick="show_category(this);">Монтаж</a></p>
                            <p><a class="category" href="#" data-id="8" select= "0" onclick="show_category(this);">Переделка</a></p>
                            <p><a class="category" href="#" data-id="2" select= "0" onclick="show_category(this);">Подключения</a></p>
                            <p><a class="category" href="#" data-id="4" select= "0" onclick="show_category(this);">Должники</a></p>
                            <p><a class="category" href="#" data-id="5" select= "0" onclick="show_category(this);">Наружка</a></p>
                            <p><a class="category" href="#" data-id="6" select= "0" onclick="show_category(this);">Частный сектор</a></p>
                            <p><a class="category" href="#" data-id="7" select= "0" onclick="show_category(this);">Ремонт</a></p>
                            <p><a class="category" href="#" data-id="9" select= "0" onclick="show_category(this);">Оптика</a></p>
                            <p><a class="category" href="#" data-id="1" select= "0" onclick="show_category(this);">Другое</a></p>
                            <?php } ?>
                        </div>
                        <div class="col-md-9">
                            <?php echo WorklistWidget::widget(['tunit' => $tunit]); ?>
                        </div>
                    </div>
              </div>
            </div>            
        </div>
    </div>
</div>
<script>
    hide_hour_select();
    hide_employe_select();
    render_worklist();
    renew_table();
    
    function show_category (elm) {
        //var all = document.getElementsByClassName("category");
        if (elm.getAttribute("select") == "0") elm.setAttribute("select", 1);
        else elm.setAttribute("select", 0);
//        for (i = 0; i < all.length; i++) {
//            if (all[i].getAttribute("data-id") == id) {
//                if (all[i].getAttribute("select") == 0) {
//                    all[i].style.fontWeight = "600";
//                    all[i].setAttribute("select", 1);
//                }
//                else {
//                    all[i].style.fontWeight = "400";
//                    all[i].setAttribute("select", 0);
//                }
//            }
//            //else {
//                //all[i].style.fontWeight = "400";
//                //all[i].setAttribute("select", 0);
//            //}
//        }
//        var tr = document.getElementsByClassName("with-filter");
//        for (t = 0; t < tr.length; t++) {
//            var atr = tr[t].getAttribute("data-id");
//            if ((atr != null) && (atr != "")) {
//                if (atr == id) {
//                    tr[t].style.display = "table-row";
//                }
//                else {
//                    tr[t].style.display = "none";
//                }
//            }
//        }
//        var flt = document.getElementById('filter');
//        //flt_proccess(flt.value);

        render_worklist();
    }
    function render_worklist() {
        
        var mass = new Array();
        var all = document.getElementsByClassName("category");
        var flt = document.getElementById("filter");
        var ftx = flt.value.trim().toLowerCase();
        
        var elem = document.getElementsByClassName('with-filter');
        
        for (i = 0; i < all.length; i++) {
            if (all[i].getAttribute("select") == "1") {
                mass.push(all[i].getAttribute("data-id"));
                all[i].style.fontWeight = "600";
            }
            else all[i].style.fontWeight = "400";
        }
        
        for (i = 0; i < elem.length; i++) {
            if (ftx == '') { // пустой фильтр, выводим по группам (если не выбрана ни одна группа, то не выводим ничего)
                
                var items = elem[i].querySelectorAll('TD');
                var msg = items[2].textContent.toLowerCase();
                if (items[1].querySelector('INPUT').checked /* || (mass.length == 0) */ || (mass.indexOf(elem[i].getAttribute("data-id")) != -1)) 
                elem[i].style.display = 'table-row';
            else elem[i].style.display = 'none';
            }
            else { // не пустой фильтр, выводим токлько элементы подпадающие под фильтр и если выбранна хоть одна группа, то попадающий в нее, а если не выбрана ни одна, то просто под фильтр
                var items = elem[i].querySelectorAll('TD');
                var msg = items[2].textContent.toLowerCase();
                if (items[1].querySelector('INPUT').checked
                    || ((msg.indexOf(ftx) != -1) && (mass.length == 0))
                    || ((msg.indexOf(ftx) != -1) && (mass.length != 0) && (mass.indexOf(elem[i].getAttribute("data-id")) != -1)))
                    elem[i].style.display = 'table-row';//'inline-block';
                else elem[i].style.display = 'none';
            }
        }
    }
    
    function renew_table() {
        
        var stab = document.getElementById("selwork");
        var atab = document.getElementById("allwork");
        var elms = stab.getElementsByClassName('with-filter');
        var elma = atab.getElementsByClassName('with-filter');
        
        for (i = 0; i < elms.length; i++) {
            var items = elms[i].querySelectorAll('TD');
            if (!items[1].querySelector('INPUT').checked) {
                atab.appendChild(elms[i]);
            }
        }
        for (i = 0; i < elma.length; i++) {
            var items = elma[i].querySelectorAll('TD');
            if (items[1].querySelector('INPUT').checked) {
                stab.appendChild(elma[i]);
            }
        }
    }
    
</script>
<?= Html::endForm() ?>
