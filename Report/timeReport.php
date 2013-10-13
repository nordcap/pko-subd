<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей отчета "затраты времени"
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
include_once("../modules/lib/lib.php");
require_once("begin1.php");
include_once("../js/jscript.js");
//инициализация массива времени
print "<a href=\"object.php\">Назад</a><br />\n";
print "<a href=\"../Graph/graphReport.php?Type=timeReport\">График загрузки подразделений по месяцам</a><br />\n";
print "<CENTER><H2>Объектная карточка 'Затраты времени'</H2></CENTER><br />\n";
$array_timeReport = InitializationMatrixCard();

$arraySum = InitializationSumCard(13);
print "<Table  Width=\"100%\" Border=\"1\" CellSpacing=\"0\" CellPadding=\"2\">\n";
print "<Tr>\n";
print "<Td>";
print "Объект: ";
if( $_SESSION['lstObject'] == "Выбор объекта" )
{
	print "Все объекты";
}
else {
	print getNameProject($_SESSION['lstObject']);
}
print "<Td>";
print "Заказчик: ";
if( $_SESSION['lstCustomer'] == "Выбор заказчика" )
{
	print "Все заказчики";
}
else {
	print getNameCustomer($_SESSION['lstCustomer']);
}
print "<Td>";
print "Текущая дата ".date("d.m.Y");
print "<Td>";
print "Год ".$_SESSION['lstYear'];
print "<Tr>\n";
print "<Td colspan=4>";
if($_SESSION['lstObject']=="Выбор объекта")
{
	print "";
}
else {
	print "<U>".getNameExtProject($_SESSION['lstObject'])."</U>";
}
print "<Tr>\n";
print "<Td colspan=2>";
print "Стадия&nbsp";
print "<Td>";
print "Начало&nbsp";
print "<Td>";
print "Окончание&nbsp";
print "</Table>\n";
print "<br /><br />";
//составляем объединение результатов работы нескольких команд SELECT в один набор результатов.
//таблица HTML
print "<TABLE Width=\"100%\" Border=\"3\" CellSpacing=\"0\" CellPadding=\"2\">\n";
print "<TR>\n";
print "<TH colspan=3 rowspan=2>Наименование</TH>".
"<TH colspan=12>Месяцы</TH>".
"<TH rowspan=2>Итого, часов</TH>".
"<TR>\n".
"<TH>январь</TH>".
"<TH>февраль</TH>".
"<TH>март</TH>".
"<TH>апрель</TH>".
"<TH>май</TH>".
"<TH>июнь</TH>".
"<TH>июль</TH>".
"<TH>август</TH>".
"<TH>сентябрь</TH>".
"<TH>октябрь</TH>".
"<TH>ноябрь</TH>".
"<TH>декабрь</TH>";
//заполнение двумерного массива значениями выборки по всему году
// САПРиГП=>0,0,50,70,0.......
// Технологический=>10,0,0,60,0.......
// Руководство=>0,30,0,0,3.......
for ($i=1; $i<13; $i++)
{
	$Query1 = "SELECT department.Name_Department, SUM(design.Time) AS sumTime ".
	"FROM design,employee,department,project ".
	"WHERE ".
	"((design.id_Employee = employee.Id) AND ".
	"(department.Id = employee.id_Department) AND ".
	"(department.Id <> 22) AND (department.Id <> 24) AND (department.Id <> 25) AND (department.Id <> 26) AND ".
	"(design.id_Project = project.Id) AND ";
	$Query2 = "(project.Id={$_SESSION['lstObject']}) AND ";
	$Query3 = "(design.Date>={$_SESSION['arrayMonth'][$i]}) AND (design.Date<{$_SESSION['arrayMonth'][$i+1]})) ".
	"GROUP BY department.id ";
	if( $_SESSION['lstObject'] != "Выбор объекта" )
	{
		$Query2 = "(project.Id={$_SESSION['lstObject']}) AND ";
	}
	else {
		$Query2 = "";
	}
	if( $_SESSION['lstCustomer'] != "Выбор заказчика" )
	{
		$Query4 = "(project.Id_Customer=".escapeshellarg($_SESSION['lstCustomer']).") AND ";
	}
	else {
		$Query4 = "";
	}
	//результирующий запрос
	$Query = $Query1.$Query2.$Query4.$Query3;
	//file_put_contents("query.txt",$Query);
    //print($Query);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		print "Выборка не удалась!\n";
	}
	else 	{
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			$array_timeReport[$row['Name_Department']][$i] = round($row['sumTime'], 0);
		}
	}
}
//заполнение таблицы
foreach($array_timeReport as $Name=>$Year)
{
	print "<TR align=center>\n";
	print "<TD colspan=3>$Name</TD>";
	foreach($Year as $key=>$Month)
	{
		printZeroTD($Month);
		$arraySum[$key] = $Month + $arraySum[$key];
	}
	$sum = array_sum($Year);
	printZeroTH($sum);
	$arraySum[$key + 1] = $sum + $arraySum[$key + 1];
}
print "<TR align=center>\n";
print "<TD colspan=3><b>Итого</b></TD>";
foreach($arraySum as $key=>$SumMonth)
{
	printZeroTH($SumMonth);
}
print "</TABLE>\n";
$_SESSION['graphData'] = array_slice($arraySum, 0, count($arraySum) - 1);
require_once("../end1.php");
?>