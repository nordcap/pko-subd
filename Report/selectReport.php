<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является служебным, предназначен для диспечеризации отчетов
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
include_once("../modules/lib/lib.php");
//определим массив с значениями месяцев в сек заданного года
if ( $_REQUEST['lstYear'] == "Выберите год" )
{
	$date_time_array = getdate();
	$lstYear = $date_time_array['year'];
}
else {
	$lstYear = $_REQUEST['lstYear'];
}

//заносим в сесиию временной интервал пользовательского ввода
if ( isset($_REQUEST['getData4']) OR
	isset($_REQUEST['getData2']) OR
	isset($_REQUEST['getData9']) OR
	isset($_REQUEST['getData5']))
	{
		$arrInterval = set_time_interval($_REQUEST['DateBegin'], $_REQUEST['DateEnd']);
		$_SESSION['DateBegin'] = $arrInterval[0];
		$_SESSION['DateEnd'] = $arrInterval[1];
	}

//записываем переменные в сессию т.к. используется промежуточная страница
//и $_REQUEST в след.странице будет недоступен
$_SESSION['arrayMonth'] = getArrayTimeYear($lstYear, $_REQUEST['rbPeriod']);
$_SESSION['rbPeriod'] = $_REQUEST['rbPeriod'];
$_SESSION['lstMonth'] = $_REQUEST['lstMonth'];
$_SESSION['lstYear'] = $_REQUEST['lstYear'];

$_SESSION['chkYear'] = $_REQUEST['chkYear'];

$_SESSION['lstObject'] = $_REQUEST['lstObject'];
$_SESSION['lstCustomer'] = $_REQUEST['lstCustomer'];
$_SESSION['Office'] = $_REQUEST['Office'];
$_SESSION['Department'] = $_REQUEST['Department'];
$_SESSION['rbCategry'] = $_REQUEST['rbCategry'];
$_SESSION['rbCategryAdd'] = $_REQUEST['rbCategryAdd'];
$_SESSION['rbTypeReport'] = $_REQUEST['rbTypeReport'];
$_SESSION['Smeta'] = $_REQUEST['Smeta'];
$_SESSION['SmetaCalc'] = $_REQUEST['SmetaCalc'];
$_SESSION['K_Graph'] = $_REQUEST['K_Graph'];
$_SESSION['K_Text'] = $_REQUEST['K_Text'];
$_SESSION['chkstatusSearch'] = $_REQUEST['chkstatusSearch'];
$_SESSION['rbReport'] = $_REQUEST['rbReport'];
$_SESSION['chkYearSearch'] = $_REQUEST['chkYearSearch'];
//если в отчете выбран месяц, то он по умолчанию должен быть активен в панели календаря
if ($_SESSION['lstMonth'] > 0)
{
	$_SESSION['pageMonth'] = $_SESSION['lstMonth'];
}
if ($_SESSION['lstYear'] > 0)
{
	$_SESSION['pageYear'] = $_SESSION['lstYear'];
}
//если нажата 1 кпопка, то перенаправляем на следующие отчеты  -Сводные данные за год по отделам
if ( isset($_REQUEST['getData1']) )
{
	switch ( $_REQUEST['rbReport'] )
	{
		case "time":
		header("Location:timeReport.php");
		break;

		case "man_hour":
		header("Location:hourReport.php");
		break;

		case "sheet_a1":
		header("Location:ReportA1.php");
		break;

		case "sheet_a4":
		header("Location:ReportA4.php");
		break;

		case "project_output":
		header("Location:ProjectOutput.php");
		break;

		case "project_outputA1":
		header("Location:ProjectOutputA1.php");
		break;

		case "project_outputA4":
		header("Location:ProjectOutputA4.php");
		break;

		case "project_output_allocation":
		header("Location:project_output_allocation.php");
		break;

		case "project_output_archive":
		header("Location:ProjectOutput_Archive.php");
		break;
	}
} elseif(isset($_REQUEST['getData11']))
{
  $_SESSION['rbShema'] = $_REQUEST['rbShema'];
	header("Location:ChartReport.php");
}
//если нажата 2 кпопка, то перенаправляем на следующие отчеты-Свод трудозатрат отделов по объектам
elseif ( isset($_REQUEST['getData2']) )
{
	$_SESSION['rbReport'] = $_SESSION['rbCategry'];
	header("Location:Project_Output_Object.php");
}
//перенаправляем на следующие отчеты-Свод трудозатрат УПИРиИ по объектам
elseif ( isset($_REQUEST['getData5']) )
{
	header("Location:Project_Output_ObjectAll.php");
}
//перенаправляем на следующий отчет - статистика по методике ДКС
elseif ( isset($_REQUEST['getData6']) )
{
	header("Location:Project_Output_DKS.php");
}
//если нажата 3 кпопка, то
//перенаправляем на следующий отчет - сводные данные по выработке(отправляется в ПЭБ)
	elseif ( isset($_REQUEST['getData3']) )
{
	header("Location:departmentReport.php");
}
//если нажата 4 кпопка, то перенаправляем на следующие отчеты-Свод трудозатрат отдела по месяцам
elseif ( isset($_REQUEST['getData4']) )
{
	header("Location:Project_Output_Department.php");
}
//если нажата 7 кпопка, то перенаправляем на следующие отчеты-Отчет табельного времени
elseif ( isset($_REQUEST['getData7']) )
{
	header("Location:ReportTabel.php");
}
//если нажата 8 кпопка, то перенаправляем на следующие отчеты-План отдела
elseif ( isset($_REQUEST['getData8']) )
{
	header("Location:ReportPlanDepartment.php");
}
//если нажата 9 кпопка, то перенаправляем на следующие отчеты-распределение по типам работ
elseif ( isset($_REQUEST['getData9']) )
{
	header("Location:ReportStatusProject.php");
}
//если нажата 9 кпопка, то перенаправляем на следующие отчеты-распределение по типам работ
elseif ( isset($_REQUEST['getData10']) )
{
	header("Location:ReportTabelAll.php");
}
//если нажата 9 кпопка, то перенаправляем на следующие отчеты-распределение по типам работ
elseif ( isset($_REQUEST['getDataPerformance']) )
{
	header("Location:ReportPerformance.php");
}
//перенаправляем на следующие отчет определение нормы трудоемкости работ
elseif ( isset($_REQUEST['btnNorma']) )
{
	header("Location:ReportNorma.php");
}
?>