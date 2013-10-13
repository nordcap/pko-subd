<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.4
+-----------------------------------------------------------------------+
| Copyright (c) 2011 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей отчета "Отчет по видам выполняемых работ"
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
error_reporting(E_ERROR & ~E_NOTICE );
session_start();
//начать буферизацию вывода
ob_start();
include_once("../modules/lib/lib.php");
require_once("begin1.php");
include_once("../js/jscript.js");
//инициализация массива времени
print "<div style=\"margin-left: 10px;\">";
print "<a href=\"object.php\">Назад</a>\n";
print "</div>";

print "<H2>Отчет по выполненным работам</H2><br />\n";
print "<Hr Width=\"100%\" ALIGN=\"left\"><br />\n";
if ( $_SESSION['lstMonth'] != "Выберите месяц" )
{
	print "<b>Месяц:</b>&nbsp;";
	print ($_SESSION['Month'][$_SESSION['lstMonth']]."<br />");
}
if ( $_SESSION['lstYear'] != "Выберите год" )
{
	print "<b>Год:</b>&nbsp;";
	print ($_SESSION['lstYear']."<br />");
}
print "<b>Период:</b>&nbsp;";
print ($_SESSION['rbPeriod']."<br />");
print "<p></p>";


//проверка времени
checkTrueTime($_SESSION['DateBegin'], $_SESSION['DateEnd']);

//если есть ошибки- выводим их на экран
if ( count($errors) > 0 )
{
	display_errors();
}
if ( count($errors) == 0 )
{
	switch ($_SESSION['rbTypeReport'])
	{
		case "short":
		ViewTableStatusProject_short(); //краткий отчет - кол-во выполн.работ
		break;
		case "long":
		ViewTableStatusProject_long(); //подробный отчет- проекты с параметрами
		break;
		case "month":
		$result = ViewTableStatusProject_month(); //подробный отчет- месячно-номенклатурный план работ
			$s = serialize($result);    //происходит сериализация массива для передачи по скрытому полю ввода
			print "<form action=\"../Report/report_excel/saveExcel_MonthPlan.php\" method=\"POST\">\n";
			print "<input type=\"image\" name=\"btnSaveExcel\" src=\"../img/file_xls.png\" width=\"48\" height=\"48\" border=\"0\" alt=\"сохранение в Экселе\" />  ";
			print "<div class=\"hint\"><span>Подсказка! </span>Сохранение в Excel</div>";
			print "<input type=\"hidden\" name=\"dataToExcel\" value=\"".htmlspecialchars($s, ENT_QUOTES)."\" />  ";
			print "</form>";
		break;
	}
}
require_once("../end1.php");

//сбросить содержимое буфера
ob_end_flush();
?>
