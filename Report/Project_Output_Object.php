<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей отчета трудозатрат работ по объектам
по всем подразделениям
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
error_reporting(0);
session_start();
$errors = array(); //объявляется массив ошибок

include_once("../modules/lib/lib.php");
require_once("begin1.php");
include_once("../js/jscript.js");
print "<a href=\"object.php\">Назад</a><br /><br />\n";
print "<a href=\"../Graph/graphReport.php?Type=Project_Output_Object\">График загрузки подразделений</a>\n";
print "<p></p>   ";

//проверка времени
checkTrueTime($_SESSION['DateBegin'], $_SESSION['DateEnd']);

//если есть ошибки- выводим их на экран
if ( count($errors) > 0 )
{
	display_errors();
}
if ( count($errors) == 0 )
{

	//в зависимости от выбора группирования меняем запрос
	switch ($_SESSION['rbCategry'])
	{
		case "man_hour":
		print "<CENTER><h2>Свод трудозатрат отделов (общие)</h2></CENTER><hr />\n";
		break;
		case "project_output":
		print "<CENTER><h2>Свод трудозатрат отделов (по выдаче в БВП)</h2></CENTER><hr />\n";
		break;
		case "project_output_archive":
		print "<CENTER><h2>Свод трудозатрат отделов (в архиве)</h2></CENTER><hr />\n";
		break;
		case "time":
		print "<CENTER><h2>Свод по фактически затраченному времени<H2></h2></CENTER><hr />\n";
		break;
	}
	
	print "<b>Месяц:</b>&nbsp;";
	print ($_SESSION['Month'][$_SESSION['lstMonth']]."<br />");


	print "<b>Год:</b>&nbsp;";
	print ($_SESSION['lstYear']."<br />");

	print "<b>Период:</b>&nbsp;";
	print ($_SESSION['rbPeriod']."<br />");
	print "<p></p>";
	
	print "Заказчик:&nbsp;";
	if ( $_SESSION['lstCustomer'] == "Выбор заказчика" )
	{
		print "Все заказчики";
	}
	else {
		print getNameCustomer($_SESSION['lstCustomer']);
	}
	switch ($_SESSION['rbCategry'])
	{
		case "man_hour":
		$sum = " SUM(design.Man_Hour_Sum) AS Man_Hour_Sum ";
		if (!empty($_SESSION['DateBegin']) OR !empty($_SESSION['DateEnd']))
		{
			$Query2 = " AND ((design.Date>=".escapeshellarg($_SESSION['DateBegin']).
			") AND (design.Date<".escapeshellarg($_SESSION['DateEnd']+3600*24)."))";
		}
		else {
			$Query2 = getIntervalDate(
			$_SESSION['lstMonth'],
			$_SESSION['lstYear'],
			"design",
			"Date",
			$_SESSION['rbPeriod']);
		}
		break;

		case "project_output":
		$sum = " SUM(design.Man_Hour_Sum) AS Man_Hour_Sum ";
		 if (!empty($_SESSION['DateBegin']) OR !empty($_SESSION['DateEnd']))
		{
			$Query2 = " AND ((design.statusBVP>=".escapeshellarg($_SESSION['DateBegin']).
			") AND (design.statusBVP<".escapeshellarg($_SESSION['DateEnd']+3600*24)."))";
		}
		else {
			$Query2 = getIntervalDate(
			$_SESSION['lstMonth'],
			$_SESSION['lstYear'],
			"design",
			"statusBVP",
			$_SESSION['rbPeriod']);
		}
		break;

		case "project_output_archive":
		$sum = " SUM(design.Man_Hour_Sum) AS Man_Hour_Sum ";
		if (!empty($_SESSION['DateBegin']) OR !empty($_SESSION['DateEnd']))
		{
			$Query2 = " AND ((design.approvalBVP>=".escapeshellarg($_SESSION['DateBegin']).
			") AND (design.approvalBVP<".escapeshellarg($_SESSION['DateEnd']+3600*24)."))";
		}
		else {
			$Query2 = getIntervalDate(
			$_SESSION['lstMonth'],
			$_SESSION['lstYear'],
			"design",
			"approvalBVP",
			$_SESSION['rbPeriod']);
		}
		break;
		
		
		case "time":
		$sum = " SUM(design.Time) AS Man_Hour_Sum ";
		 if (!empty($_SESSION['DateBegin']) OR !empty($_SESSION['DateEnd']))
		{
			$Query2 = " AND ((design.statusBVP>=".escapeshellarg($_SESSION['DateBegin']).
			") AND (design.statusBVP<".escapeshellarg($_SESSION['DateEnd']+3600*24)."))";
		}
		else {
			$Query2 = getIntervalDate(
			$_SESSION['lstMonth'],
			$_SESSION['lstYear'],
			"design",
			"statusBVP",
			$_SESSION['rbPeriod']);
		}		
		break;
	}
	//составляем объединение результатов работы нескольких команд SELECT в один набор результатов.
	//таблица HTML
	print "<br /><br />\n";
	//print "<div class=\"biglayer\">\n";
	//определим список отделов для передачи в  InitializationMatrixCardProject
	$listDepartment = getArrayDepartment();
	//удаляем отделы которые не учавствуют в работах (геодезия)
	unset($listDepartment[26]); //ГД-геодезия
	unset($listDepartment[24]);    //удаление из списка отделов ИТ

	//заполнение массива "отделы" нулями
	//порядок отделов жестко закреплен(из базы). При перестановке работать корректно не будет
	$arraySum = array(
	'ТО'    => 0,
	'КИПиА' => 0,
	'СТРО'  => 0,
	'ГП'    => 0,
	'ПСО'   => 0,
	'ВКиОВ' => 0,
	'КО'    => 0,
	'ЭО'    => 0
	);



	//если расчет идет  расчет сметы по %
	if ($_SESSION['SmetaCalc'] == TRUE)
	{
		if ($_SESSION['Smeta'] >= 0 OR empty($_SESSION['Smeta']))
		{
			$PSO = FALSE;    //флаг поиска по ПСО
		}
	}  else 
	{
		$PSO = TRUE; //если не выбран расчет сметы по %
	}
	//при указании % сметы, в запрос включается доп условие

	if ($PSO)
	{
		$QueryPSO = "";
	}
	else {
		$QueryPSO = "(department.id <> 19) AND ";
		unset($listDepartment[19]);    //удаление из списка отделов ПСО
		//удаляем из списка ПСО
		unset($arraySum['ПСО']);
	}
	//print_r($listDepartment);
	$Query1 = "SELECT project.Number_Project, project.Name_Project, department.Name_Department, customer.Number_Customer, customer.Name_Customer, ".
	$sum.
	" FROM design,employee,department,project,customer ".
	" WHERE ".
	" (design.id_Employee = employee.Id) AND ".
	" (employee.id_Department = department.Id) AND ".
	" (project.id_Customer = Customer.Id) AND ".
	" (design.id_Project = project.Id) AND ".
	" (Primary_Department = 'TRUE') AND ".
	$QueryPSO. //кроме сметного отдела
	/*"(department.id <> 22) AND ".    //кроме руководства
	"(department.id <> 24) AND ".    //кроме ИТ
	"(department.id <> 25) AND ".    //кроме бюро ТЭОиСДР*/
	" (department.id <> 26) ";    //кроме ГД-геодезии

	//" (design.Man_Hour_Sum>".escapeshellarg('0').") "; //сумма работ должна быть больше нуля
	if ( $_SESSION['lstCustomer'] != "Выбор заказчика" )
	{
		$Query4 = " AND (project.Id_Customer=".escapeshellarg($_SESSION['lstCustomer']).")";
	}
	else {
		$Query4 = "";
	}
	$Query3 = " GROUP BY project.id, department.id ";
	$Query5 = "	ORDER BY BINARY customer.Name_Customer, project.Number_Project ";
	//результирующий запрос
	$Query = $Query1.$Query2.$Query4.$Query3.$Query5;
	//print_r($Query);
	
	// file_put_contents("query.txt",$Query);
	//определяем массив из пар номер проекта-отдел
	$arrayDepartment = InitializationMatrixCardProject($Query2, $listDepartment);
	unset($arrayCustomer);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			//записываем в массив данные по каждому месяцу- округлим до 2 знака после запятой
			$arrayDepartment["{$row['Number_Customer']}-{$row['Number_Project']}&nbsp;{$row['Name_Project']}"][$row['Name_Department']] = round($row['Man_Hour_Sum'],0);
			$arrayCustomer["{$row['Number_Customer']}-{$row['Number_Project']}&nbsp;{$row['Name_Project']}"] = $row['Name_Customer'];
		}
	}
	/*print "<pre>";
	print_r ($arrayDepartment);
	print "</pre>";*/
	if ($PSO)
	{
		$TDPSO = "<TH>ПСО</TH>";
		$TD_nullPSO = "";                           //работаем по базу
		} else {
		$TDPSO = "";
		$TD_nullPSO = "<TH>ПСО</TH>";     //работает с вычисляемым %
	}
	$n=0;
	//печать таблицы
	print "<TABLE Width=\"100%\" Border=\"2\" CellSpacing=\"0\" CellPadding=\"2\">\n";
	print "<TR bgcolor = #E8E8E8>\n";
	print "<TH rowspan=2>№№</TH>".
	"<TH rowspan=2>Наименование</TH>".
	"<TH rowspan=2>Заказчик</TH>".
	"<TH colspan=8>Трудозатраты отделов на выполнение</TH>".
	"<TH rowspan=2>Итого</TH>".
	"<TR bgcolor = #E8E8E8>\n".
	"<TH>ТО</TH>".
	"<TH>КИПиА</TH>".
	"<TH>СТРО</TH>".
	"<TH>ГП</TH>".
	$TDPSO.
	"<TH>ВКиОВ</TH>".
	"<TH>КО</TH>".
	"<TH>ЭО</TH>".
	$TD_nullPSO;
	foreach($arrayDepartment as $NumberProject=>$Departments)
	{
		//если проект не используется каким-либо из отделов то строку в результат не включаем
		if ( array_sum($Departments) <> 0 )
		{
			print "<TR align=center>\n";
			printZeroTD(++$n);
			print("<td align=left><b>".substr($NumberProject,0,strpos($NumberProject,"&nbsp;"))."</b>".
			"<sub>".strstr($NumberProject, "&nbsp;")."</sub>"."</td>");                 //номер проекта
			print("<td align=left><sub>".$arrayCustomer[$NumberProject]."</sub>"."</td>"); //заказчик

			
			foreach($Departments as $key=>$Department)
			{
				printZeroTD($Department); //трудозатраты отдела
				$arraySum[$key] = $Department + $arraySum[$key];//суммарные труд-ты отдела
			}
			$sum = array_sum($Departments);  //суммарные труд-ты отделов на проект
			//столбец сметного бюро
			if ($PSO === FALSE )
			{
				if (empty($_SESSION['Smeta']))
				{
					$smeta =0;
				}
				else {
					$smeta = round(($_SESSION['Smeta']*$sum) / 100, 0);
				}
				printZeroTD($smeta);//сметный
				$arraySum['ПСО'] = $smeta + $arraySum['ПСО'];
			}
			printZeroTH($sum + $smeta);  //итого со сметой
			$arraySum['all'] = $sum + $smeta + $arraySum['all'];
		}
	}
	/*print "<pre>-----------";
	print_r ($arraySum);
	print "</pre>";*/
	//подводим итоги
	print "<TR align=center>\n";
	print "<TD colspan=3><b>Итого</b></TD>";
	//печатаем итого
	foreach($arraySum as $key=>$SumDepartment)
	{
		printZeroTH($SumDepartment);
	}
	print "</TABLE>\n";
	$_SESSION['graphData'] = array_slice($arraySum, 0, count($arraySum) - 1);
	unset($_SESSION['rbPeriod']);

}

require_once("../end1.php");

?>