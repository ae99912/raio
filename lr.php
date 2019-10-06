<?php
/*
 * Copyright (c) 2019.  Aleksey Eremin
 * 20.08.2019
 *
 *  Выдать список регионов
 * спиcок состоит из кода региона и краткого наименнования
 * [ {o: код_ОКТМО, k: краткое_название}, ...]
 *
 */
require_once "common.php";

$ws = "__000000";

$sql = "SELECT oktmo,kratko FROM sibwill_raio_oktmo WHERE oktmo LIKE '$ws'";
$res = queryDb($sql);
$arr = [];
while(list($oktmo,$kratko) = fetchRow($res)) {
    $arr[] = array("o"=>$oktmo, "k"=>$kratko);
}
// $res->close() не делать, так как выводит предупреждение на экран
// получим строку JSON
$str = json_encode($arr);
echo $str;
