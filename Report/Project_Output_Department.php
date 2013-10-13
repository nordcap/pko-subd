<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей отчета трудозатрат,листов,времени
выполненных в подразделении
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
include_once("../modules/lib/lib.php");
require_once("begin1.php");
include_once("../js/jscript.js");
print "<a href=\"object.php\">Назад</a><br />\n";
print "<H2>Свод трудозатрат отдела по месяцам</H2><br />\n";
print "<p><b>Заказчик:</b>&nbsp ";
if( $_SESSION['lstCustomer'] == "Выбор заказчика" )
{
	print "Все заказчики";
}
else {
	print getNameCustomer($_SESSION['lstCustomer']);
}
print "   </p>";
print "<p><b>Отдел:</b>&nbsp;";
if ( $_SESSION['Department'] != "Выбор отдела" )
{
	print (getNameDepartment($_SESSION['Department'])."<br />");
}
print "   </p>";

print "   <p>";
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
print "   </p>";

print "<p>";
	if( $_SESSION['lstObject'] == "Выбор объекта" )
	{
		print "Все объекты";
	}
	else {
		print "<b>".getNameProject($_SESSION['lstObject'])."</b>";
        print "    <U>".getNameExtProject($_SESSION['lstObject'])."</U>";
	}
print "</p>";


//проверка времени
checkTrueTime($_SESSION['DateBegin'], $_SESSION['DateEnd']);

//если есть ошибки- выводим их на экран
if ( count($errors) > 0 )
{
	display_errors();
}
if ( count($errors) == 0 )
{
	switch ($_SESSION['rbCategry'])
	{
		case "time":
		if (!empty($_SESSION['DateBegin']) OR !empty($_SESSION['DateEnd']))
		{
			$QueryDate = " AND ((design.Date>=".escapeshellarg($_SESSION['DateBegin']).
			") AND (design.Date<".escapeshellarg($_SESSION['DateEnd']+3600*24)."))";
		}
		else {
			$QueryDate = getIntervalDate(
			$_SESSION['lstMonth'],
			$_SESSION['lstYear'],
			"design",
			"Date",
			$_SESSION['rbPeriod']);
		}
		break;
		case "man_hour":
		if (!empty($_SESSION['DateBegin']) OR !empty($_SESSION['DateEnd']))
		{
			$QueryDate = " AND ((design.statusBVP>=".escapeshellarg($_SESSION['DateBegin']).
			") AND (design.statusBVP<".escapeshellarg($_SESSION['DateEnd']+3600*24)."))";
		}
		else {
			$QueryDate = getIntervalDate(
			$_SESSION['lstMonth'],
			$_SESSION['lstYear'],
			"design",
			"statusBVP",
			$_SESSION['rbPeriod']);
		}
		break;
	}
	if ( $_SESSION['lstObject'] == "Выбор объекта" )
	{
		$QueryObject = "";
	}
	else {
		$QueryObject = " AND (project.Id=".escapeshellarg($_SESSION['lstObject']).")";
	}
	//запрос отдела
	$QueryDepartment = selectDepartment();
	//составляем объединение результатов работы нескольких команд SELECT в один набор результатов.
	$Query1= "SELECT design.Date, design.statusBVP, ".
	"employee.Family, employee.Name, employee.Patronymic, ".
	"project.Number_Project, ".
	"department.Name_Department, department.Id, office.Name_Office, office.Id, ".
	"mark.Name_Mark, ".
	"SUM(design.Sheet_A1) AS Sheet_A1, SUM(design.Sheet_A3) AS Sheet_A3, SUM(design.Sheet_A4) AS Sheet_A4, ".
	"SUM(design.Man_Hour_Sum) AS Man_Hour_Sum ".
	"FROM design, employee, project, mark, department, office ".
	"WHERE ".
	"(design.id_Mark = mark.id) AND ".
	"(design.id_Project = project.id) AND ".
	"(design.id_Employee = employee.id) AND ".
	"(employee.id_Department = department.id) AND ".
	"(employee.id_Department <> 19) AND ". //ПСО обрабатывается в позициях а не в формате А1-А4
	"(employee.id_Office = office.id) AND ".
	"(project.Number_Project <> ".escapeshellarg('Заявление').") ";
	if ( $_SESSION['lstCustomer'] != "Выбор заказчика" )
	{
		$QueryCustomer = " AND (project.Id_Customer=".escapeshellarg($_SESSION['lstCustomer']).")";
	}
	else {
		$QueryCustomer = "";
	}
	//определяем, какие данные нам важны- проекты или работники
	switch ($_SESSION['rbCategryAdd'])
	{
		case "project":
		$GroupBy = " GROUP BY project.id,employee.id ORDER BY project.Number_Project";
		break;
		case "employee":
		$GroupBy = " GROUP BY employee.id ORDER BY BINARY employee.Family";
		break;
	}
	//объединение запроса в 1 запрос
	$Query = $Query1.$QueryObject.$QueryDate.$QueryDepartment.$QueryCustomer.$GroupBy;
	//print_r($Query);
	//file_put_contents("query.txt",$Query); 
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print"Выборка не удалась!\n";
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		//таблица HTML
		print "<TABLE  Border=\"3\" CellSpacing=\"0\" CellPadding=\"2\">\n";
		print " <Caption><H4>Объектная карта</Caption>\n";
		//переделал таблицу
		print "<TR bgcolor=#E8E8E8 align=center>\n";
		print "<TH rowspan=2>№№</TH>\n";
		//определяем, какие данные нам важны- проекты или работники
		switch ($_SESSION['rbCategryAdd'])
		{
			case "project":
			print "<TH rowspan=2>Номер объекта</TH>\n";
			break;
			case "employee":
			break;
		}
		print "<TH rowspan=2>Сотрудник</TH>\n";
		print "<TH colspan = 1>Графическая часть</TH>\n";
		print "<TH colspan = 2>Текстовая часть</TH>\n";
		print "<TH rowspan=2>Итого чел.-час</TH>\n";
		print "</TR>\n";
		print "<TR bgcolor=#E8E8E8 align=center>\n";
		print "<TD>Лист А1</TD>\n";
		print "<TD>Лист А3</TD>\n";
		print "<TD>Лист А4</TD>\n";
		print "</TR>\n";
		$Sheet_A1 = 0;
		$Sheet_A3 = 0;
		$Sheet_A4 = 0;
		$Time = 0;
		$ManHourSum = 0;
		$n = 0;
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			$n++;
			print "<TR align=center>\n";
			print "<TD>".$n."</TD>";;
			switch ($_SESSION['rbCategryAdd'])
			{
				case "project":
				print "<TD align=left>{$row['Number_Project']}</TD>";
				break;
				case "employee":
				break;
			}
			print "<TD nowrap align=left>{$row['Family']} ".
			mb_substr($row['Name'],0,1,'utf8').".".
			mb_substr($row['Patronymic'],0,1,'utf8').".</TD>".
			"<TD>".round($row['Sheet_A1'],2)."</TD>".
			"<TD>".round($row['Sheet_A3'],2)."</TD>".
			"<TD>".round($row['Sheet_A4'],2)."</TD>".
			"<TD>".round($row['Man_Hour_Sum'],1)."</TD>\n";
			$Sheet_A1 = $Sheet_A1 + $row['Sheet_A1'];
			$Sheet_A3 = $Sheet_A3 + $row['Sheet_A3'];
			$Sheet_A4 = $Sheet_A4 + $row['Sheet_A4'];
			$ManHourSum = $ManHourSum + $row['Man_Hour_Sum'];
		}
		print "<TR align=center>\n";
		switch ($_SESSION['rbCategryAdd'])
		{
			case "project":
			print "<TH colspan=3 align=right>Итого:</TH>";
			break;
			case "employee":
			print "<TH colspan=2 align=right>Итого:</TH>";
			break;
		}
		printZeroTH(round($Sheet_A1, 1));
		printZeroTH(round($Sheet_A3, 1));
		printZeroTH(round($Sheet_A4, 1));
		printZeroTH(round($ManHourSum, 1));
		print "</Table>\n";
	}
	print "<p></p>";
	print "<div class=\"hint\"><span>Подсказка! </span>Сметный отдел не учитывается</div>";
	print "<p></p>";
	print "<Table  Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\">\n";
	print "<Tr>\n";
	print "<Td>";
	print "Начальник отдела: ";
	print "<Td>";
	print "<br />";
	print "<Hr Width=\"250\" ALIGN=left>\n";
	print "<br />";
	print "</Table>\n";
	unset($_SESSION['DateBegin']);
	unset($_SESSION['DateEnd']);
}
require_once("../end1.php");
?>