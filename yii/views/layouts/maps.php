<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<title>Отображение карты OpenStreetMap с помощью OpenLayers</title>
<script src="http://www.openlayers.org/api/OpenLayers.js"></script>
<script type="text/javascript"> 
      function init() {
       var map = new OpenLayers.Map("basicMap");
        var mapnik = new OpenLayers.Layer.OSM();
        map.addLayer(mapnik);
        map.setCenter(new OpenLayers.LonLat(35.357612,52.341125) // Центр карты
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // преобразование из WGS 1984
            new OpenLayers.Projection("EPSG:900913") // в Spherical Mercator Projection
          ), 14 // Уровень масштаба
        );
      }
    </script>
</head>
<body onload="init();">
    <div id="basicMap" style="width:100%;height:960px;"></div>
</body>
</html>