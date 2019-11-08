<?php use yii\helpers\Html; ?>
<style>
    .container {
        width: 90%;
    }
</style>
<script>
    var dots = [];
</script>
<?php
    foreach ($support as $item) {
        
        echo '<script>'.
                'dots.push(["'.($item['xy']['xcor']-rand(-100, 100)*0.0000005).'","'.($item['xy']['ycor']-rand(-100, 100)*0.0000005).'","'.$item['stime'].($item['sdate'] != date("Y-m-d") ? ' '.date("d-m-Y", strtotime($item['sdate'])) : '').'","'.$item['xy']['street'].' д. '.$item['xy']['home'].'<br>'.$item['scomment'].'","'.$item['sservice'].'"]);
             </script>';
    }
?>
</script>
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>

<div class="row">
    <div class="col-md-10">
        <div id="map" style="width: 100%; height: 800px; "></div>
    </div>
    <div class="col-md-2">
        <div class="text-center"> <!-- форма добавления заявки -->
        <?= Html::beginForm("/support", 'post', ['id' => 'w0']) ?>
            <input type="hidden" name="did" value="5<?= ''//Yii::$app->user->identity->did ?>" >
            <input type="hidden" name="sreport" value="" >
            <input type="date" class="form-control" name="sdate" value="<?= Yii::$app->session->get('sdate') ?>" />
            <select id="yid" class="form-control" name="yid">
                <option value="0" disabled selected>Выберите адрес...</option>
                <?php
                    foreach ($yandex as $item) {
                        ?>
                <option value="<?= $item['yid'] ?>"><?= $item['home'] != '---' ? $item['street'].' д. '.$item['home'] : $item['street'] ?></option>
                        <?php
                    }
                ?>
            </select>
            <select id="service" class="form-control" name="sservice">
                <option value="0" disabled selected>Тип заявки...</option>
                <option>Интернет</option>
                <option>ТВ</option>
                <option>Другое</option>
                <option>Недалеко</option>
            </select>
            <select id="time" class="form-control" name="stime">
                <option value="0" disabled selected>Время...</option>
                <option>07:00</option>
                <option>08:00</option>
                <option>09:00</option>
                <option>10:00</option>
                <option>11:00</option>
                <option>12:00</option>
                <option>13:00</option>
                <option>14:00</option>
                <option>15:00</option>
                <option>16:00</option>
                <option>17:00</option>
                <option>18:00</option>
                <option>19:00</option>
                <option>20:00</option>
                <option>21:00</option>
                <option>22:00</option>
            </select>
            <input type="text" class="form-control" name="scomment" placeholder="Комментарий" />
            <button id="progress" class="btn btn-danger btn-block"> Перейти</button>
        <?= Html::endForm() ?>
        </div>
        <div><!-- Список заявок -->
            <?php
                $xcor = 52.349163;
                $ycor = 35.343216;
                foreach ($support as $item) {
                    $color = "#ff0000";
                    if ($item['sservice'] == "Интернет") $color = "#0000ff";
                    if ($item['sservice'] == "Другое") $color = "#008000";
                    ?>
            <p style="margin-bottom: 0px; color: <?= $color ?>">
                <?= $item['xy']['home'] != '---' ? $item['stime'].' '.$item['xy']['street'].' д. '.$item['xy']['home'] : $item['stime'].' '.$item['xy']['street'] ?>
                <?= distance($xcor, $ycor, $item['xy']['xcor'], $item['xy']['ycor']) ?>
                <a style="float: right; display: none;" class="btn btn-danger btn-xs button-remove" href="/support/remove/<?= $item['sid'] ?>" onClick="return window.confirm('Вы действительно хотите удалить запись?');">X</a>
                <a id="_<?= $item['sid'] ?>" style="float: right; display: none;" class="btn btn-success btn-xs button-execute" href="/support/execute/<?= $item['sid'] ?>" onClick="return confirmAndPrompt(this);">X</a>
                <a id="click<?= $item['sid'] ?>"style="display: none;" href="/support/execute/<?= $item['sid'] ?>"></a>
                <p style="font-size: 12px; color: #666666; margin: 0px 0px 0px 42px;"><?= $item['scomment'] ?></p>
            </p>
                    <?php
                    $xcor = $item['xy']['xcor'];
                    $ycor = $item['xy']['ycor'];
                }
            ?>
        </div>
        <div class="text-center">
            <a id="refresh" href="/support" class="btn btn-primary btn-block"> Обновить</a><br>
            <a id="execute" href="#" class="btn btn-success btn-block"> Выполнено</a><br>
            <a id="remove" href="#" class="btn btn-danger btn-block"> Удалить</a>
        </div>
    </div>
</div>


<script>
    
function htmlspecialchars(html){
  var div =  document.createElement('div');
  div.innerText = html;
  return div.innerHTML;
}

function checkData() {
    //alert("test");
    if ((document.getElementById("yid").selectedIndex != 0) && (document.getElementById("service").selectedIndex != 0) && (document.getElementById("time").selectedIndex != 0)) {
        progress.innerHTML = " Добавить";
        progress.classList.remove("btn-danger");
        progress.classList.add("btn-success");
    }
    else {
        progress.innerHTML = " Перейти";
        progress.classList.remove("btn-success");
        progress.classList.add("btn-danger");
    }
}

function confirmAndPrompt(elem) {
    var _confirm = confirm('Отметить запись как выполненную?');
    if (_confirm != false) {
        var _prompt = prompt('Введите описание работ:');
        //_prompt = htmlspecialchars(_prompt.trim());
        _prompt = _prompt.replace(new RegExp('"','g'), '&quot;');

        //_prompt = encodeURI(_prompt.trim());
        //alert(_prompt);
        //if (_prompt != "") {
            var alink = document.getElementById("click"+elem.id.replace("_", ""));
            document.location.href = alink.href + '/' + _prompt;;
//            alert(alink);
//            alert(alink.id);
//            alink.href = alink.href + '/' + _prompt;
//            
//            alert(alink.href);
//            alink.onclick();
        //}
    }
    return false; //_confirm;
}

document.getElementById("remove").onclick = 
    function () {
        var rbutton = document.getElementsByClassName("button-remove");
        for (i = 0; i < rbutton.length; i++) {
            rbutton[i].style.display = "inherit";
        }
        var ebutton = document.getElementsByClassName("button-execute");
        for (i = 0; i < ebutton.length; i++) {
            ebutton[i].style.display = "none";
        }
    }
;
document.getElementById("execute").onclick = 
    function () {
        var rbutton = document.getElementsByClassName("button-remove");
        for (i = 0; i < rbutton.length; i++) {
            rbutton[i].style.display = "none";
        }
        var ebutton = document.getElementsByClassName("button-execute");
        for (i = 0; i < ebutton.length; i++) {
            ebutton[i].style.display = "inherit";
        }
    }
;

document.getElementById("yid").addEventListener("change", checkData);
document.getElementById("service").addEventListener("change", checkData);
document.getElementById("time").addEventListener("change", checkData);



var yandex = ymaps.ready(init);


// %2C2&z=18
function addGeoObject(yaobject, xcor, ycor, caption, comment, service) {
    // здесь расчитываем цвет значка и имя файла изображения
    color = "#ff0000"; file = "pRed.png";
    if (service == "Интернет") { color = "#0000ff"; file = "pBlue.png"; }
    if (service == "Другое") { color = "#008000"; file = "pGreen.png"; }
     
    yaobject.geoObjects

        .add(new ymaps.Placemark([xcor, ycor], {
            balloonContent: comment,
            iconCaption: caption
            //hintContent: comment,// это всплывающая подсказка
        }, {
            preset: 'islands#circleDotIcon',
            iconColor: color,
//            // Опции.
//            // Необходимо указать данный тип макета.
//            iconLayout: 'default#image',
//            // Своё изображение иконки метки.
//            iconImageHref: 'images/'+file,
//            // Размеры метки.
//            iconImageSize: [8, 8],
//            // Смещение левого верхнего угла иконки относительно
//            // её "ножки" (точки привязки).
//            iconImageOffset: [-4, -4]
        }));
}

function init() {
    var myMap = new ymaps.Map("map", {
            center: [52.340652, 35.365819],
            zoom: 14
        }, {
            searchControlProvider: 'yandex#search'
        });
        for (i = 0; i < dots.length; i++) {
            addGeoObject(myMap, dots[i][0],dots[i][1],dots[i][2],dots[i][3],dots[i][4]);
        }
//        myMap.MouseEvent.click = function () {
//    alert("Щелк!");
//};
// Прослушивание событий одного DOM-элемента.
//var block = document.getElementById('map');
//ymaps.domEvent.manager

//myMap.cursors.push('crosshair');
myMap.cursors.push('arrow');
myMap.behaviors.disable('dblClickZoom')
    myMap.events.add('contextmenu', function (event) {
        var coords = event.get('coords');

        //console.log(event.get('coorsd'));
        console.log('Координаты: '+ coords);
        //myMap.setCenter(coords);
        // Событие click.
    }).add('dblclick', function (event) {
        var coords = event.get('position');

        //console.log(event.get('coorsd'));
        console.log('Курсор: '+ coords);
        //myMap.setCenter(coords);
        // Событие click.
    }).add('click', function (event) {
        //var coords = event.get('position');

        //console.log(event.get('coorsd'));
        console.log('Клик');
        //myMap.setCenter(coords);
        // Событие click.
    });
//    .add(block, 'mouseleave', function (event) {
//        console.log(event.get('type'));
//        // Событие mouseleave.
//    })
//    // Задание одного слушателя на несколько событий.
//    .add(block, ['mousedown', 'mouseup'], function (event) {
//        console.log(event.get('type'));
//        // События mousedown / mouseup.
//    });
    return myMap;
}



</script>

