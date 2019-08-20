<?php
/**
 * (C) 2019. Aleksey Eremin
 * 18.08.19 23:08
 */
//require_once ".mydb.php";
//$cccmydb = array (
//    'db'   => 'имя_базы',
//    'host' => 'адрес_хоста',
//    'usr'  => 'пользователь',
//    'pwd'  => 'пароль'
//);
$cccmydb = array (
    'db'   => 'raio',
    'host' => 'localhost',
    'usr'  => 'raio',
    'pwd'  => '1234ave5678'
);

class MyDB
    extends mysqli
{
  function __construct()
  {
    global $cccmydb;
    $c = $cccmydb;

    parent::__construct($c['host'], $c['usr'], $c['pwd'], $c['db']);
    //@ $this->connect($c['host'], $c['usr'], $c['pwd'], $c['db']);
    if($this->connect_errno) {
      die("?-Error-Ошибка открытия БД");
    }
  }

  function __destruct()
  {
    $this -> close();
    //echo " destruct ";
  }
}

