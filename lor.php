<?php
/**
 * Copyright (c) 2019.  Aleksey Eremin
 * Date: 20.08.2019
 * Time: 8:36
 */
/*
 * Выдать список под-регионов по главному коду
 * спиcок состоит из кода региона и краткого наименнования
 * ?gok=17
 * [ {o: код_ОКТМО, k: краткое_название}, ...]
 */
require_once "common.php";
// код головного ргиона
$a = $_REQUEST['gok'];
$kod = str_replace("'","",$a);
if(strlen($kod) != 8) {
  die("неверный код ОКТМО - не 8 цифр: " . $a);
}
$ws = substr($kod,0,2) . "______";

$sql = "SELECT oktmo,kratko FROM raio_oktmo WHERE oktmo LIKE '$ws'";
$res = queryDb($sql);
$arr = [];
while(list($oktmo,$kratko) = fetchRow($res)) {
    $arr[] = array("o"=>$oktmo, "k"=>$kratko);
}
// $res->close() не делать, так как выводит предупреждение на экран
// получим строку JSON
$str = json_encode($arr);
echo $str;
