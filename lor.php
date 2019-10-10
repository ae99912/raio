<?php
/*
 * Copyright (c) 2019.  Aleksey Eremin
 * 20.08.2019
 *
 *  Выдать список под-регионов по главному коду
 * спиcок состоит из кода региона и краткого наименования
 * ?gok=17
 * [ {o: код_ОКТМО, k: краткое_название}, ...]
 *
 */
require_once "common.php";
// код головного ргиона
$a = $_REQUEST['gok'];
$kod = str_replace("'","",$a);
$kr = substr($kod,0,2);
if(intval($kr) != 0) {
  $kr1 = $kr . '______';
  $kr2 = $kr . '000000';
  $ws = "oktmo LIKE '$kr1' AND oktmo != $kr2";
} else {
  // если 0 - то регионы страны
  $ws = "lvl=100";
}


$sql = "SELECT oktmo,kratko FROM sibwill_raio_oktmo WHERE $ws";
$res = queryDb($sql);
$arr = [];
while(list($oktmo,$kratko) = fetchRow($res)) {
    $arr[] = array("o"=>$oktmo, "k"=>$kratko);
}
// $res->close() не делать, так как выводит предупреждение на экран
// получим строку JSON
$str = json_encode($arr);
echo $str;
