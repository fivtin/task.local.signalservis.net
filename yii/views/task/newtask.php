<style>
    .mb5px {
        margin-bottom: 5px;
    }
    .padding5px10px {
        padding: 5px 10px;
    }
    .marginauto {
        display:block;
        margin: 3px auto;
    }

</style>
<div class="container">
    <div class="row">
        
        <div class="col-md-12">
            <div class="panel panel-primary">
                
                <div class="panel-heading">
                    <h3 class="panel-title">Понедельник, 20 ноября 2017 года</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-md-2">Дата</label>
                            <div class="col-md-2">
                                <input class="form-control" type="date" value="2017-11-20" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">Описание задачи</label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" placeholder="Введите описание задачи" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">Описание работ</label>
                            <div class="col-md-6">
                                <textarea rows="2" class="form-control" placeholder="Краткое описание работ"></textarea>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group worklist">
                            <div class="col-md-2">
                                <label class="control-label marginauto">Работы</label><br>
                                <button class="btn btn-success" id="id1" onclick="addwork();" type="button" >Add</button>
                                <button class="btn btn-danger" id="id2" onclick="removeall();" type="button" >Del</button>
                            </div>
                            <div class="col-md-10" id="accordion">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>    
    </div>
    
</div>


<!-- ШАБЛОН ФОРМЫ ВЫБОРА ВИДА РАБОТЫ -->
<template id="template_panel">
    <div class="panel panel-danger mb5px">
        <div class="hidden num_index">NNNNN</div>
        <div class="panel-heading padding5px10px">
            <label>
                <a data-toggle="collapse" data-parent="#accordion" href="#collapse_NNNNN">Добавление работы</a><span></span>
            </label>
            <button class="btn btn-xs btn-danger glyphicon glyphicon-remove pull-right" id="btn_NNNNN" type="button" onclick="remove('panel_NNNNN')" /></button>                                                   
        </div>
        <div id="collapse_NNNNN" class="panel-collapse collapse">
            <div id="work_NNNNN" class="panel-body">
                <label class="control-label">Категория
                    <select id="select_NNNNN" class="form-control" onchange="loadworklist('panel_NNNNN');">
                        <option value="0" >Все работы</option>
                    </select>
                </label>
                <label class="control-label" >Фильтр<input class="form-control" type="text" onkeyup="loadworklist('panel_NNNNN');" ></label>
                <button type="button" class="btn btn-success" onclick="loadworklist('panel_NNNNN');">Фильтр</button>
                <div class="col-md-10 col-md-offset-1">
                    <table class="table table-condensed table-striped" ></table>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- ШАБЛОН СПИСКА ЭЛЕМЕНТОВ КАТЕГОРИЙ -->
<template id="template_options">
    <?php
        foreach ($cat as $item) {
    ?>
    <option value="<?= $item['eqid'] ?>"><?= $item['title']  ?></option>
    <?php } ?>
</template>

<!-- ШАБЛОН СТРОКИ ТАБЛИЦЫ СПИСКА РАБОТ -->
<template id="table_row">
    <tr>
        <td><button class="btn-link" type="button" onclick="selectwork('NNN', 'TITLE', TWID);" ></button></td>
    </tr>
</template>

<!--  -->
<template id="work_list">
    <?php
    foreach ($tw as $item) {
    ?>
    <ul class="work_item_data">
        <li class="twid"><?= $item['twid'] ?></li>
        <li class="twid_category"><?= $item['twid'] ?></li> <!-- заменить потом на "category" -->
        <li class="twid_title"><?= $item['title'] ?></li>
        <li class="twid_detail"><?= $item['detail'] ?></li>
        <li class="twid_info"><?= $item['info'] ?></li>
        <li class="twid_cost"><?= $item['cost'] ?></li> <!-- здесь должна быть проверка прав и этот пункт выводится только кому положено -->
    </ul>
    <?php
    }
    ?>
</template>

<!-- ШАБЛОН СПИСКА СОТРУДНИКОВ -->
<template id="employe_list">
    <?php
        foreach ($emp as $item) {
            ?>
    <input type="checkbox" name="cb[NNNNN][TWID][EID]" id="cb<?= $item['eid'] ?>_NNN"><label for="cb<?= $item['eid'] ?>_NNN"><?= $item['fio_short'] ?></label><br>
            <?php
        }
    ?>
</template>

<!--  -->
<template id="work_item">
    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label col-md-2">Комментарий</label>
            <div class="col-md-8">
                <input class="form-control" type="text" name="info[0][1]" ><!-- первый индекс-номер панели, второй-twid name="info[NNNNN][TWID]" -->
            </div>
        </div>
    <div class="col-md-4">
        
        <div class="form-group">
            <label class="control-label col-md-5">Количество</label>
            <div class="col-md-7">
                <input class="form-control" type="number" name="nrepeat[0][1]" value="1" ><!-- первый индекс-номер панели, второй-twid -->
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-5">Стоимость</label>
            <div class="col-md-7">
                <input class="form-control" type="number" name="cost[0][1]" value="1" ><!-- первый индекс-номер панели, второй-twid -->
            </div>
        </div>
        
    </div>
    <div class="col-md-8 employe-list"></div>
    </div>
    <button type="button" class="btn btn-success pull-right" >OK</button>
</template>

<!-- ШАБЛОН СПИСКА ВЫБОРА КАТЕГОРИИ -->
<template id="category_list">
    <select class="form-control" onchange="alert(this.options[this.selectedIndex].value);">
        <option value="0" selected>Все работы</option>
        <?php
            foreach ($cat as $item) {
        ?>
        <option value="<?= $item['eqid'] ?>"><?= $item['title']  ?></option>
        <?php } ?>
    </select>
</template>


<script type="text/javascript">

function addwork() {
    
    //class="table table-condensed table-striped"
    
    // определяем максимальный индекс панели на странице
    var num = document.getElementsByClassName('num_index');
    var max = 0;
    for (i = 0; i < num.length; i++) {
        if (Number(num.item(i).innerHTML) > max) max = Number(num.item(i).innerHTML);     
    }
    // +1 для нового элемента
    max = Number(max) + 1;
    
    // создаем ссылку на список работ на странице
    var works = document.getElementById('accordion');
    
    // загружаем шаблон панели
    var panel = document.getElementById('template_panel').content.cloneNode(true);
    
    // меняем ID панели
    panel = panel.querySelector('DIV');
    panel.id = 'panel_' + max;
    panel.innerHTML = panel.innerHTML.replace(new RegExp('NNNNN','g'), max);
  
    // добавляем список опций в фильтр категорий
    
    var select_tag = panel.querySelector('SELECT');    
    var options = document.getElementById('template_options').content.cloneNode(true);
    select_tag.appendChild(options);
    
    
    
    // вставляем панель на страницу перед ранее добавленными элементами
    works.insertBefore(panel, works.children[0]);
    
    // загружаем список видов работ
    loadworklist(panel.id);
    
}

// 
function loadworklist(pnName) {

    // ищем элемент по имени, так как далее этот же обработчик используется для фильтрации
    var pnItem = document.getElementById(pnName);
    
    // загружаем параметр фильтра
    var flCategory = pnItem.getElementsByTagName('SELECT')[0].value; // по категории
    var flTitle = pnItem.getElementsByTagName('INPUT')[0].value; // по названию
    flTitle = flTitle.trim().toLowerCase(); // убираем пробелы и переводим в нижний регистр

    // загружаем список работ и шаблон строки
    var work_list = document.querySelector('#work_list').content.cloneNode(true);
    var tt = work_list.querySelectorAll('UL');
    var table_all = pnItem.getElementsByTagName('TABLE');
    var table_row = document.querySelector('#table_row').content;

    // очищаем содержимое таблицы
    table_all[0].innerHTML = '';
        
    // обрабатываем построчно
    for (i = 0; i < tt.length; i++) {

        // загружаем строку
        var li = tt[i].querySelectorAll('LI');

        // поля данных
        var twid = li[0].innerHTML;
        var category = li[1].innerHTML;
        var title = li[2].innerHTML;
        var detail = li[3].innerHTML;
        var info = li[4].innerHTML;
        var cost = li[5].innerHTML;
        
        // экранируем кавычки символом "минуты" для Javascript
        
        var caption = title.replace(new RegExp('"','g'), '&quot;');
        //var caption = title.replace(new RegExp('"','g'), '&#8243;');
        
        // проверяем по фильтру
        if ((flCategory === '0') || (flCategory === category)) {
            var ttl = title.toLowerCase();
            ttl = ttl.indexOf(flTitle);
            //alert(ttl);
            
            if ((flTitle === '') || (ttl !== -1)) {
    
                // копируем шаблон строки
                var row = table_row.cloneNode(true);

                // вставляем данные
                var td = row.querySelector('TR');
                var btn = row.querySelector('BUTTON');

                btn.title = detail;
                btn.textContent = title;

                td.innerHTML = td.innerHTML.replace('NNN', pnItem.id);
                td.innerHTML = td.innerHTML.replace('TITLE', caption);
                td.innerHTML = td.innerHTML.replace('TWID', twid);

                // добавляем строку в таблицу
                var row = table_all[0].appendChild(row);
            }
        }
        
    
//        var table_row = document.createElement('TR');
//        var table_col = document.createElement('TD');
//        table_col.textContent = title;
//        //table
//        table_row.appendChild(table_col);
//        table_all[0].appendChild(table_row);

    }
 
}

// выбор вида работы
function selectwork(pnID, title, twid) {
    
    // определяем индекс идентификатора
    var id = pnID.substring(6);
    
    // меняем заголовок
    var panel = document.getElementById(pnID);
    var link = panel.getElementsByTagName('A');
    link[0].textContent = title;
    
    // очищаем панель работы от списка работ
    var work = document.getElementById('work_' + id);    
    work.innerHTML = '';
    
    // меняем цвет панели
    panel.classList.remove('panel-danger');
    panel.classList.add('panel-success');
    
    // загружаем шаблон в панель работы
    var item = document.getElementById('work_item').content.cloneNode(true);
    item = item.querySelector('DIV');
    
    // загружаем список сотрудников на выполнение работы
    var employe = document.getElementById('employe_list').content.cloneNode(true);
    
    // вставляем шаблон в панель
    
    var emp = item.getElementsByClassName('employe-list');
    emp[0].appendChild(employe);
    
    work.appendChild(item);
    
}


 
function remove(id){
    if (confirm("Удалить этот элемент?")) document.getElementById(id).remove();
 }
 
function removeall() {
    if (confirm("Очистить список работ?")) document.getElementById('accordion').innerHTML='';
 }
 
</script>
<?php
//  $dom = new domDocument("1.0", "utf-8"); // Создаём XML-документ версии 1.0 с кодировкой utf-8
//  $root = $dom->createElement("users"); // Создаём корневой элемент
//  $dom->appendChild($root);
//  $logins = array("User1", "User2", "User3"); // Логины пользователей
//  $passwords = array("Pass1", "Pass2", "Pass3"); // Пароли пользователей
//  for ($i = 0; $i < count($logins); $i++) {
//    $id = $i + 1; // id-пользователя
//    $user = $dom->createElement("user"); // Создаём узел "user"
//    $user->setAttribute("id", $id); // Устанавливаем атрибут "id" у узла "user"
//    $login = $dom->createElement("login", $logins[$i]); // Создаём узел "login" с текстом внутри
//    $password = $dom->createElement("password", $passwords[$i]); // Создаём узел "password" с текстом внутри
//    $user->appendChild($login); // Добавляем в узел "user" узел "login"
//    $user->appendChild($password);// Добавляем в узел "user" узел "password"
//    $root->appendChild($user); // Добавляем в корневой узел "users" узел "user"
//  }
//  $dom->save(__DIR__."/u0s0e0r0s.xml"); // Сохраняем полученный XML-документ в файл
//  echo (__DIR__);
//
//  //require_once 'phpexcel/PHPExcel.php'; // Подключаем библиотеку PHPExcel
//  $phpexcel = new PHPExcel(); // Создаём объект PHPExcel
//  /* Каждый раз делаем активной 1-ю страницу и получаем её, потом записываем в неё данные */
//  $page = $phpexcel->setActiveSheetIndex(0); // Делаем активной первую страницу и получаем её
//  $page->setCellValue("A1", "Hello"); // Добавляем в ячейку A1 слово "Hello"
//  $page->setCellValue("A2", "World!"); // Добавляем в ячейку A2 слово "World!"
//  $page->setCellValue("B1", "MyRusakov.ru"); // Добавляем в ячейку B1 слово "MyRusakov.ru"
//  $page->setTitle("Test"); // Ставим заголовок "Test" на странице
//  /* Начинаем готовиться к записи информации в xlsx-файл */
//  $objWriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');
//  /* Записываем в файл */
//  $objWriter->save(__DIR__."/test.xlsx");

?>