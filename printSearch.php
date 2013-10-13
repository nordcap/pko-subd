<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей печати данных, полученных при поиске
на странице поиска
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
include_once("modules/lib/lib.php");
require_once("begin1.php");
//include_once("../js/jscript.js");
print "<a href=\"search.php\" title=\"Поиск\">Назад</a>\n";
print "<br /><br />";
//phpinfo(32);
print "<CENTER><H2>Карта учета рабочего времени</H2></CENTER><br />\n";
if ( isset($_SESSION['NameSelectedEmployee']))
{
	ViewTableSearch_One(); //текущий пользователь или выбранный

}
else {
	ViewTableSearch_All();  //отчет при выборе отдела и бюро
}
$_SESSION['Query'] = null;
$_SESSION['NameSelectedEmployee'] = null;
$_SESSION['lstMonth'] = null;
$_SESSION['lstYear'] = null;
?>
<div class="g-row">
    <div class="g-2">Сотрудник:</div>
    <div class="g-2">_______________</div>
</div>
<div class="g-row">
    <div class="g-2">Начальник бюро:</div>
    <div class="g-2">_______________</div>
</div>
<div class="g-row">
    <div class="g-2">Начальник отдела:</div>
    <div class="g-2">________________</div>
</div>
</body>
<script  type="text/javascript">
$(document).ready(function(){
     $('.f-table-zebra tr>td:nth-child(3)').css('text-align', 'left');
     $('.f-table-zebra tr>td:nth-child(4)').css('text-align', 'left');
  });
</script>
</html>