<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.3.9
+-----------------------------------------------------------------------+
| Copyright (c) 2013 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является обработчиком события onchange выпадающего списка
"сотрудники"
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
include_once("../lib/lib.php");

$str = getNameEmployee_short($_REQUEST['data']);

print  $str;