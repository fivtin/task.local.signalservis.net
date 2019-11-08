<?php // было в файле view.php ?>
<style>
    .material-switch > input[type="checkbox"] {
    display: none;   
}

.material-switch > label {
    cursor: pointer;
    height: 0px;
    position: relative; 
    width: 40px;  
}

.material-switch > label::before {
    background: rgb(0, 0, 0);
    box-shadow: inset 0px 0px 10px rgba(0, 0, 0, 0.5);
    border-radius: 8px;
    content: '';
    height: 16px;
    margin-top: -8px;
    position:absolute;
    opacity: 0.3;
    transition: all 0.4s ease-in-out;
    width: 40px;
}
.material-switch > label::after {
    background: rgb(255, 255, 255);
    border-radius: 16px;
    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
    content: '';
    height: 24px;
    left: -4px;
    margin-top: -8px;
    position: absolute;
    top: -4px;
    transition: all 0.3s ease-in-out;
    width: 24px;
}
.material-switch > input[type="checkbox"]:checked + label::before {
    background: inherit;
    opacity: 0.5;
}
.material-switch > input[type="checkbox"]:checked + label::after {
    background: inherit;
    left: 20px;
}
</style>

<span id="label_title" class="title_off hidden" style="display: block; float: left; " onclick="showTitleEdit()" ><?= $tunit->title ?></span>
<input type="text" id="title" name="title0" value="<?= $tunit->title ?>" size="96" maxlength="128" class="title_on hidden" style="display: none; float: left; " >
<span class="text-success glyphicon glyphicon-ok title_on" style="display: none; float: left; " onclick="saveTitle()" ></span>
<span class="text-danger glyphicon glyphicon-remove title_on" style="display: none; float: left; " onclick="undoTitle()"></span>
<span class="glyphicon glyphicon-pencil text-primary title_off hidden" id="block_id2" style="display: block; float: left; " onclick="showTitleEdit()" ></span>

<script type="text/javascript">
   /**
            * Функция Скрывает/Показывает блок 
            * @author ox2.ru дизайн студия
            **/
            function showHide(element_id) {
                //Если элемент с id-шником element_id существует
                if (document.getElementById(element_id)) { 
                    //Записываем ссылку на элемент в переменную obj
                    var obj = document.getElementById(element_id); 
                    //Если css-свойство display не block, то: 
                    if (obj.style.display != "block") { 
                        obj.style.display = "block"; //Показываем элемент
                    }
                    else obj.style.display = "none"; //Скрываем элемент
                }
                //Если элемент с id-шником element_id не найден, то выводим сообщение
                else alert("Элемент с id: " + element_id + " не найден!"); 
            }
            
            function showTitleEdit() {
                var titles = document.getElementsByClassName("title_on");
                for (var i = 0; i < titles.length; i++) {
                    var item = titles[i];
                    item.style.display = "block";
                }
                var titles = document.getElementsByClassName("title_off");
                for (var i = 0; i < titles.length; i++) {
                    var item = titles[i];
                    item.style.display = "none";
                }
            }
            
            function hideTitleEdit() {
                var titles = document.getElementsByClassName("title_on");
                for (var i = 0; i < titles.length; i++) {
                    var item = titles[i];
                    item.style.display = "none";
                }
                var titles = document.getElementsByClassName("title_off");
                for (var i = 0; i < titles.length; i++) {
                    var item = titles[i];
                    item.style.display = "block";
                }
                //var edit = document.getElementById("title");
                //edit.focus();
            }
            
            function saveTitle() {
                document.getElementById("label_title").innerHTML = document.getElementById("title").value;
                hideTitleEdit();
            }
            
            function undoTitle() {
                document.getElementById("title").value = document.getElementById("label_title").innerHTML;
                hideTitleEdit();
            }
           
           var fn = function(a) {
                var b;
                document.getElementById(a).onkeypress = function() {
                    b = event.keyCode
                };
                document.forms[0].onsubmit = function() {
                    var a = 13 != b;
                    b = "";
                    return a
                }
            }("title");
           
           
        </script>

<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4">
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">Material Design Switch Demos</div>

                <!-- List group -->
                <ul class="list-group">
                    <li class="list-group-item">
                        Bootstrap Switch Default
                        <div class="material-switch pull-right">
                            <input id="someSwitchOptionDefault" name="someSwitchOption001" type="checkbox"/>
                            <label for="someSwitchOptionDefault" class="label-default"></label>
                        </div>
                    </li>
                    <li class="list-group-item">
                        Bootstrap Switch Primary
                        <div class="material-switch pull-right">
                            <input id="someSwitchOptionPrimary" name="someSwitchOption001" type="checkbox"/>
                            <label for="someSwitchOptionPrimary" class="label-primary"></label>
                        </div>
                    </li>
                    <li class="list-group-item">
                        Задание выполнено
                        <div class="material-switch pull-right">
                            <input id="someSwitchOptionSuccess" name="status" value="1" type="checkbox"/>
                            <label for="someSwitchOptionSuccess" class="label-success"></label>
                        </div>
                    </li>
                    <li class="list-group-item">
                        Bootstrap Switch Info
                        <div class="material-switch pull-right">
                            <input id="someSwitchOptionInfo" name="someSwitchOption001" type="checkbox"/>
                            <label for="someSwitchOptionInfo" class="label-info"></label>
                        </div>
                    </li>
                    <li class="list-group-item">
                        Bootstrap Switch Warning
                        <div class="material-switch pull-right">
                            <input id="someSwitchOptionWarning" name="someSwitchOption001" type="checkbox"/>
                            <label for="someSwitchOptionWarning" class="label-warning"></label>
                        </div>
                    </li>
                    <li class="list-group-item">
                        Bootstrap Switch Danger
                        <div class="material-switch pull-right">
                            <input id="someSwitchOptionDanger" name="someSwitchOption001" type="checkbox"/>
                            <label for="someSwitchOptionDanger" class="label-danger"></label>
                        </div>
                    </li>
                </ul>
            </div>            
        </div>
    </div>
</div>

<!-- При клике запускаем функцию showHide, и передаем параметр 
        id-шник элемента который нужно показать/скрыть -->
        <a href="javascript:void(0)" onclick="showHide('block_id')">Скрыть/Показать элемент</a>
        <input type="text" name="title4" value="Ремонтные заявки по ТВ." maxlength="128" size="128" id="block_id0" style="display: none; float:left; ">
        <input type="text" name="date" value="2017-11-06" maxlength="10" size="16" id="block_id1" style="display: none; float:right; ">
        <br style="clear:both; ">
        
    <?php
    Modal::begin(['header' => '<h2>Дата задачи</h2>','toggleButton' => ['label' => ShowDate($tunit['dttask'], true), 'tag' => 'label'], 'footer' => 'Низ окна']);
    //echo Html::encode('Подключить "Красное и белое" - Ленина д.73');
    ?>
    <a href="#">Нажми...</a><br>
    <input type="date" name="dttask12" value="<?= $tunit['dttask'] ?>" />
    <?php
    Html::hiddenInput('date', 'new_date');
    Modal::end();
    ?>
    <?php
    Modal::begin(['header' => '<h2>Описание задачи</h2>','toggleButton' => ['label' => $tunit['title'], 'tag' => 'label'], 'footer' => 'Низ окна']);
    //echo Html::encode('Подключить "Красное и белое" - Ленина д.73');
    ?>
    <a href="#">Нажми...</a><br>
    <input type="text" size="32" maxlength="128" name="intype" value="<?= $tunit['title'] ?>" />
    <?php
    Html::hiddenInput('date', 'new_date');
    Modal::end();
    ?>
        
<style>
  .error {
    background: red;
  }
</style>

<div>Возраст:
  <input type="text" id="age">
</div>

<div>Имя:
  <input type="text">
</div>

<script>
  age.onblur = function() {
    if (isNaN(this.value)) { // введено не число
      // показать ошибку
      this.classList.add("error");
      //... и вернуть фокус обратно
      age.focus();
    } else {
      this.classList.remove("error");
    }
  };
</script>

<pre>
<?= print_r($tunit) ?>
</pre>