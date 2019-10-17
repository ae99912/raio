/*
 * Copyright (c) 2019. Aleksey Eremin
 *
 * 2019-08-19
 * 2019-09-17
 */

/*


 */

var strRegs = '?regs=';           // аргумент параметров под-"регионы"
var Map1;
var colorSelect  = '#aa1314';
var colorNoselect = '#3f3fa2';
var strKeyMetka = '0305396879554012335'; // ключевая метка для наших полигонов
var Regions = defineRegion();  // определить регионы картирования (2 цифры)
var Regions0 = Regions.substr(0,2); // регион или список регионов;     // начальный набор
var promise_regions = [];   // массив обещаний
var MyBounds = [[],[]];     // границы регионов
//

ymaps.ready(f1);

