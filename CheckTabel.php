<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является таблицей	сравнения табельного времени и заполненного сотрудником
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
include_once("modules/lib/lib.php");
//include_once("js/jscript.js");
require_once("begin1.php");
print "<H2>Сравнение табеля и фактического времени</H2><br />\n";
if ($_SESSION['Name_Department']=="САПР")
{
	print "<a href=\"sapr/input.php\">Назад</a><br />\n";
}
else {
	print "<a href=\"index1.php\">Назад</a><br />\n";
}
print "<Hr Width=\"100%\" ALIGN=\"left\"><br />\n";
$arrayTabelTime = getArrDate("26-25");
//получаем список дней с трудочасами
$arrayTime = getArrWorkTime($_SESSION['Id_Employee'], "26-25");
//таблица HTML
print "<div class='g-6'>\n";
print "<table  class=\"f-table-zebra\">\n";
print "<caption>Проверка табельного времени</caption>\n";
print "<thead>\n";
print "<TR class=\"header\" bgcolor=#E8E8E8 align=center>\n";
print "<TH>Дата</TH>\n";
print "<TH>Табельное <br /> время</TH>\n";
print "<TH>Заполненное <br /> время</TH>\n";
print "<TH>Разность</TH>\n";
print "</TR>\n";
print "<thead>\n";

$SumTabel = 0;
$SumTime = 0;
// по каждому табельному дню в месяце заполняем таблицу
foreach ($arrayTabelTime as $index=>$value)
{
	print "<TR align=center>\n";
	//дата
	printZeroTD($index);
	//табельное время
	printZeroTD($value);
	//заполненное время
	printZeroTD($arrayTime[$index]);
	//разница между табельным временем и заполненным
	$Raznost = round($value - $arrayTime[$index],1);
	//если разность табельного времени и заполненного положительна, значит табельное время нужно распределить между работами
	if ($Raznost > 0)
	{
		//выделяем ошибку красным
		printZeroTD($Raznost, "red");
	}
	else {
		//иначе все нормально (затратить время можно больше табельного-напр. домашняя работа)
			printZeroTD($Raznost, "black");
	}
	//суммация табельного времени
	$SumTabel = $SumTabel + $value;
	//суммация заполненного времени
	$SumTime =  $SumTime +  $arrayTime[$index];
}
print "<TR align=center>\n";
print "<TH>Итого:</TH>";
printZeroTH(round($SumTabel,1));
printZeroTH(round($SumTime,1));
//разность между табельным и заполненным временем
$SumRaznost =  round($SumTabel -  $SumTime,1);
if ($SumRaznost > 0)
{
	//выделяем ошибку красным
	printZeroTH($SumRaznost, "red");
}
else {
	//иначе все нормально (затратить время можно больше табкльного-напр. домашняя работа)
		printZeroTH($SumRaznost, "black");
}
print "</table>";
print "</div>";
require_once("end1.php");
?>
