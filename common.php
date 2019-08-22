<?php
/**
 * (C) 2019. Aleksey Eremin
 * 18.08.19
 *
 * Библиотека общих функций
 *
 */

require_once "MyDB.php";
// объект базы данных
$My_Db = new MyDB() ;

// подкаталог для сохранения файлов (документы, заметки)
define('FILES_DIR', 'files');

/**
 * Преобразование даты из формата SQL в строку русского формата DD.MM.YYYY
 * @param string $dat строка в формате SQL YYYY-MM-DD
 * @return string дата DD.MM.YYYY
 */
function  dat2str($dat)
{
  $str = null;
  if(preg_match("/(\d{4})-(\d{1,2})-(\d{1,2}).*/",$dat, $mah)) {
    $y = $mah[1];  $m = $mah[2];  $d = $mah[3];
    $str = sprintf("%02d.%02d.%04d", $d,$m,$y);
  }
  return $str;
}

/**
 * Преобразование строки русского формата DD.MM.[YY]YY в дату формата SQL YYYY-MM-DD
 * @param string $str строка в формате DD.MM.[YY]YY (вместо точки может быть запятая)
 * @return string дата  YYYY-MM-DD
 */
function  str2dat($str)
{
  $dat = null;
  if(preg_match("/(\d{1,2})[\.,](\d{1,2})[\.,](\d{2,4}).*/",$str, $mah)) {
    $d = $mah[1];  $m = $mah[2];  $y = $mah[3];
    if($y<100) $y = '20' . $y;
    $dat = sprintf("%04d-%02d-%02d", $y,$m,$d);
  }
  return $dat;
}

/**
 * Проверяет корректность строки даты с заданным форматом
 * http://php.net/manual/ru/function.checkdate.php
 * http://php.net/manual/ru/datetime.createfromformat.php
 * @param string $dat     строка даты
 * @param string $format  формат строки даты
 * @return bool true - дата корректна, false - неправильная дата
 */
function validateDate($dat, $format = 'Y-m-d')
{
  $d = DateTime::createFromFormat($format, $dat);
  return ($d) && ($d->format($format) == $dat);
}

/**
 * Формурует начало страницы html
 * @param string $title    заголовок страницы
 */
function  printHeadPage($title)
{
  echo <<<_EOF
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>$title</title>
</head>
<body>

_EOF;
}

/**
 * Формурует начало страницы html
 * @param string $title    заголовок страницы
 * @param string $timeOut  время перехода на другую страницу
 * @param string $url      URL страницы, куда перейти
 */
function  printHeadPage1($title, $timeOut='', $url='')
{
  $t = '';
  if(!empty($timeOut)) {
    $t = "<meta http-equiv=\"Refresh\" content=\"$timeOut; URL=$url\">";
  }
  echo <<<_EOF
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>$title</title>
  $t
  <link rel="stylesheet" type="text/css" href="css/style.css">

  <script type="text/javascript" language="javascript" src="js/jquery.min.js"></script>
</head>
<body>

_EOF;
}

/**
 * вывести конец страницы
 */
function printEndPage()
{
  echo <<<_EOF
</body>
</html>

_EOF;
}

/**
 * Выводит окно извещение, с последующим переходом на url
 * @param string $title    заголовок надпись
 * @param int    $timeOut  время ожидания
 * @param string $url      URL обратного перехода
 */
function  printPopupPage($title, $timeOut, $url)
{
  printHeadPage(strip_tags($title), $timeOut, $url);
  echo "<p>$title. <a href='$url' title='вернуться'>&lt;&lt;&lt;</a></p>";
  printEndPage();
}

/**
 * Возвращает первое поле в первой строке, заданного SQL-запроса
 * @param $sql  string SQL запрос
 * @return null значение поля
 */
function  getVal($sql)
{
  $val = null;
  $res = queryDb($sql);
  if ($row = fetchRow($res)) $val = $row[0];
  $res->close();
  return $val;
}

/**
 * Возвращает массив значений первой строки заданного SQL-запроса
 * @param $sql  string SQL запрос
 * @return null array цифровой массив значений
 */
function  getVals($sql)
{
  $res = queryDb($sql);
  $row = fetchRow($res);
  $res->close();
  return $row;
}

/**
 * Простая обертка для функции выполнения запроса
 * @param $sql  string  строка запроса
 * @return bool|mysqli_result результат запроса
 */
function  queryDb($sql)
{
  global $My_Db;
  return $My_Db->query($sql);
}

/**
 * Простая обертка для функции загрузки числового массива полей строки запроса
 * @param $res  mysqli_result   результат query
 * @return mixed    числовой массив результата
 */
function   fetchRow($res)
{
  return $res->fetch_row();
}

/**
 * Простая обертка для функции загрузки ассоциативного массива полей строки запроса
 * @param $res  mysqli_result   результат query
 * @return mixed    ассоциативный массив результата
 */
function   fetchAssoc($res)
{
  return $res->fetch_assoc();
}

/**
 * Простая обертка для функции загрузки числового и ассоциативного массива полей строки запроса
 * @param $res  mysqli_result результат query
 * @return mixed  числовой и ассоцитивный массив строки
 */
function  fetchArray($res)
{
  return $res->fetch_array();
}

/**
 * Выполнить SQL-запрос
 * @param string $sql  SQL-запрос
 * @return boolean|mixed результат выполнения оператора типа INSERT, DELETE, UPDATE
 */
function  execSQL($sql)
{
  global $My_Db;
  $r = $My_Db->query($sql);
  return $r;
}

/**
 * Подготавливает оператор для выполнения подстановок в SQL запросе
 * @param string $sql  строка SQL запроса
 * @return mysqli_stmt подготовленный оператор
 */
function  prepareSql($sql)
{
  global $My_Db;
  return $My_Db->prepare($sql);
}

/**
 * Преобразовывает символы кавычек и других символов входной строки в безопасные символы
 * @param string $str входная строка
 * @return string строка без кавычек
 */
function  s2s($str)
{
  return htmlspecialchars($str, ENT_QUOTES);
}


