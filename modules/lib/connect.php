<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль предназначен для коннекта к БД

+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
  $link = mysql_connect("localhost:3306", "alex","1234567890") or die("Not connected : " . mysql_error());
  mysql_select_db("pko_azot",$link) or die ("Can\'t use pko_azot : " . mysql_error());
/*  mysql_query("SET character SET CLIENT=cp1251",$link);
  mysql_query("SET character SET RESULTS=cp1251",$link);
  mysql_query("SET collation_connection=cp1251_general_ci",$link);*/
mysql_query("SET NAMES 'utf8'",$link);
//mysql_set_charset('latin1', $link);  //применяется (PHP 5 >= 5.2.3)


  $_SESSION['localTime'] = 0; //отставание от местного времени на 7 часов если время зимнее и 6 если летнее
?>