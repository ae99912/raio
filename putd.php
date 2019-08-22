<?php
/*
 * Copyright (c) 2019. Aleksey Eremin
 * 19.08.2019
 *
 * Положить данные в БД
 *
 */

require_once "common.php";

//printHeadPage("Данные ГЕО");

$kod = $_REQUEST['oktmo'];
$dat = $_REQUEST['djso'];

echo "$kod ";
$stmt = $My_Db -> prepare("UPDATE raio_oktmo SET geojson=? WHERE oktmo=?;");
$stmt -> bind_param('ss', $dat, $kod);
if( ! $stmt->execute()) {
  echo "error " . $php_errormsg;
} else {
  echo "ok";
}
echo "\r\n";
//printEndPage();

