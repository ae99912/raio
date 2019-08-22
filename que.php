<?php
/*
 * Copyright (c) 2019.  Aleksey Eremin
 * 19.08.2019
 *
 * ПО полученному коду ОКТМО
 * Выдать гео-данные, а если их нет, то запросить в OSM гео-данные на область
 * ?q=ОКТМО
 *
 */

require_once "common.php";
require_once ".proxy.php";

$kod = $_REQUEST['q'];  // код ОКТМО
$kodi = str_replace("'","",$kod);
$sql = "SELECT zapros,geojson FROM raio_oktmo WHERE oktmo='$kodi'";
$res = queryDb($sql);
list($zapros,$geojson) = fetchRow($res);
if(empty($geojson) || strlen($geojson)<32) {
    // читаем данные из OSM
    // https://nominatim.openstreetmap.org/search/
    $url = 'https://nominatim.openstreetmap.org/search/';
    $sa  = '?format=json&polygon_geojson=1&polygon_threshold=0.001';
    $sq  = '&q=' . rawurlencode($zapros);
    $uri = $url . $sa . $sq;
    $geo = getURI($uri);
    if(strlen($geo) > 255) {
      $stmt = $My_Db -> prepare("UPDATE raio_oktmo SET geojson=? WHERE oktmo='$kodi';");
      $stmt->bind_param('s', $geo);
      $stmt->execute();
//      if($stmt->execute()) {
//        // echo 'ok';
//      } else {
//        // echo " error " . $php_errormsg;
//      }
      // закрываем запрос
      // оператор вызывает вывод на экран сообщения
      // о предупреждении .
      //$stmt->close();
      $geojson = $geo;
    } else {
      die("[]");  // ничего нет
    }
}
echo $geojson;

/**
 * Чтение URI через прокси
 * @param $uri
 * @return bool|string
 */
function getURI($uri)
{
  global $proxy_server, $proxy_user;
  //return file_get_contents($uri);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $uri);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.1) Gecko/2008070208');
  // чтение через proxy-сервер
  if(!empty($proxy_server)) {
    curl_setopt($ch, CURLOPT_PROXY, $proxy_server);         // 'ip_address:port'
    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_user);    // 'username:userpassword';
    curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
  }
  $ss=curl_exec($ch);
  curl_close($ch);
  return $ss;
}
