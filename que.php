<?php
/**
 * Created by PhpStorm.
 * User: ae
 * Date: 19.08.2019
 * Time: 15:55
 */
/**
 *  Запросить в OSM гео-данные на область
 */
require_once "common.php";
require_once ".proxy.php";

$kod = $_REQUEST['oktmo'];
$kodi = str_replace("'","",$kod);
$sql = "SELECT oktmo,zapros,geojson FROM raio_oktmo WHERE oktmo='$kodi'";
$res = queryDb($sql);
list($zapros,$geojson) = fetchRow($res);
if(strlen($geojson) < 32) {
    //  https://nominatim.openstreetmap.org/search/
    //   .
    $url = 'https://nominatim.openstreetmap.org/search/';
    $s = '?format=json&polygon_geojson=1&polygon_threshold=0.001';
    $sq = '&q=' . rawurlencode($zapros);
    $uri = $url . $s . $sq;
    $geo =  $contents = getURI($uri);
    echo $geo;
    if(strlen($geo) > 255) {
      $stmt = $My_Db -> prepare("UPDATE raio_oktmo SET geojson=? WHERE oktmo=?;");
      $stmt -> bind_param('ss', $geo, $kodi);
      if( ! $stmt->execute()) {
        //echo " error " . $php_errormsg;
      } else {
        //echo " ok";
      }
      // закрываем запрос
      $stmt->close();
      $geojson = $geo;
    } else {
      die("");  // ничего нет
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
  // file_get_contents($uri);
  $proxy = $proxy_server; // 'ip_address:port'
  $uauth = $proxy_user;   // 'username:userpassword';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $uri);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.1) Gecko/2008070208');
  curl_setopt($ch, CURLOPT_PROXY, $proxy);
  curl_setopt($ch,CURLOPT_HTTPPROXYTUNNEL, 0);
  curl_setopt($ch, CURLOPT_PROXYUSERPWD, $uauth);
  curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
  $ss=curl_exec($ch);
  curl_close($ch);
  return $ss;
}