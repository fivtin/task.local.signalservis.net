<style>
    .container {
        width: 90%;
    }
</style>
<script>
    var dots = [];
</script>
<?php
    $indoor = 0;
    $outdoor = 0;
    foreach ($support as $item) {
        
        if ($item['stype'] >= 40) $indoor++; else $outdoor++;
        
        echo '<script>'.
                'dots.push(["'.($item['xy']['xcor']-rand(-100, 100)*0.0000005).'","'.($item['xy']['ycor']-rand(-100, 100)*0.0000005).'","'.$item['stime'].($item['sdate'] != date("Y-m-d") ? ' '.date("d-m-Y", strtotime($item['sdate'])) : '').'","['.$item['sid'].'] '.date("d-m-Y", strtotime($item['sdate'])).'<br>'.$item['xy']['street'].' д. '.$item['xy']['home'].'<br>'.$item['scomment'].'<br>'.$item['sreport'].'","'.$item['stype'].'"]);
                    
             </script>';
    }
?>

<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>

<div class="row">
    <div class="col-md-12">
        <div id="map" style="width: 100%; height: 800px; "></div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <h4>Статистика по заявкам</h4>
    </div>
    <div class="col-md-12">
        Проблема вне помещения абонента: <?= $outdoor ?>
    </div>
    <div class="col-md-12">
        Проблема в квартире: <?= $indoor ?>
    </div>
</div>


<script>
    
var yandex = ymaps.ready(init);

// %2C2&z=18
function addGeoObject(yaobject, xcor, ycor, caption, comment, service) {
    // здесь расчитываем цвет значка и имя файла изображения
    color = "#ff0000"; file = "pRed.png";
    //if (service == "Интернет") { color = "#0000ff"; file = "pBlue.png"; }
    //if (service == "Другое") { color = "#008000"; file = "pGreen.png"; }
    if (service > 40) { color = "#0000ff"; file = "pBlue.png"; }
     
    yaobject.geoObjects

        .add(new ymaps.Placemark([xcor, ycor], {
            //balloonContent: comment,
            //iconCaption: caption
            hintContent: comment,// это всплывающая подсказка
        }, {
//            preset: 'islands#circleDotIcon',
//            iconColor: color,
//            // Опции.
//            // Необходимо указать данный тип макета.
            iconLayout: 'default#image',
//            // Своё изображение иконки метки.
            iconImageHref: '../../images/'+file,
//            // Размеры метки.
            iconImageSize: [8, 8],
//            // Смещение левого верхнего угла иконки относительно
//            // её "ножки" (точки привязки).
            iconImageOffset: [-4, -4]
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
            //console(i);
        }
    return myMap;
}



</script>
