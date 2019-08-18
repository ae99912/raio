<?php
/**
 * (C) 2019. Aleksey Eremin
 * 18.08.19 23:07
 *
 */
/*
 * Библиотека общих функций.
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
 * @param string $timeOut  время перехода на другую страницу
 * @param string $url      URL страницы, куда перейти
 */
function  printHeadPage($title, $timeOut='', $url='')
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
  echo "\n</body>\n</html>\n";
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
 * Переход в указанное место URL
 * @param string $url  URL перехода
 */
function  gotoLocation($url)
{
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: " . $url);
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

/**
 * Проверить время активности пользователя (тайм-аут активности) сек
 * @param int $tmout время неактивности, сек
 */
function  test_timeout_user_actitiviti($tmout)
{
  global $Uid;
  // время ожидания активности пользователя
  $tsnow = date('U'); // текущее время
  $tsses = intval($_SESSION['datatime_work_metka']);
  $_SESSION['datatime_work_metka'] = $tsnow;
  if($tsses > 0 && ($tsnow - $tsses) > $tmout) {
    unset($_SESSION['Uid']);
    $Uid = 0;
    $_SESSION["error_message"] = "<span style='color: blue'>Истекло время ожидания...</span>";
  }
}

/**
 * формирует форму с тэгом select и элементами option для переключения названного параметра.
 * форма выбора региона "автозапуском" https://javatalks.ru/topics/22399
 * @param string $nameParam  название параметра для выбора региона
 * @return string строка с формой
 */
function  make_FormSelectRegion($nameParam)
{
  $myself = $_SERVER['PHP_SELF'];
  $reg = intval(getParSes($nameParam));
//  $str = <<<_EOF
//  <form action='$myself' method='post' name='FormSelReg'>
//  <select size=1 name='$nameParam' onchange="document.forms['FormSelReg'].submit()">
//_EOF;
  $str  = "<form action='$myself' method='post'>";
  $str .= "<select size=1 name='$nameParam' onchange='this.form.submit()'>";
  $rst = queryDb("SELECT id,nam FROM Regions WHERE id<100 ORDER BY id");
  while(list($fid,$fnam)=fetchRow($rst)) {
    if($fid == 0) { $fnam = "(все регионы)"; }
    $s =($reg == $fid)? 'selected': '';
    $str .= "<option value='$fid' $s>$fnam</option>";
  }
  $rst->close();
  $str .= "</select></form>";
  return $str;
}

/**
 * Вернуть значение парметра из параметра формы или сессионной переменной
 * и задать этот параметр в сессию, а если параметр формы не задан, то прочитать
 * этот парметр из сессии.
 * @param $namePar
 * @return mixed
 */
function getParSes($namePar)
{
  if(array_key_exists($namePar, $_REQUEST)) {
    // вызвали форму
    $par = $_REQUEST[$namePar];
    $_SESSION[$namePar] = $par;
  } else {
    // форму не вызывали, проверим сессионную переменную
    $par = $_SESSION[$namePar];
  }
  return $par;
}

/**
 * Определить возможность редактирования оператора, если uid больше 1 и меньше 100,
 * то это супер-пользователь, который может редактировать любой регион.
 * Если uid > 100 то можно редактировать только свой регион.
 * @param int $op_id  код оператора
 * @return bool можно редактировать
 */
function  isCanEditOp($op_id)
{
  global $Uid, $Reg;
  if($Uid <= 1)
    return false;
  if($Uid < 100)
    return true;
  // установим признак возможности редактирования строки оператора по региону
  $re = sprintf("%02d", $Reg);  // двузначный номер региона
  $canEditOp = intval(getVal("SELECT COUNT(*) FROM Opers WHERE op_id=$op_id AND regs LIKE '%$re%'"));
  return  $canEditOp != 0;
}

/**
 * Определить возможность редактирования конкретной записи, по номеру региона
 * если uid 2-99,то это супер-пользователь, который может редактировать любой регион.
 * Если uid > 100 то можно редактировать только свой регион.
 * @param int $regstr номер региона строки файла
 * @return bool можно редактировать
 */
function  isCanEditRec($regstr)
{
  global $Uid, $Reg;
  if($Uid <= 1)
    return false;
  if($Uid < 100)
    return true;
  if($regstr == $Reg)
    return true;
  return false;
}
