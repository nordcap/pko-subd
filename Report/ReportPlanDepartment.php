<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей отчета "План отдела"
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
print "<a href=\"object.php\">Назад</a><br />\n";
print "<H2>План отдела</H2><br />\n";
$errors = array(); //объявляется массив ошибок
//отдел должен указываться всегда, иначе получаются размытые данные
if ($_SESSION['Department'] == "Выбор отдела")
{
	$errors[] = "<H3>Необходимо указать отдел</H3>\n";
}
if ( count($errors) > 0 )
{
	display_errors();
	return;
}
print "<p></p>";
//======================= Модуль постраничного вывода информации на экран ========================
SheetOutput();
//======================= Конец модуля постраничного вывода информации на экран ========================
print "<p></p>";
//рисуем таблицу данных
HeadTable();
print "<p></p>";
//вызываем проверку на наличие записей в табл. Plan_Employee
$res = checkRecordPlan_Employee();
print "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\">\n";
if ($res>0)
{
	//выбираем данные с табл. Plan_Employee и формируем таблицу
	$ArrayEmployee = DrawTable_ReportPlanDepartmentUpdate();
}
else {
	//формируем таблицу из списка сотрудников отдела
	$ArrayEmployee = DrawTable_ReportPlanDepartmentInsert();
}
switch ($_REQUEST['OK'] )
{
	case "UPDATE":
	//происходит обновление записей в табл. Plan_Employee
	foreach($ArrayEmployee as $id=>$valueCorrection)
	{
		$QueryUpdate = "UPDATE plan_employee SET Correction=".escapeshellarg($valueCorrection).
		" WHERE plan_employee.id=".escapeshellarg($id);
		if ( !($dbResult = mysql_query($QueryUpdate, $link)) )
		{
			print "запрос $QueryUpdate не выполнен\n".mysql_error();
		}
	}
	//после обновления данных перегружаем страницу и данные берутся уже из табл. Plan_Employee
	header("Location:ReportPlanDepartment.php");
	break;
	case "INSERT":
	foreach($ArrayEmployee as $id=>$valuePlan)
	{
		//происходит вставка новых записей в табл. Plan_Employee
		$QueryInsert = "INSERT INTO plan_employee SET ".
		"  id_Month = ".escapeshellarg($_SESSION['pageMonth']).
		", id_Year = (SELECT id FROM Year WHERE Year.Year=".escapeshellarg($_SESSION['pageYear']).")  ".
		", id_Employee = ".escapeshellarg($id).
		", Correction =".escapeshellarg($valuePlan);
		//print($QueryInsert);
		if ( !($dbResult = mysql_query($QueryInsert, $link)) )
		{
			print "запрос $QueryInsert не выполнен\n".mysql_error();
		}
	}
	//после добавления данных перегружаем страницу и данные берутся уже из табл. Plan_Employee
	header("Location:ReportPlanDepartment.php");
	break;
	default:
}
print("</form>");
require_once("../end1.php");
//сбросить содержимое буфера
ob_end_flush();
?>
