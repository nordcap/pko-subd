<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей отчета "Выдача работ в БВП"
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
include_once("../modules/lib/lib.php");
require_once("begin1.php");
include_once("../js/jscript.js");
print "<a href=\"object.php\">Назад</a><br />\n";
print "<a href=\"../Graph/graphReport.php?Type=ProjectOutput_Archive\">График загрузки подразделений по месяцам</a><br />\n";
print "<CENTER><H2>Объектная карточка 'Архивные трудозатраты'</H2></CENTER><br />\n";
bodyProjectOutput("approvalBVP");
require_once("../end1.php");
?>
