/*
 * Copyright (c) 2019. Aleksey Eremin
 *
 */

/*
 Выдает гео-коды выбранных регионов согласно ОКТМО
 ISO3166, ISO3166_2:RU
 https://ru.wikipedia.org/wiki/ISO_3166-2:RU

 Пример Яндекс-карты
 https://tech.yandex.ru/maps/jsbox/2.1/regions

 */
var strRegs = '?regs=';           // аргумент параметров под-"регионы"
var Map1;
var colorSelect  = '#aa1314';
var colorNoselect = '#3f3fa2';
var strKeyMetka = '0305396879554012335'; // ключевая метка для наших полигонов
var Regions = defineRegion();  // определить регионы картирования (2 цифры)
var promise_regions = [];   // массив обещаний
var MyBounds = [[],[]];     // границы регионов
//
ymaps.ready(f1);

function f1() {
  // 0. Создаем карту, например так:
  var
    centerP = [55.751, 37.618], // Москва
    zoomP = 6;

  Map1 = new ymaps.Map('ymap', {
      center: centerP,
      zoom: zoomP,
      //type: null,
      controls: ['zoomControl']
    }
    // ,{
    //     restrictMapArea: [[10, 10], [85,-160]]
    // }
  );
  Map1.controls.get('zoomControl').options.set({size: 'small'});
  //
  // загрузка районов пегиона
  // код ОКТМО первые 2 знака - главный регион
  listReg(Regions.substr(0,2), function(value){
    regPolygon(value[1], value[2], value[0]);
  });
  Map1.geoObjects.events.add('click', clickOnPolygon);
  //Map1.events.add('click', clickOnPolygon);

  // ждем исполнения всех промисов после загрузки полигонов
  Promise.all(promise_regions).then(fallpromises);
}

/**
 * по завершению всех промисов переместить карту на границы
 * @param values
 */
function fallpromises(values)
{
  console.log("обещания выполнены " + values.length);
  //Map1.setCenter(Cpoint);
  if(MyBounds[0].length >= 2) {
    Map1.setBounds(MyBounds);
  } else {
    console.log("границы не определены");
  }
  promise_regions = [];
  MyBounds = [[],[]];    // границы регионов
}

/**
 * Обработка нажатия мыши над полигоном
 * @param e
 */
function clickOnPolygon(e)
{
  //alert('Дошло до коллекции объектов карты');
  // Получение ссылки на дочерний объект, на котором произошло событие.
  var obj = e.get('target');
  if(isMyPolygon(obj)) {
    // есть свойство
    //var slct = obj.properties._data.myKeySelect;
    var slct = fisSelect(obj);
    setSelectRegion(obj, !slct);
  }
  var otv = fgetSelectedRegions();
  var tit = document.title;
  var pan = document.location.pathname;
  var uri = pan + otv;
  window.history.pushState('', tit, uri);
  window.history.pathname  = uri;
  // https://tech.yandex.ru/maps/jsapi/doc/2.1/dg/concepts/events-docpage/
  e.stopPropagation();
}
/**
 * Определить - полигон выбран
 * @param obj полигон
 * @returns {boolean}
 */
function fisSelect(obj)
{
  var clr = obj.options.get('fillColor');
  return clr === colorSelect;
}

/**
 * Создать полигоны для региона
 * @param query запрос к OSM
 * @param name  название на подсказке
 * @param idreg код региона
 */
function regPolygon(query, name, idreg)
{
  var p;  // полигон
  // будем ждать "обещания, что нарисуется полигон"
  var promise_reg = new Promise(function (resolve, reject) {
    // Новоорский  район, Оренбургская область
    // http://nominatim.openstreetmap.org/search/
    var url;
    //url = "http://nominatim.openstreetmap.org/search";
    url = "que.php";
    $.getJSON(url, {oktmo: idreg}) // {q: query, format: "json", polygon_geojson: 1, polygon_threshold: 0.001}
        .then(function (data) {
          $.each(data, function (ix, place) {
            if ("relation" === place.osm_type) {
              var strm = JSON.stringify(data);
              $.post("putd.php", {oktmo: idreg, djso: strm}).then(function(dat) {
                console.log(idreg);
              });
              // 2. Создаем полигон с нужными координатами
              //var cpoint = coordinateswap(place.geojson.coordinates);
              //var coords = place.geojson.coordinates;
              if (place.geojson.type === 'MultiPolygon') {
                var ar1 = place.geojson.coordinates;
                for (var i = 0; i < ar1.length; i++) {
                  // https://noteskeeper.ru/1/
                  faddPolygon(ar1[i], name, idreg);
                }
              }
              if (place.geojson.type === 'Polygon') {
                var ar1 = place.geojson.coordinates;
                faddPolygon(ar1, name, idreg);
              }
            }
          });
          resolve("добавили " + idreg);
        }, function (err) {
          console.log("error: " + err);
          resolve("ошибка чтения региона " + idreg);
        });
  });
  // запомним
  promise_regions.push(promise_reg);
}

/**
 * Добавить полигон для региона
 * @param coords
 * @param regname
 * @param idreg
 */
function faddPolygon(coords, regname, idreg)
{
  var ncors = coords.slice();
  coordinateswap(ncors);
  // Инициализация цветом выбранных регионов по строке параметров "регионы"
  // если строка кода региона есть в строке аргументов, то он будет выбранный
  var colr = (Regions.indexOf(idreg)<0)? colorNoselect: colorSelect;
  var p = new ymaps.Polygon(ncors, {
    hintContent:      regname,      // название региона
    myKeyIdRegion:    idreg,        // идентификатор региона
    myKeyMetka:       strKeyMetka,  // ключевая метка для опознания своих полигонов
  }, {
    fillColor:        colr,         // цвет и признак, что полигон выбран
    fillOpacity:      0.4
  });
  // Добавляем полигон на карту
  Map1.geoObjects.add(p);
  //p.events.add('click', clickOnPolygon);
}

/**
 * Вернуть строку выбранных регионов
 */
function fgetSelectedRegions()
{
  var mapa = new Map();
  Map1.geoObjects.each(function (elm) {
    if(isMyPolygon(elm)) {
      // проверим выбран полигон ? (окрашен в нужный цвет)
      if(fisSelect(elm)) {
        var idreg = elm.properties._data.myKeyIdRegion;  // код региона
        mapa.set(idreg, 1);
      }
    }
  });
  var str = '', sep ='';
  var par = strRegs;
  mapa.forEach(function (value, key, map) {
    str = par + str + sep + key;
    sep = ','; par = '';
  });
  return str;
}

/**
 * Определим: данный объект наш полигон?
 * @param obj   объект
 * @returns {boolean}
 */
function isMyPolygon(obj) {
  if('properties' in obj && '_data' in obj.properties) {
      if(obj.properties._data.myKeyMetka === undefined) return false;
      if(strKeyMetka === obj.properties._data.myKeyMetka) return true;
    }
  return false;
}

/**
 * установить отобранность и цвет всем полигонам с таким-же регионом
 * @param obj   объект-полигон
 * @param slct  выбран/не выбран
 */
function  setSelectRegion(obj, slct)
{
  if (!isMyPolygon(obj)) return;
  var colr = slct? colorSelect: colorNoselect;             // цвет регионов
  var reg  = obj.properties._data.myKeyIdRegion;  // код региона
  Map1.geoObjects.each(function (elm) {
    if(isMyPolygon(elm)) {
      if(elm.properties._data.myKeyIdRegion === reg) {
        //elm.properties._data.myKeySelect = slct;  // уст. признак выбран
        elm.options.set('fillColor', colr);       // установить нужный цвет
      }
    }
  });
}

/**
 * Меняет местами координаты в массивах, вычисляет границы
 * @param coordinates
 */
function coordinateswap(coordinates)
{
  //var cnt = 0, x = 0, y = 0;
  coordinates[0].forEach(function(point, i, arr) {
    // поменяем координаты местами
    var a = point[0];
    point[0] = point[1];
    point[1] = a;
    if (MyBounds[0].length < 2) {
      MyBounds[0][0] = MyBounds[1][0] = point[0];
      MyBounds[0][1] = MyBounds[1][1] = point[1];
    } else {
      MyBounds[0][0] = Math.min(point[0], MyBounds[0][0]);
      MyBounds[0][1] = Math.min(point[1], MyBounds[0][1]);
      MyBounds[1][0] = Math.max(point[0], MyBounds[1][0]);
      MyBounds[1][1] = Math.max(point[1], MyBounds[1][1]);
    }
    //cnt++;
  });
  // if(cnt > 0) {
  //   Cpoint[0] = (MyBounds[0][0] + MyBounds[1][0])/2;
  //   Cpoint[1] = (MyBounds[0][1] + MyBounds[1][1])/2;
  // }
}


/**
 * Получить цвет объекта из коллекции
 * @param objectId
 * @param collection
 * @returns {string|string}
 */
function fcolorRegion(objectId, collection)
{
  var object = collection.getById(objectId);
  if (object && object.options) {
    var col = object.options.fillColor;
    return col;
  }
  return '?';
}

/**
 * Определить регион по аргументам, либо по-умолчанию
 */
function defineRegion()
{
  var i, sr, strs;
  strs = document.location.search;
  i = strs.indexOf(strRegs);  // есть строка с аргументами районами-региона?
  if (i < 0) {
    // регион явно в аргументах не задан
    // ищем в html параграф с id = "defaultRegion"
    var dr = document.getElementById('defaultRegion');
    if(dr) {
      sr = dr.innerText;
    } else {
      sr = '';
    }
  } else {
    // задан регион аргументом ?regs=
    sr = strs.substr(i + strRegs.length); // регион или список регионов
  }
  return sr.length >= 2? sr: "50"; // XX регион по-умолчанию
}
