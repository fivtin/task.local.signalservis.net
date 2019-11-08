<?php $this->title = "Сигнал: Карта заявок"; ?>
<style>
    
body {
    margin: 0;
}

.onoffswitch {
    position: relative; 
    width: 72px;
    float: left;
    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
}
.onoffswitch-checkbox {
    display: none;
}
.onoffswitch-label {
    display: block; overflow: hidden; cursor: pointer;
    border: 2px solid #D1D1D1; border-radius: 13px;
}
.onoffswitch-inner {
    display: block; width: 200%; margin-left: -100%;
    transition: margin 0.3s ease-in 0s;
}
.onoffswitch-inner:before, .onoffswitch-inner:after {
    display: block; float: left; width: 50%; height: 20px; padding: 0; line-height: 20px;
    font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
    box-sizing: border-box;
}
.onoffswitch-inner:before {
    content: "ДА";
    padding-left: 15px;
    background-color: #39C234; color: #FFFFFF;
}
.onoffswitch-inner:after {
    content: "НЕТ";
    padding-right: 15px;
    background-color: #FF0F0F; color: #FFFFFF;
    text-align: right;
}
.onoffswitch-switch {
    display: block; width: 13px; margin: 3.5px;
    background: #FFFFFF;
    position: absolute; top: 0; bottom: 0;
    right: 48px;
    border: 2px solid #D1D1D1; border-radius: 13px;
    transition: all 0.3s ease-in 0s; 
}
.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
    margin-left: 0;
}
.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
    right: 0px; 
}
.switch-label {
    display: block;
    margin-left: 80px;
    margin-top: 4px;
    color: #333333;
    cursor: pointer;
}
    
#map {
    height: 100vh; /* 100vh - 100% от высоты viewport(окна браузера) */
}
.home {
    left: 30px;
    background-image: url('../web/images/small/home.png');
}

.tv {
    left: 80px;
    background-image: url('../web/images/small/tv2.png');
}

.inet {
    left: 130px;
    background-image: url('../web/images/small/inet.png');
}

.electro {
    left: 180px;
    background-image: url('../web/images/small/electro.png');
}

.edit {
    left: 230px;
    background-image: url('../web/images/small/setting1.png');
}


.map-button:hover .map-panel {
    left: 10px;
    transition: 0.25s;
    
}
.map-button:not(:hover) .map-panel {
    /* left: 10px; */
    transition: 0.5s;
}

.map-button {
    z-index: 999;
    position: fixed;
    top: 8px;
    width: 40px;
    height: 40px;
    border: 1px solid #cccccc;
    border-radius: 3px;
    box-shadow: 1px 1px 2px 0px rgba(0, 0, 0, 0.2);
    filter: grayscale(100%);
    background-color: rgba(255, 255, 255, 0.95);
    background-repeat: no-repeat;
    background-size: 26px 26px;
    background-position: center;
    cursor: pointer;
}
.map-button:hover {
    /* background-color: rgba(255, 255, 255, 0.95); */
    box-shadow: 1px 1px 2px 0px rgba(0, 0, 0, 0.3);
    /* background-color: rgba(255, 255, 255, 0.95); */
    filter: grayscale(0%);
    background-position: 50% calc(50% - 1px);
}
.map-button.checked {
    background-color: #ffddaa;
    filter: grayscale(0%);
}


.map-panel {
    /* z-index: 9999; */
    position: fixed;
    top: 56px;
    left: -100%;
    width: 320px;
    height: calc(100% - 112px);
    background-color: rgba(255, 255, 255, 0.85);
    border: 1px solid #cccccc;
    border-radius: 4px;
    box-shadow: 3px 3px 8px 0px rgba(0, 0, 0, 0.2);
    padding: 4px;
    cursor: default;
}
.map-panel:hover {
    background-color: rgba(255, 255, 255, 0.95);
    box-shadow: 3px 3px 8px 0px rgba(0, 0, 0, 0.4);
}
.map-panel.show {
    left: 10px;
}
.map-panel.hide {
    left: -360px;
}

.center {
    text-align: center;
}

select.time, option {
    background-color: #ffffff;
    border: 1px solid #cccccc;
    border-radius: 2px;
    line-height: 40px;
    height: 48px;
    width: 45%;
    text-align-last: center;
    font-size: 40px;    
}

input[type="date"], input[list="operators"] {
    width: 90%;
    font-size: 32px;
    height: 48px;
    line-height: 48px;
    text-align: center;
}

</style>
<div id="map">
    <a href="/">
        <div class="map-button home" title="На главную"></div>
    </a>
    <div class="map-button tv" title="Заявки по ТВ" onclick="press_button(this);">
        <div class="map-panel">
            <h4 class="center">Телевидение</h4>
        </div>
    </div>
    <div class="map-button inet" title="Заявки по Интернету" onclick="press_button(this);">
        <div class="map-panel">
            <h4 class="center">Интернет</h4>
        </div>
    </div>
    <div class="map-button electro" title="Отключения энергии" onclick="press_button(this);">
        <div class="map-panel">
            <h4 class="center">Электричество</h4>
            <div style="position: absolute; bottom: 8px; ">
                <div class="onoffswitch">
                    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch">
                    <label class="onoffswitch-label" for="myonoffswitch">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
                <label for="myonoffswitch" class="switch-label">Показать все отключения</label>
            </div>
            <p>
                <select id="start" class="time">
                    <option disabled selected>--:--</option>
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
                </select>
                <select id="finish" class="time">
                    <option disabled selected>--:--</option>
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
                </select>
            </p>
            <input type="date" id="power-off-date" value="<?= date("Y-m-d", time()) ?>">
            <input list="operators" id="sender_fio" ;;;;">
            <datalist id="operators">
                <option value="ГЭС">
                <option value="Жигунова">
                <option value="Жилищник">    
                <option value="ЖСК">
                <option value="МУП ЕРЦ">
            </datalist>
            <div class="power-off-list">
                <p id="id_1056">
                    <b>Ленина д. 65</b>
                    <button class="rm-electro" onclick="this.parentNode.remove();">X</button>
                </p>
                <p id="id_1057">
                    <b>Ленина д. 65/2</b>
                    <button class="rm-electro" onclick="this.parentNode.remove();">X</button>
                </p>
                <p id="id_1058">
                    <b>Ленина д. 65/3</b>
                    <button class="rm-electro" onclick="this.parentNode.remove();">X</button>
                </p>
            </div>
            <button>Добавить</button>
        </div>
    </div>
    <div class="map-button edit" title="Настройка узлов" onclick="press_button(this);">
        <div class="map-panel">
            <h4 class="center">Редактирование</h4>
        </div>
    </div>
</div>
<script type="text/javascript">
    var state = "";
    ymaps.ready(init);    
    function init(){ 
        var myMap = new ymaps.Map("map", {
            center: [52.337971, 35.351743],
            zoom: 14,
            controls: ['typeSelector']
            //searchControlProvider: false,
            //trafficControlProvider: false
        }); 
        myMap.cursors.push('arrow');
        myMap.behaviors.disable('dblClickZoom');
        myMap.controls.add('zoomControl', {position: {right: '10px', top: '50px'}});
        
        //myMap.controls.add('zoomControl', {right: '25px', top: '42px'});
    }
    
    function press_button(elem) {
        state = elem.classList[1];
        var buttons = document.getElementsByClassName("map-button");
        for (i = 0; i < buttons.length; i++) {
            if (buttons[i].classList.contains(elem.classList[1])) {
                buttons[i].classList.add("checked");
                
                var first = buttons[i].getElementsByClassName("map-panel");
                first[0].classList.add("show");
            }
            else {
                buttons[i].classList.remove("checked");
                var first = buttons[i].getElementsByClassName("map-panel");
                //first[0].classList.remove("show");
            }
        }
        alert(state);
    }
    
</script>