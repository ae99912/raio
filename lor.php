<?php
/*
 * Copyright (c) 2019.  Aleksey Eremin
 * 20.08.2019
 *
 *  Выдать список под-регионов по главному коду
 * спиcок состоит из кода региона и краткого наименнования
 * ?gok=17
 * [ {o: код_ОКТМО, k: краткое_название}, ...]
 *
 */
require_once "common.php";
// код головного ргиона
$a = $_REQUEST['gok'];
$kod = str_replace("'","",$a);
$level = intval($_REQUEST['level']);
if($level == 100) {
  // высший уровень - страна
  $ws = "lvl=100";
} else {
  if (strlen($kod) != 8) {
    die("неверный код ОКТМО - не 8 цифр: '$a'");
  }
  $ws = "oktmo LIKE '" . substr($kod, 0, 2) . "______'";
}

$sql = "SELECT oktmo,kratko FROM raio_oktmo WHERE $ws";
$res = queryDb($sql);
$arr = [];
while(list($oktmo,$kratko) = fetchRow($res)) {
    $arr[] = array("o"=>$oktmo, "k"=>$kratko);
}
// $res->close() не делать, так как выводит предупреждение на экран
// получим строку JSON
$str = json_encode($arr);
echo $str;
