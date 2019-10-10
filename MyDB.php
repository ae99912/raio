<?php
/*
 * (C) 2019. Aleksey Eremin
 * 18.08.19
 */
require_once ".mydb.php";
//$cccmydb = array (
//    'db'   => 'имя_базы',
//    'host' => 'адрес_хоста',
//    'usr'  => 'пользователь',
//    'pwd'  => 'пароль'
//);

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
    // русификация вывода из MySql
    // https://dev.mysql.com/doc/refman/5.5/en/charset-connection.html
    $this -> query("SET NAMES 'utf8';");
    // $this -> query("SET CHARACTER SET 'utf8';");
    // $this -> query("SET SESSION collation_connection='utf8_general_ci';");

  }

  function __destruct()
  {
    $this -> close();
    //echo " destruct ";
  }
}

