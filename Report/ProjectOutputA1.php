<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей отчета "Выдача в БВП листов А1"
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
include_once("../modules/lib/lib.php");
require_once("begin1.php");
include_once("../js/jscript.js");
print "<a href=\"object.php\">Назад</a><br />\n";
print "<a href=\"../Graph/graphReport.php?Type=ProjectOutputA1\">График выдачи в БВП листов формата А1</a><br />\n";
print "<CENTER><H2>Объектная карточка 'Выдача в БВП листов формата А1'</H2></CENTER><br />\n";
bodyProjectOutputFormat("OutputA1");  
require_once("../end1.php");
?>