<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является библиотекой функций применяемых в приложении
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
//session_start();
include_once("connect.php");
error_reporting(0);
//массив представляющий собой список месяцев в году
//include_once("Tabel_Time.php");
/*
*****************************************************************************************************
*************************************<< Функции отображения таблиц >>********************************
*****************************************************************************************************
*/
// ViewTableDesign()- функция отображения рабочей таблицы
// Входные параметры:
//************************************************************************************************

function ViewTableDesign()
{
	global $link; //переменная соннекта с базой
	//суммарное время затраченное на работу
	$SumTime = $_REQUEST['Time1'] + //время на работу форм.А1
	$_REQUEST['Time3'] +  //время на работу форм.А3
	$_REQUEST['Time4']+	//время на работу форм.А4
	$_REQUEST['Time_Collection'] +  //время на сбор данных
	$_REQUEST['Time_Agreement'] +  //время на согласование
	$_REQUEST['prov'];	//время на проверку
	//суммарные  трудозатраты на работу
	$SumManHour = $_REQUEST["Сf_Sheet"] * (
	($_REQUEST['Man_Hour1'] * $_REQUEST['Sheet_A1'] * $_REQUEST['Сf_Man_Hour1']) +
	($_REQUEST['Man_Hour3'] * $_REQUEST['Sheet_A3'] * $_REQUEST['Сf_Man_Hour3']) +
	($_REQUEST['Man_Hour4'] * $_REQUEST['Sheet_A4'] * $_REQUEST['Сf_Man_Hour4'])) +
	($_REQUEST['Time_Collection'] + $_REQUEST['Time_Agreement']);
	//составление запроса в зависимости от вида операции

	switch ($_REQUEST['rbselect'])
	{
		case "select":
		//выборка всей базы
		break;
		case "insert":
		//вставка новой записи
		$Query = "INSERT INTO design SET Date=".escapeshellarg($_REQUEST['Date']).
		", id_Employee=".escapeshellarg($_SESSION['Id_Employee']).
		", id_Project=".escapeshellarg($_REQUEST['Number_Project']).
		", id_Work=".escapeshellarg($_REQUEST['Name_Work']).
		", Sheet_A1=".escapeshellarg($_REQUEST['Sheet_A1']).
		", Sheet_A3=".escapeshellarg($_REQUEST['Sheet_A3']).
		", Sheet_A4=".escapeshellarg($_REQUEST['Sheet_A4']).
		", k_Sheet=".escapeshellarg($_REQUEST['Сf_Sheet']).
		", Time1=".escapeshellarg($_REQUEST['Time1']).
		", Time3=".escapeshellarg($_REQUEST['Time3']).
		", Time4=".escapeshellarg($_REQUEST['Time4']).
		", Man_Hour1=".escapeshellarg($_REQUEST['Man_Hour1']).
		", Man_Hour3=".escapeshellarg($_REQUEST['Man_Hour3']).
		", Man_Hour4=".escapeshellarg($_REQUEST['Man_Hour4']).
		", Time_Collection=".escapeshellarg($_REQUEST['Time_Collection']).
		", Time_Agreement=".escapeshellarg($_REQUEST['Time_Agreement']).
		", id_Mark=".escapeshellarg($_REQUEST['Name_Mark']).
		", Time=".escapeshellarg($SumTime).
		", prov=".escapeshellarg($_REQUEST['prov']).//время на проверку объекта
		", num_cher=".escapeshellarg($_REQUEST['num_cher']).//номер чертежа-не обязательное поле
		", Man_Hour_Sum=".escapeshellarg($SumManHour).
		", checkBVP=".escapeshellarg($_REQUEST['checkBVP']). //сдано на проверку
		", statusBVP=".escapeshellarg('0'); //при вставке работа еще не является утвержденной

    if ( !($dbResult = mysql_query($Query, $link)) )
		{
			print "запрос $Query не выполнен\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "update":
		//обновление записи
		$Comma_separated = implode(",", $_REQUEST['design']);
		$Query = "UPDATE design SET Date=".escapeshellarg($_REQUEST['Date']).
		", id_Employee=".escapeshellarg($_SESSION['Id_Employee']).
		", id_Project=".escapeshellarg($_REQUEST['Number_Project']).
		", id_Work=".escapeshellarg($_REQUEST['Name_Work']).
		", Sheet_A1=".escapeshellarg($_REQUEST['Sheet_A1']).
		", Sheet_A3=".escapeshellarg($_REQUEST['Sheet_A3']).
		", Sheet_A4=".escapeshellarg($_REQUEST['Sheet_A4']).
		", k_Sheet=".escapeshellarg($_REQUEST['Сf_Sheet']).
		", Time1=".escapeshellarg($_REQUEST['Time1']).
		", Time3=".escapeshellarg($_REQUEST['Time3']).
		", Time4=".escapeshellarg($_REQUEST['Time4']).
		", Man_Hour1=".escapeshellarg($_REQUEST['Man_Hour1']).
		", Man_Hour3=".escapeshellarg($_REQUEST['Man_Hour3']).
		", Man_Hour4=".escapeshellarg($_REQUEST['Man_Hour4']).
		", Time_Collection=".escapeshellarg($_REQUEST['Time_Collection']).
		", Time_Agreement=".escapeshellarg($_REQUEST['Time_Agreement']).
		", id_Mark=".escapeshellarg($_REQUEST['Name_Mark']).
		", Time=".escapeshellarg($SumTime).
		", prov=".escapeshellarg($_REQUEST['prov']). //Добавил Ден
		", num_cher=".escapeshellarg($_REQUEST['num_cher']). //Добавил Ден
		", Man_Hour_Sum=".escapeshellarg($SumManHour).
		", checkBVP=".escapeshellarg($_REQUEST['checkBVP']).
		", statusBVP=".escapeshellarg('0').
		"  WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "hand_all":
		//сдать на проверку все выделенные записи
		$Comma_separated = implode(",", $_REQUEST['design']);
		$Query = "UPDATE design SET checkBVP=".escapeshellarg('TRUE').
		"  WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print("запрос $Query не выполнен\n");
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "delete":
		//удалить выбранные записи
		$Comma_separated = implode(",", $_REQUEST['design']);
		$Query = "DELETE FROM design WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
	}
	//======================= Модуль постраничного вывода информации на экран ========================
  	ListMonthYear();
	$arrayMonth = getArrayTimeYear($_SESSION['pageYear']);
	//======================= Конец модуля постраничного вывода информации на экран ========================
	$Query1 = "SELECT DISTINCTROW design.Id, design.Date, design.checkBVP, design.statusBVP, ".
	"employee.Family, employee.Name, employee.Patronymic, ".
	"project.Number_Project, mark.Name_Mark, design.k_Sheet, ".
	"design.Time1, design.Sheet_A1, design.Man_Hour1, ".
	"design.Time3, design.Sheet_A3, design.Man_Hour3, design.num_cher, ". //Добавил design.num_cher Ден
	"design.Time4, design.Sheet_A4, design.Man_Hour4, design.prov, ".//Добавил design.prov, Ден
	"design.Time_Collection, design.Time_Agreement, design.Time, design.Man_Hour_Sum ".
	"FROM design, employee, project, mark ".
	"WHERE ".
	"((design.id_Mark = mark.id) AND ".
	"(design.id_Project = project.id) AND ".
	"(design.id_Employee = employee.id)) AND (employee.id=".escapeshellarg($_SESSION['Id_Employee']).") ";
	$Query2 = " AND ((design.Date>=".escapeshellarg($arrayMonth[$_SESSION['pageMonth']]).
	") AND (design.Date<".escapeshellarg($arrayMonth[$_SESSION['pageMonth']+1])."))";
	$Query3 = " ORDER BY design.Date DESC";
	//соединяем все запросы в один
	$Query = $Query1.$Query2.$Query3;
	//file_put_contents("query.txt",$Query);
	//результирующая таблица
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		//listSession();
	}
	else {
		//print "<div class=\"layer\">\n";
		print "<table  class=\"f-table-zebra\">\n";
        print "<caption>Объектная карта</caption>";
		print "<thead>\n";
		//переделал таблицу
		print "<TR>\n";
		print "<TH rowspan=2> Метка</TH>\n";
		print "<TH rowspan=2>Дата</TH>\n";
		print "<TH rowspan=2>Номер объекта</TH>\n";
		print "<TH rowspan=2>Номер чертежа</TH>\n";
		print "<TH rowspan=2>Марка</TH>\n";
		print "<TH colspan=3>Графическая часть</TH>\n";
		print "<TH colspan=6>Текстовая часть</TH>\n";
		print "<TH rowspan=2>К-т заполн листа</TH>\n";
		print "<TH rowspan=2>Сбор данных</TH>\n";
		print "<TH rowspan=2>Согл.-ие</TH>\n";
		if ( ($_SESSION['Name_Post'] == "Экономист" ) OR
		($_SESSION['Name_Post'] == "Начальник управления") OR
		($_SESSION['Name_Post'] == "Начальник отдела") OR
		($_SESSION['Name_Post'] == "Зам.начальника отдела") OR
		($_SESSION['Name_Post'] == "Начальник бюро") OR
		($_SESSION['Name_Post'] == "Администратор"))
		{
			print "<TH rowspan=2>Проверка</TH>\n";
		}
		print "<TH rowspan=2>Итого часов <br />(табель)</TH>\n";
		print "<TH rowspan=2>Итого чел.-час<br />(трудозатраты)</TH>\n";
		print "<TH rowspan=2>Сдача на проверку</TH>\n";
		print "<TH rowspan=2>Дата утверждения</TH>\n";
		print "</TR>\n";
		print "<TR>\n";
		print "<TH>Лист А1</TH>\n";
		print "<TH>Факт. время</TH>\n";
		print "<TH>Труд.-ты</TH>\n";
		print "<TH>Лист А3</TH>\n";
		print "<TH>Факт. время</TH>\n";
		print "<TH>Труд.-ты</TH>\n";
		print "<TH>Лист А4</TH>\n";
		print "<TH>Факт. время</TH>\n";
		print "<TH>Труд.-ты</TH>\n";
		print "</TR>\n";
		print "</thead>";
		$Sheet_A1 = 0;
		$Time1 = 0;
		$Sheet_A3 = 0;
		$Time3 = 0;
		$Sheet_A4 = 0;
		$Time4 = 0;
		$Time_Collection = 0;
		$Time_Agreement = 0;
		$prov = 0;
		$Time = 0;
		$ManHourSum = 0;
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			//если выбрано копирование, то выделяем красным
			if ( $_REQUEST['rbselect'] == "copy" AND
			FALSE !== array_search($row['Id'], $_REQUEST['design'], TRUE))
			{
				print "<TR align=center style=\"background-color:#FF7F50\">\n";
			}
			else {
				print "<TR align=center>\n";
			}
			if ($_REQUEST['rbselect'] == "select_all")
			{
				print "<TD><input name=\"design[]\" type=\"checkbox\" value=\"{$row['Id']}\" checked></TD>\n";
			}
			else {
				print "<TD><input name=\"design[]\" type=\"checkbox\" value=\"{$row['Id']}\"></TD>\n";
			}
			print "<TD>".strftime("%d.%m.%y",$row['Date']+ $_SESSION['localTime'] * 3600)."</TD>\n";
			printZeroTD($row['Number_Project']);
			printZeroTD($row['num_cher']);
			printZeroTD($row['Name_Mark']);
			printZeroTD($row['Sheet_A1']);
			printZeroTD($row['Time1']);
			printZeroTD($row['Man_Hour1']);
			printZeroTD($row['Sheet_A3']);
			printZeroTD($row['Time3']);
			printZeroTD($row['Man_Hour3']);
			printZeroTD($row['Sheet_A4']);
			printZeroTD($row['Time4']);
			printZeroTD($row['Man_Hour4']);
			printZeroTD($row['k_Sheet']);
			printZeroTD($row['Time_Collection']);
			printZeroTD($row['Time_Agreement']);
			if ( ($_SESSION['Name_Post'] == "Экономист") OR
			($_SESSION['Name_Post'] == "Начальник управления") OR
			($_SESSION['Name_Post'] == "Начальник отдела") OR
			($_SESSION['Name_Post'] == "Зам.начальника отдела") OR
			($_SESSION['Name_Post'] == "Начальник бюро") OR
			($_SESSION['Name_Post'] == "Администратор"))
			{
				printZeroTD($row['prov']);
			}
			printZeroTD($row['Time']);
			printZeroTD(round($row['Man_Hour_Sum'],2));
			if ( $row['checkBVP'] == "TRUE" )
			{
				printZeroTD("<img class= \"icon\" src=\"img/success.png\" align=\"middle\">");
			}
			else {
				printZeroTD("<img class= \"icon\" src=\"img/validation.png\" align=\"middle\">");
			}
			if ( $row['statusBVP'] <> 0 )
			{
				printZeroTD(strftime("%d.%m.%y",$row['statusBVP'] + $_SESSION['localTime'] * 3600));
			}
			else {
				printZeroTD("<img class= \"icon\" src=\"img/validation.png\" align=\"middle\">");
			}
			$Sheet_A1 = $Sheet_A1 + round($row['Sheet_A1'],2);
			$Sheet_A3 = $Sheet_A3 + round($row['Sheet_A3'],2);
			$Sheet_A4 = $Sheet_A4 + round($row['Sheet_A4'],2);
			$Time1 = $Time1 + round($row['Time1'],2);
			$Time3 = $Time3 + round($row['Time3'],2);
			$Time4 = $Time4 + round($row['Time4'],2);
			$Time_Collection = $Time_Collection + round($row['Time_Collection'],2);
			$Time_Agreement = $Time_Agreement + round($row['Time_Agreement'],2);
			$prov = $prov + round($row['prov'],2);
			$Time = $Time + round($row['Time'],2);
			$ManHourSum = $ManHourSum+round($row['Man_Hour_Sum'],2);
		}
		print "<TR align=center>\n";
		print "<TH colspan=5 align=right>Итого:</TH>";
		printZeroTH($Sheet_A1);
		printZeroTH($Time1);
		print "<TH>&nbsp</TH>\n";
		printZeroTH($Sheet_A3);
		printZeroTH($Time3);
		print "<TH>&nbsp</TH>\n";
		printZeroTH($Sheet_A4);
		printZeroTH($Time4);
		print "<TH>&nbsp</TH>\n";
		print "<TH>&nbsp</TH>\n";
		printZeroTH($Time_Collection);
		printZeroTH($Time_Agreement);
		if ( ($_SESSION['Name_Post'] == "Экономист") OR
		($_SESSION['Name_Post'] == "Начальник управления") OR
		($_SESSION['Name_Post'] == "Начальник отдела") OR
		($_SESSION['Name_Post'] == "Зам.начальника отдела") OR
		($_SESSION['Name_Post'] == "Начальник бюро") OR
		($_SESSION['Name_Post'] == "Администратор"))
		{
			printZeroTH($prov);
		}
		printZeroTH($Time);
		printZeroTH($ManHourSum);
		print "<TH>&nbsp</TH>\n";
		print "<TH>&nbsp</TH>\n";
		print "</TABLE>\n";
		//print "</div>";
	}
}
//******************************************************************************
//SheetOutput() - постраничный вывод в виде списка месяцев и лет  (устар. подлежит удалению)
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function SheetOutput($full = true)
{
	//global $Month;//список месяцев в году
	//global $Year; //список лет
	$Year =  $_SESSION['Year'];
	//если применяется полная версия функции, то обрабатываются  pageMonth
	if ($full == true)
	{
		$Month = $_SESSION['Month'];
		if ( empty($_GET['pageMonth']) )
		{
			$pageMonth = $_SESSION['pageMonth'];//приравниваем к текущему месяцу
		}
		else {
			//если нажата ссылка то текущим месяцем становится значение ссылки
			$pageMonth = intval($_GET['pageMonth']);
			$_SESSION['pageMonth'] = $_GET['pageMonth'];
		}
	}
	if ( empty($_GET['pageYear']) )
	{
		$pageYear = $_SESSION['pageYear'];//приравниваем к текущему году
	}
	else {
		//если нажата ссылка то текущим годом становится значение ссылки
		$pageYear = intval($_GET['pageYear']);
		$_SESSION['pageYear'] = $_GET['pageYear'];
	}
	print "<div class=\"split\">";
	print "<div class=\"menu_year\">";
	print "<ul>";
	foreach($Year as $i=>$value )
	{
		if ( $i == $pageYear )
			print "<li><a href=\"{$_SERVER['PHP_SELF']}?pageYear=$i\" class=\"special\">$value</a></li>";
		else {
			print "<li><a href=\"{$_SERVER['PHP_SELF']}?pageYear=$i\">$value</a></li>";
		}
	}
	print "</ul>";
	print "</div>";
	print "</div>";
	if ($full == true)
	{
		print "<div class=\"split\">";
		print "<div class=\"menu_year\">";
		print "<ul>";
		foreach($Month as $i=>$value )
		{
			if ( $i == $pageMonth )
				print "<li><a href=\"{$_SERVER['PHP_SELF']}?pageMonth=$i\" class=\"special\">$value</a></li>";
			else {
				print "<li><a href=\"{$_SERVER['PHP_SELF']}?pageMonth=$i\">$value</a></li>";
			}
		}
		print "</ul>";
		print "</div>";
		print "</div>";
	}
}


//******************************************************************************
// ListMonthYear($full = true)  - постраничный вывод в виде списка месяцев и лет
//аналогичино SheetOutput но с использованием css framework
//Входные параметры:$full-bool полный вывод
//Выходные параметры:
//******************************************************************************
function ListMonthYear($full = true)
{
    $Year =  $_SESSION['Year'];
	//если применяется полная версия функции, то обрабатываются  pageMonth
	if ($full == true)
	{
		$Month = $_SESSION['Month'];
		if ( empty($_GET['pageMonth']) )
		{
			$pageMonth = $_SESSION['pageMonth'];//приравниваем к текущему месяцу
		}
		else {
			//если нажата ссылка то текущим месяцем становится значение ссылки
			$pageMonth = intval($_GET['pageMonth']);
			$_SESSION['pageMonth'] = $_GET['pageMonth'];
		}
	}
	if ( empty($_GET['pageYear']) )
	{
		$pageYear = $_SESSION['pageYear'];//приравниваем к текущему году
	}
	else {
		//если нажата ссылка то текущим годом становится значение ссылки
		$pageYear = intval($_GET['pageYear']);
		$_SESSION['pageYear'] = $_GET['pageYear'];
	}

 	print "<ul class=\"f-nav f-nav-tabs\">";
	foreach($Year as $i=>$value )
	{
		if ( $i == $pageYear )
			print "<li class=\"active\"><a href=\"{$_SERVER['PHP_SELF']}?pageYear=$i\">$value</a></li>";
		else {
			print "<li><a href=\"{$_SERVER['PHP_SELF']}?pageYear=$i\">$value</a></li>";
		}
	}
	print "</ul>";

	if ($full == true)
	{
		print "<ul class=\"f-nav f-nav-tabs\">";
		foreach($Month as $i=>$value )
		{
			if ( $i == $pageMonth )
				print "<li class=\"active\"><a href=\"{$_SERVER['PHP_SELF']}?pageMonth=$i\">$value</a></li>";
			else {
				print "<li><a href=\"{$_SERVER['PHP_SELF']}?pageMonth=$i\">$value</a></li>";
			}
		}
		print "</ul>";
	}
}
//******************************************************************************
//SheetProject() - постраничный вывод в виде списка ссылок номеров проекта
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function SheetProject()
{
	$arrayProject = $_SESSION['arrayProject'];
	if ( empty($_GET['pageProject']) )
	{
		$pageProject = $_SESSION['pageProject'];//извлекаем 1 эл-т массива
	}
	else {
		//если нажата ссылка то текущим месяцем становится значение ссылки
		$pageProject = $_GET['pageProject'];
		$_SESSION['pageProject'] = $_GET['pageProject'];
	}
	//$_SESSION['pageProject'] = $_GET['pageProject'];
	print "<div class=\"split\">";
	print "<div class=\"menu_year\">";
	print "<ul>";
	foreach($arrayProject as $i=>$value )
	{
		if ( $i == $pageProject )
			print "<li><a href=\"{$_SERVER['PHP_SELF']}?pageProject=$i\" class=\"special\">$value</a></li>";
		else {
			print "<li><a href=\"{$_SERVER['PHP_SELF']}?pageProject=$i\">$value</a></li>";
		}
	}
	print "</ul>";
	print "</div>";
	print "</div>";
}
//******************************************************************************
//SheetDepartment() - постраничный вывод в виде списка ссылок названий отделов
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function SheetDepartment()
{
	$arrayDepartment = $_SESSION['arrayDepartment'];
	if ( empty($_GET['pageDepartment']) )
	{
		$pageDepartment = $_SESSION['pageDepartment'];//извлекаем 1 эл-т массива
	}
	else {
		//если нажата ссылка то текущим месяцем становится значение ссылки
		$pageDepartment = $_GET['pageDepartment'];
		$_SESSION['pageDepartment'] = $_GET['pageDepartment'];
	}
	print "<div class=\"split\">";
	print "<div class=\"menu_year\">";
	print "<ul>";
	foreach($arrayDepartment as $i=>$value )
	{
		if ( $i == $pageDepartment )
			print "<li><a href=\"{$_SERVER['PHP_SELF']}?pageDepartment=$i\" class=\"special\">$value</a></li>";
		else {
			print "<li><a href=\"{$_SERVER['PHP_SELF']}?pageDepartment=$i\">$value</a></li>";
		}
	}
	print "</ul>";
	print "</div>";
	print "</div>";
}
//*************************************************************************************************************
// ViewTableEmployee()- функция отображения таблицы сотрудников
// Входные параметры:
//*************************************************************************************************************

function ViewTableEmployee()
{
	global $link;
	switch ($_REQUEST['rbselect'])
	{
		case "select":
		//выборка всей базы
		break;
		case "insert":
		checkMatchEmployee();
		$Query = "INSERT INTO Employee SET Family=".escapeshellarg($_REQUEST['Family']).
		", Name=".escapeshellarg($_REQUEST['Name']).
		", Patronymic=".escapeshellarg($_REQUEST['Patronymic']).
		", id_Department=".escapeshellarg($_REQUEST['Department']).
		", Tabel_Number=".escapeshellarg($_REQUEST['Tabel_Number']).
		", id_Office=".escapeshellarg($_REQUEST['Office']).
		", id_Post=".escapeshellarg($_REQUEST['Post']).
		", Sanction=".escapeshellarg($_REQUEST['Sanction']).
		", Login=".escapeshellarg($_REQUEST['Login']).
		", Password=".escapeshellarg(cryptParol(trim(strtolower($_REQUEST['Password'])))).
		", StatusMonth = ".escapeshellarg(0).
		", StatusYear = ".escapeshellarg(0);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "update":
		$Comma_separated = implode(",", $_REQUEST['employee']);
		//если стоит статус уволен то дата увольнения тоже обновляется
		if ($_REQUEST['Status'] == 'FALSE')
		{
			$StatusDate =  ", StatusMonth = ".escapeshellarg($_REQUEST['lstMonth']).
			", StatusYear = ".escapeshellarg(getIdYear($_REQUEST['lstYear']));
		}
		else {
			$StatusDate =  ", StatusMonth = ".escapeshellarg(0).
			", StatusYear = ".escapeshellarg(0);
		}
		$Query = "UPDATE Employee SET Family=".escapeshellarg($_REQUEST['Family']).
		", Name=".escapeshellarg($_REQUEST['Name']).
		", Patronymic=".escapeshellarg($_REQUEST['Patronymic']).
		", Tabel_Number=".escapeshellarg($_REQUEST['Tabel_Number']).
		", id_Department=".escapeshellarg($_REQUEST['Department']).
		", id_Office=".escapeshellarg($_REQUEST['Office']).
		", id_Post=".escapeshellarg($_REQUEST['Post']).
		", Login=".escapeshellarg($_REQUEST['Login']).
		", Status=".escapeshellarg($_REQUEST['Status']).
		", Sanction=".escapeshellarg($_REQUEST['Sanction']).
		", Password=".escapeshellarg(cryptParol(trim(strtolower($_REQUEST['Password'])))).$StatusDate.
		"  WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "updateNotParol":
		$Comma_separated = implode(",", $_REQUEST['employee']);
		//если стоит статус уволен то дата увольнения тоже обновляется
		if ($_REQUEST['Status'] == 'FALSE')
		{
			$StatusDate =  ", StatusMonth = ".escapeshellarg($_REQUEST['lstMonth']).
			", StatusYear = ".escapeshellarg(getIdYear($_REQUEST['lstYear']));
		}
		else {
			$StatusDate =  ", StatusMonth = ".escapeshellarg(0).
			", StatusYear = ".escapeshellarg(0);
		}
		$Query = "UPDATE Employee SET Family=".escapeshellarg($_REQUEST['Family']).
		", Name=".escapeshellarg($_REQUEST['Name']).
		", Patronymic=".escapeshellarg($_REQUEST['Patronymic']).
		", Tabel_Number=".escapeshellarg($_REQUEST['Tabel_Number']).
		", id_Department=".escapeshellarg($_REQUEST['Department']).
		", id_Office=".escapeshellarg($_REQUEST['Office']).
		", id_Post=".escapeshellarg($_REQUEST['Post']).
		", Login=".escapeshellarg($_REQUEST['Login']).
		", Status=".escapeshellarg($_REQUEST['Status']).
		", Sanction=".escapeshellarg($_REQUEST['Sanction']).$StatusDate.
		"  WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "delete":
		$Comma_separated = implode(",", $_REQUEST['employee']);
		$Query = "DELETE FROM Employee WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		//при увольнении учитываем месяц увольнения
		case "status":
		$Comma_separated = implode(",", $_REQUEST['employee']);
		$Query ="  UPDATE Employee SET Status=".escapeshellarg('FALSE').
		", StatusMonth = ".escapeshellarg($_REQUEST['lstMonth']).
		", StatusYear = ".escapeshellarg(getIdYear($_REQUEST['lstYear'])).
		"  WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
	}
	//======================= Модуль постраничного вывода информации на экран ========================
	SheetDepartment();
	//======================= Конец модуля постраничного вывода информации на экран ========================
	//выборка из таблицы
	$Query = "SELECT Employee.Id, Employee.Family, Employee.Name, Employee.Patronymic, Employee.Status, Employee.Tabel_Number, ".
	"Department.Name_Department, Office.Name_Office, Post.Name_Post, Employee.Sanction, Employee.Login, Employee.Password,
	Employee.StatusMonth, Employee.StatusYear ".
	"FROM Department, Office, Post, Employee ".
	"WHERE ".
	"((Post.id = Employee.id_Post) AND ".
	"(Office.id = Employee.id_Office) AND ".
	"(Department.id = Employee.id_Department) AND ".
	"(Department.id LIKE \"{$_SESSION['pageDepartment']}\")) ".
	"ORDER BY Employee.Status, Department.Name_Department,Office.Name_Office,Employee.Family ";
	//file_put_contents("query.txt",$Query);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		print "<H4>Всего ".mysql_num_rows($dbResult)." позиций </H4>";
		//таблица HTML
		//print "<div class=\"layer\">\n";
		print "<TABLE  Width=\"100%\" Border=\"1\" CellSpacing=\"0\" CellPadding=\"2\">\n";
		print "<caption><H4>Таблица 'Сотрудник'</H4></caption>\n";
		print "<THEAD align=center bgcolor = #E8E8E8 valign=top>\n";
		print "<TR>\n";
		print "<TH>Метка</TH>".
		"<TH>id</TH>".
		"<TH>Фамилия</TH>".
		"<TH>Имя</TH>".
		"<TH>Отчество</TH>".
		"<TH>Табельный <br>номер</TH>".
		"<TH>Отдел</TH>".
		"<TH>Бюро</TH>".
		"<TH>Должность</TH>".
		"<TH>Статус</TH>".
		"<TH>Дата<br> увольнения</TH>".
		"<TH>Логин</TH>".
		"<TH>Утверждающий</TH>";
		print "</TR>\n";
		print "</THEAD>";
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			//если выбрано копирование, то выделяем красным
			if ( $_REQUEST['rbselect'] == "copy" AND
			FALSE !== array_search($row['Id'],$_REQUEST['employee'],TRUE) )
			{
				print "<TR align=left BGCOLOR=#FF0000>\n";
			}
			else {
				print "<TR align=left>\n";
			}
			//если сотрудник уже уволен, то выделяем его
			if ( $row['Status'] == 'FALSE' )
			{
				print "<TR align=left BGCOLOR=#A2A2A2>\n";
			}
			print "<TD><input name=\"employee[]\" type=\"checkbox\" value=\"{$row['Id']}\">";
			print "<TD>{$row['Id']}</TD>";
			print "<TD>{$row['Family']}</TD>";
			print "<TD>{$row['Name']}</TD>";
			print "<TD>{$row['Patronymic']}</TD>";
			printZeroTD($row['Tabel_Number']);
			print "<TD>{$row['Name_Department']}</TD>";
			print "<TD>{$row['Name_Office']}</TD>";
			print "<TD>{$row['Name_Post']}</TD>";
			if ( $row['Status'] == 'TRUE' )
			{
				print "<TD>работает</TD>";
				printZeroTD();
			}
			else {
				print "<TD>уволен</TD>";
				printZeroTD(getMonthFromId($row['StatusMonth'])."&nbsp;".getYearFromId($row['StatusYear']));
			}
			print "<TD>{$row['Login']}</TD>";
			if ($row['Sanction'] == 'TRUE')
			{
				print "<TD>утверждающий</TD>";
			}
			else {
				print "<TD>нет</TD>";
			}
		}
		print "</TABLE>\n";
		//print "</div>";
	}
}
//******************************************************************************
//ViewTableGeod() - функция отображения данных в модуле сапр
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function ViewTableGeod()
{
	global $link; //переменная соннекта с базой
	//коэф на полевые работы
	$Coefficient1 =
	$_REQUEST['k_field'] *     //коэффициент на полевые работы
	$_REQUEST['k_light'] * 		// коэффициент при работе с искусственным освещением
	$_REQUEST['k_vibration'] * //коэффициент при работе в помещениях с вибрацией
	$_REQUEST['k_season'] *    //коэффициент сезонности
	$_REQUEST['k_bridge'] *    //коэффициент при работе с подмастей
	$_REQUEST['k_regime'];    //режимный коэффициент
	//коэф на камеральные работы
	$Coefficient2 =
	$_REQUEST['k_plane'] *     //коэффициент при составлении планов в цвете
	$_REQUEST['k_it']*         //коэффициент при использовании комп.технологий
	$_REQUEST['k_kalka']*      //коэффициент при нанесении исп. съемки на кальку
  $_REQUEST['k_regime_kam']* //режимный коэффициент при работе с камералкой
	$_REQUEST['k_profil'];    //коэффициент при создании профилей в 2 видах
	if ($Coefficient1 == 1 OR $Coefficient2 == 1)
	{
		$Coefficient = $Coefficient1 * $Coefficient2;
	}
	else {
		$Coefficient = $Coefficient1 + $Coefficient2;
	}
	//общий коэффициент
	$Coefficient = $Coefficient *
	$_REQUEST['k_index'] *     //коэффициент индексации
	$_REQUEST['k_smeta'] *     //коэффициент К итогу сметной стоимости в зависимости от районного коэф
	$_REQUEST['k_correction']; //коэффициент коррекции или дополнительных работ
	//для вывода информации о коэф-ах необходимо разместить их в сессии
	$_SESSION['k_field'] = $_REQUEST['k_field'];
	$_SESSION['k_index'] = $_REQUEST['k_index'];
	$_SESSION['k_smeta'] = $_REQUEST['k_smeta'];
	$_SESSION['k_light'] = $_REQUEST['k_light'];
	$_SESSION['k_vibration'] = $_REQUEST['k_vibration'];
	$_SESSION['k_season'] = $_REQUEST['k_season'];
	$_SESSION['k_bridge'] = $_REQUEST['k_bridge'];
	$_SESSION['k_regime'] = $_REQUEST['k_regime'];
	$_SESSION['k_plane'] = $_REQUEST['k_plane'];
	$_SESSION['k_profil'] = $_REQUEST['k_profil'];
	$_SESSION['k_it'] = $_REQUEST['k_it'];
	$_SESSION['k_correction'] = $_REQUEST['k_correction'];
	$_SESSION['k_kalka'] = $_REQUEST['k_kalka'];
	//стоимость
	$Amount = round($_REQUEST['Price']*$_REQUEST['Number'],2);
	//составление запроса в зависимости от вида операции
	switch ($_REQUEST['rbselect'])
	{
		case "select":
		//выборка всей базы
		break;
		case "insert":
		//вставка новой записи
		$Query = "INSERT INTO geod SET Date = ".escapeshellarg($_REQUEST['Date']).
		", Name_Work = ".escapeshellarg($_REQUEST['Name_Work']).
		", id_Project = ".escapeshellarg($_REQUEST['lstProject']).
		", id_Tender = ".escapeshellarg($_REQUEST['lstTender']). //tender_geod
		", Unit_Measurement = ".escapeshellarg($_REQUEST['Unit_Measurement']).
		", Category = ".escapeshellarg($_REQUEST['Category']).
		", Price = ".escapeshellarg($_REQUEST['Price']).
		", Number = ".escapeshellarg($_REQUEST['Number']).
		", Amount = ".escapeshellarg($Amount).
		", Coefficient = ".escapeshellarg(round($Coefficient,2)).
		", Comment = ".escapeshellarg($_REQUEST['Comment']).
		", Total = ".escapeshellarg(round($Amount * $Coefficient,2)).
		", id_Employee =".escapeshellarg($_SESSION['Id_Employee']).
		", Time = ".escapeshellarg($_REQUEST['Time']).
		", DateSubmit = ".escapeshellarg(mktime());
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "update":
		//обновление записи
		$Comma_separated = implode(",", $_REQUEST['design']);
		$Query = "UPDATE geod SET Date=".escapeshellarg($_REQUEST['Date']).
		", Name_Work = ".escapeshellarg($_REQUEST['Name_Work']).
		", id_Project = ".escapeshellarg($_REQUEST['lstProject']).
		", id_Tender = ".escapeshellarg($_REQUEST['lstTender']). //tender_geod
		", Unit_Measurement = ".escapeshellarg($_REQUEST['Unit_Measurement']).
		", Category = ".escapeshellarg($_REQUEST['Category']).
		", Price = ".escapeshellarg($_REQUEST['Price']).
		", Number = ".escapeshellarg($_REQUEST['Number']).
		", Amount = ".escapeshellarg($Amount).
		", Coefficient = ".escapeshellarg(round($Coefficient,2)).
		", Comment = ".escapeshellarg($_REQUEST['Comment']).
		", Total = ".escapeshellarg(round($Amount * $Coefficient,2)).
		", id_Employee =".escapeshellarg($_SESSION['Id_Employee']).
		", Time = ".escapeshellarg($_REQUEST['Time']).
		", DateSubmit = ".escapeshellarg(mktime()).
		"  WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "delete":
		//удалить выбранные записи
		$Comma_separated = implode(",", $_REQUEST['design']);
		$Query = "DELETE FROM geod WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
	}
	//======================= Модуль постраничного вывода информации на экран ========================
	ListMonthYear();

	$arrayMonth = getArrayTimeYear($_SESSION['pageYear']);
	//======================= Конец модуля постраничного вывода информации на экран ========================
	$Query1 = "SELECT DISTINCTROW geod.Id, geod.Date, geod.Name_Work, geod.Unit_Measurement, ".
	"geod.Category, geod.Price, geod.Number, geod.Amount, geod.Coefficient, geod.Comment, ".
	"geod.Total, geod.id_Employee, geod.DateSubmit, geod.Time, project.Number_Project, tender_geod.Number_Tender ".
	"FROM geod, employee, project, tender_geod ".
	"WHERE ".
	"(geod.id_Employee = employee.id) ".
	" AND (geod.id_Project = project.id) ".
	" AND (geod.id_Tender = tender_geod.id) ".
	" AND (employee.id=".escapeshellarg($_SESSION['Id_Employee']).") ".
	" AND (geod.Date>=".escapeshellarg($arrayMonth[$_SESSION['pageMonth']]).
	" AND geod.Date<".escapeshellarg($arrayMonth[$_SESSION['pageMonth']+1]).")";
	$Query2 = " ORDER BY project.Number_Project, tender_geod.Number_Tender DESC";
	//соединяем все запросы в один
	$Query = $Query1.$Query2;
	//file_put_contents("query.txt",$Query);
	//рисуем таблицу
	ViewGeod($Query,"noreport");
}
//******************************************************************************
//ViewGeod() - отображение таблицы стоимости геодезических работ
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function ViewGeod($Query,$Report)
{
	global $link; //переменная соннекта с базой
	//результирующая таблица
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $Query не удалась!\n";
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		print "<table class=\"f-table-zebra\">\n";
		print "<caption>Ведомость расчета стоимости выполнения работ. <br /> Бюро генплана и геодезии</caption>\n";
   		print "<thead>\n";
		print "<TR>\n";
		if ($Report != "report")
		{
			print "<TH>Метка</TH>\n";
		}
		print "<TH>Дата</TH>\n";
		if ($Report == "report")
		{
			print "<TH>Сотрудник</TH>\n";
		}
		print "<TH>№ проекта</TH>\n";
		print "<TH>№ заявки</TH>\n";
		print "<TH>Наименование работ</TH>\n";
		print "<TH>Единица измерения</TH>\n";
		print "<TH>Категория <br /> сложности</TH>\n";
		print "<TH>Цена (руб.)</TH>\n";
		print "<TH>Количество</TH>\n";
		print "<TH>Стоимость (руб.)</TH>\n";
		print "<TH>Сотрудники</TH>\n";
		print "<TH>Общий <br /> коэффициент</TH>\n";
		print "<TH>Всего (руб.)</TH>\n";
		print "<TH>Трудозатраты<br>ч./час</TH>\n";
		if ($_SESSION['k_addDate'] == "TRUE")
		{
			print "<TH>Дата внесения <br /> в базу</TH>\n";
		}
		print "</TR>\n";
		print "</thead>\n";
		$Total = 0;
		$TimeTotal = 0;
		$result = array();
		$n = 0;
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			//если выбрано копирование, то выделяем красным
			if ( $_REQUEST['rbselect'] == "copy" AND
			FALSE !== array_search($row['Id'], $_REQUEST['design'], TRUE))
			{
				print "<TR align=center style=\"background-color:#FF7F50\">\n";
			}
			else {
				print "<TR align=center>\n";
			}
			if ($Report != "report")
			{
				print "<TD><input name=\"design[]\" type=\"checkbox\" value=\"{$row['Id']}\"></TD>\n";
			}
			$strDate = strftime("%d.%m.%y",$row['Date'] + $_SESSION['localTime'] * 3600);
			printZeroTD($strDate);

			if ($Report == "report")
			{
				$strFIO = $row['Family']." ".mb_substr($row['Name'],0,1,'utf8')." ".mb_substr($row['Patronymic'],0,1,'utf8');
				print "<TD>$strFIO</TD>";
			}
			printZeroTD($row['Number_Project']);
			printZeroTD($row['Number_Tender']);
			printZeroTD($row['Name_Work']);
			printZeroTD($row['Unit_Measurement']);
			printZeroTD($row['Category']);
			printZeroTD($row['Price']);
			printZeroTD($row['Number']);
			printZeroTD($row['Amount']);
			printZeroTD($row['Comment']);
			printZeroTD($row['Coefficient']);
			printZeroTD($row['Total']);
			printZeroTD(round($row['Time'],1));
			if ($_SESSION['k_addDate'] == "TRUE")
			{
				print "<TD>".strftime("%d.%m.%y",$row['DateSubmit'])."</TD>\n";
			}
			$Total = $Total + $row['Total'];
			$TimeTotal = $TimeTotal + $row['Time'];
			//==========================конец блока ===============================================================
			++$n;//в каждой n будут храниться строка результата
		//заносим результаты в массив для его сериализации и передачи в эксель
			
			$result[$n]['Date'] 			= $strDate;
			$result[$n]['FIO'] 				= $strFIO;
			$result[$n]['Number_Project'] 	= $row['Number_Project'];
			$result[$n]['Number_Tender'] 	= $row['Number_Tender'];
			$result[$n]['Name_Work'] 		= $row['Name_Work'];
			$result[$n]['Time'] 			= round($row['Time'],1);
			
		}
		print "<TR align=center>\n";
		if ($Report != "report")
		{
			print "<TH colspan=12 align=right>Итого:</TH>";
		}
		else {
			if ($_SESSION['k_addDate'] == "TRUE")
			{
				print "<TH colspan=12 align=right>Итого:</TH>";
			}
			else {
				print "<TH colspan=12 align=right>Итого:</TH>";
			}
		}
		printZeroTH($Total);
		printZeroTH(round($TimeTotal,1));
			if ($_SESSION['k_addDate'] == "TRUE")
			{
				printZeroTH();
			}
		print "</table>\n";
	}
	$_SESSION['k_addDate']=null;
	return $result;
}
//******************************************************************************
//ViewTableSAPR()-функция отображение таблицы работ сотрудников бюро САПР
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function ViewTableSAPR()
{
	global $link; //переменная соннекта с базой
	//в зависимости от выбора отметки, инициализируется переменная 'check_Work'

	//составление запроса в зависимости от вида операции
	switch ($_REQUEST['rbselect'])
	{
		case "select":
		//выборка всей базы
		break;
		case "insert":
		//вставка новой записи
		$Query = "INSERT INTO sapr SET Date = ".escapeshellarg($_REQUEST['Date']).
		", Name_Work = ".escapeshellarg($_REQUEST['Name_Work']).
		", id_Employee = ".escapeshellarg($_REQUEST['lstPerformer']).  //исполнитель
		", id_EmployeeCustomer = ".escapeshellarg($_REQUEST['lstEmployee']).//заказчик
		", Time = ".escapeshellarg($_REQUEST['Time']).
		", Comment = ".escapeshellarg($_REQUEST['Comment']);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "update":
		//обновление записи
		$Comma_separated = implode(",", $_REQUEST['design']);
		$Query = "UPDATE sapr SET Date =".escapeshellarg($_REQUEST['Date']).
		", Name_Work = ".escapeshellarg($_REQUEST['Name_Work']).
		", id_Employee = ".escapeshellarg($_REQUEST['lstPerformer']). //исполнитель
		", id_EmployeeCustomer = ".escapeshellarg($_REQUEST['lstEmployee']).//заказчик
		", Time = ".escapeshellarg($_REQUEST['Time']).
		", Comment = ".escapeshellarg($_REQUEST['Comment']).
		"  WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;

		case "delete":
		//удалить выбранные записи
		$Comma_separated = implode(",", $_REQUEST['design']);
		$Query = "DELETE FROM sapr WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
	}
	//======================= Модуль постраничного вывода информации на экран ========================
	ListMonthYear();
	$arrayMonth = getArrayTimeYear($_SESSION['pageYear']);
	//======================= Конец модуля постраничного вывода информации на экран ========================
	if ($_SESSION['Name_Post'] == "Администратор")
	{
		$Query1 = "SELECT sapr.Id, sapr.Date, sapr.Name_Work, ".
		"tableCustomer.Family AS FamilyCustomer, tableCustomer.Name AS NameCustomer, tableCustomer.Patronymic AS PatronymicCustomer, ".
		"tableEmployee.Family AS FamilyEmployee, tableEmployee.Name AS NameEmployee, tableEmployee.Patronymic AS PatronymicEmployee, ".
		"sapr.Time, sapr.Comment  ".
		"FROM sapr, employee AS tableCustomer, employee AS tableEmployee ".
		"WHERE ".
		"(sapr.id_Employee = tableEmployee.id) ". //исполнитель
		" AND (sapr.id_EmployeeCustomer = tableCustomer.id) ". //заказчик
		" AND (sapr.id_Employee = ".escapeshellarg($_SESSION['Id_Employee']).") ".
		" AND (sapr.Date >= ".escapeshellarg($arrayMonth[$_SESSION['pageMonth']]).
		" AND sapr.Date < ".escapeshellarg($arrayMonth[$_SESSION['pageMonth']+1]).")";
	}
	else {
		$Query1 = "SELECT sapr.Id, sapr.Date, sapr.Name_Work, ".
		"tableCustomer.Family AS FamilyCustomer, tableCustomer.Name AS NameCustomer, tableCustomer.Patronymic AS PatronymicCustomer, ".
		"tableEmployee.Family AS FamilyEmployee, tableEmployee.Name AS NameEmployee, tableEmployee.Patronymic AS PatronymicEmployee, ".
		"sapr.Time, sapr.Comment  ".
		"FROM sapr, employee AS tableCustomer, employee AS tableEmployee ".
		"WHERE ".
		"(sapr.id_Employee = tableEmployee.id) ". //исполнитель
		" AND (sapr.id_EmployeeCustomer = tableCustomer.id) ". //заказчик
		" AND (sapr.id_EmployeeCustomer = ".escapeshellarg($_SESSION['Id_Employee']).") ".
		" AND (sapr.Date >= ".escapeshellarg($arrayMonth[$_SESSION['pageMonth']]).
		" AND sapr.Date < ".escapeshellarg($arrayMonth[$_SESSION['pageMonth']+1]).")";
	}
	$Query2 = " ORDER BY sapr.Date DESC";
	//соединяем все запросы в один
	$Query = $Query1.$Query2;
	//рисуем таблицу
	ViewSapr($Query,"noreport");
	//file_put_contents("query.txt",$Query);
}
//******************************************************************************
//ViewSapr(Query,Report) - отображение таблицы сапровских работ работ
//Входные параметры:
//Query - запрос
//Report - флаг состояния(отчет или отображение состояния)
	//Выходные параметры:
//******************************************************************************

function ViewSapr($Query,$Report)
{
	global $link; //переменная соннекта с базой
	//результирующая таблица
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		print "<table class=\"f-table-zebra\">\n";
		print "<caption>Ведомость трудозатрат</caption>\n";
        print "<thead>\n";
		print "<TR>\n";
		if ($Report != "report")
		{
			print "<TH width=5%>Метка</TH>\n";
		}
		print "<TH>Дата</TH>\n";
		print "<TH>Заказчик</TH>\n";
		print "<TH>Наименование работ</TH>\n";
		print "<TH>Комментарий</TH>\n";
		print "<TH>Исполнитель</TH>\n";
		print "<TH>Время <br /> выполнения</TH>\n";
		print "</TR>\n";
        print "</thead>\n";

		$Total = 0;
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			//если выбрано копирование, то выделяем красным
			if ( $_REQUEST['rbselect'] == "copy" AND
			FALSE !== array_search($row['Id'], $_REQUEST['design'], TRUE))
			{
				print "<TR align=center style=\"background-color:#FF7F50\">\n";
			}
			else {
				print "<TR align=center>\n";
			}
			if ($Report != "report")
			{
				print "<TD><input name=\"design[]\" type=\"checkbox\" value=\"{$row['Id']}\"></TD>\n";
			}
			print "<TD>".strftime("%d.%m.%y",$row['Date'] + $_SESSION['localTime'] * 3600)."</TD>\n";
			//заказчик
			print "<TD nowrap align=left>{$row['FamilyCustomer']} ".
			mb_substr($row['NameCustomer'],0,1,'utf8').".".
			mb_substr($row['PatronymicCustomer'],0,1,'utf8').
			".</TD>";
			//работа
			print "<TD nowrap align=left>".$row['Name_Work']."</TD>";
			printZeroTD($row['Comment']);
			//исполнитель
			print "<TD nowrap align=left>{$row['FamilyEmployee']} ".
			mb_substr($row['NameEmployee'],0,1,'utf8').".".
			mb_substr($row['PatronymicEmployee'],0,1,'utf8').
			".</TD>";
			printZeroTD($row['Time']);
			$Total = $Total + round($row['Time'],2);
		}
		print "<TR align=center>\n";
		if ($Report != "report")
		{
			print "<TH colspan=6 align=right>Итого:</TH>";
		}
		else {
			print "<TH colspan=5 align=right>Итого:</TH>";
		}
		printZeroTH($Total);
		print "</table>\n";
	}
}
//*************************************************************************************************************
// checkEmptyArchive()- функция проверки введенных данных модуля БВП на пустоту
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function checkEmptyArchive()
{
	global $link,$errors;
	//проверяем по отдельности каждый параметр, если он пустой, то инициализируем
	if (empty($_REQUEST['Sheet_A']))
	{
		$_REQUEST['Sheet_A'] = "0";
	}
	if (empty($_REQUEST['Sheet_A1']))
	{
		$_REQUEST['Sheet_A1'] = "0";
	}
	if (empty($_REQUEST['Sheet_A3']))
	{
		$_REQUEST['Sheet_A3'] = "0";
	}
	if (empty($_REQUEST['Sheet_A4']))
	{
		$_REQUEST['Sheet_A4'] = "0";
	}
	if (empty($_REQUEST['NumberDraw']))
	{
		$_REQUEST['NumberDraw'] = " ";
	}
	if (empty($_REQUEST['Comment']))
	{
		$_REQUEST['Comment'] = " ";
	}
	//заменяем все запятые в полях ввода точками
	$_REQUEST['Sheet_A'] = str_replace(",", ".", $_REQUEST['Sheet_A']);
	//проверка на тождественность данных числам, а не строкам
	if ( !is_numeric($_REQUEST['Sheet_A']) )
	{
		$errors[] = "<H3>Листаж имеет нечисловое значение</H3>";
	}
	//проверка, если выбор из списков не числовой и не пустой, то заносим их код
	if ( !is_numeric($_REQUEST['Number_Project']) AND
	$_REQUEST['Number_Project'] != "")
	{
		$_REQUEST['Number_Project'] = escapeshellarg(getIdProject($_REQUEST['Number_Project']));
	}
	if ( !is_numeric($_REQUEST['Name_Mark']) AND
	$_REQUEST['Name_Mark'] != "")
	{
		$_REQUEST['Name_Mark'] = escapeshellarg(getIdMark($_REQUEST['Name_Mark']));
	}
	if ( !is_numeric($_REQUEST['Name_Work']) AND
	$_REQUEST['Name_Work'] != "")
	{
		$_REQUEST['Name_Work'] = escapeshellarg(getIdWork($_REQUEST['Name_Work']));
	}
	if ( !is_numeric($_REQUEST['lstEmployee']) AND
	$_REQUEST['lstEmployee'] != "")
	{
		$_REQUEST['lstEmployee'] = escapeshellarg(getIdEmployee($_REQUEST['lstEmployee']));
	}
	//если хотя бы 1 из выпадающих списков пустой, то ошибка
	if(empty($_REQUEST['Number_Project']) OR
	empty($_REQUEST['Name_Work']) OR
	empty($_REQUEST['lstEmployee']) OR
	empty($_REQUEST['Name_Mark']))
	{
		$errors[] = "<H3>Должны быть выбраны элементы из списков</H3>";
	}
	//выборка значения трудозатрат из таблицы Man_Hour
	$Query = " SELECT man_hour.Id, mark.Name_Mark, work.Name_Work, work.Comment ".
	" FROM man_hour,work,mark ".
	" WHERE ".
	"((man_hour.id_Work=work.Id) AND ".
	"(man_hour.id_Mark=mark.Id) AND ".
	"(mark.Id=".escapeshellarg($_REQUEST['Name_Mark']).") AND ".
	"(work.Id=".escapeshellarg($_REQUEST['Name_Work']).")) ".
	" LIMIT 1";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		//результирующий массив пустой, то значит марка и вид работы не соответствуют друг другу
		if ( mysql_num_rows($dbResult) == 0 )
		{
			$errors[] = "<H3>Марка не соответствует типу работы</H3>";
		}
		//в зависимости от формата документа в переменные заносятся листы
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			if ($row['Comment'] == "формат А1")
			{
				$_REQUEST['Sheet_A1'] = $_REQUEST['Sheet_A'];
			}
			elseif ($row['Comment'] == "формат А3")
			{
				$_REQUEST['Sheet_A4'] = $_REQUEST['Sheet_A']; //если работа А3 формата, заносим в А4
			}
			elseif ($row['Comment'] == "формат А4")
			{
				$_REQUEST['Sheet_A4'] = $_REQUEST['Sheet_A'];
			}
		}
	}
}
//*************************************************************************************************************
// ViewTableArchive()- функция отображения таблицы работ, заносимых бюро БВП
// Входные параметры:
//*************************************************************************************************************

function ViewTableArchive()
{
	global $link;
	//составление запроса в зависимости от вида операции
	switch ($_REQUEST['rbselect'])
	{
		case "select":
		//выборка всей базы
		break;
		case "insert":
		//вставка новой записи
		$Query = "INSERT INTO archive SET Date = ".escapeshellarg($_REQUEST['Date']).
		", id_Project = ".escapeshellarg($_REQUEST['Number_Project']).
		", id_Mark = ".escapeshellarg($_REQUEST['Name_Mark']).
		", id_Work = ".escapeshellarg($_REQUEST['Name_Work']).
		", id_Employee = ".escapeshellarg($_REQUEST['lstEmployee']).  //исполнитель
		", Sheet_A1 = ".escapeshellarg($_REQUEST['Sheet_A1']).
		", Sheet_A4 = ".escapeshellarg($_REQUEST['Sheet_A4']).
		", NumberDraw = ".escapeshellarg($_REQUEST['NumberDraw']).
		", Comment = ".escapeshellarg($_REQUEST['Comment']);

		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "update":
		//обновление записи
		$Comma_separated = implode(",", $_REQUEST['design']);
		$Query = "UPDATE archive SET Date =".escapeshellarg($_REQUEST['Date']).
		", id_Project = ".escapeshellarg($_REQUEST['Number_Project']).
		", id_Mark = ".escapeshellarg($_REQUEST['Name_Mark']).
		", id_Work = ".escapeshellarg($_REQUEST['Name_Work']).
		", id_Employee = ".escapeshellarg($_REQUEST['lstEmployee']).  //исполнитель
		", Sheet_A1 = ".escapeshellarg($_REQUEST['Sheet_A1']).
		", Sheet_A4 = ".escapeshellarg($_REQUEST['Sheet_A4']).
		", NumberDraw = ".escapeshellarg($_REQUEST['NumberDraw']).
		", Comment = ".escapeshellarg($_REQUEST['Comment']).
		"  WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "delete":
		//удалить выбранные записи
		$Comma_separated = implode(",", $_REQUEST['design']);
		$Query = "DELETE FROM archive WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
	}
	//======================= Модуль постраничного вывода информации на экран ========================

    ListMonthYear();
	$arrayMonth = getArrayTimeYear($_SESSION['pageYear']);
	//======================= Конец модуля постраничного вывода информации на экран ==================
	$Query1 = "SELECT archive.Id,
	archive.Date, archive.Sheet_A1, archive.Sheet_A4, archive.NumberDraw, archive.Comment,
	mark.Name_Mark, project.Number_Project, project.Name_Project,
	employee.Name, employee.Family, employee.Patronymic ".
	" FROM archive, employee, project, mark ".
	" WHERE ".
	"(archive.id_Mark = mark.id) AND ".
	"(archive.id_Project = project.id) AND ".
	"(archive.id_Employee = employee.id) ";
	$Query2 = " AND ((archive.Date>=".escapeshellarg($arrayMonth[$_SESSION['pageMonth']]).
	") AND (archive.Date<".escapeshellarg($arrayMonth[$_SESSION['pageMonth']+1])."))";
	$Query3 = " ORDER BY project.Number_Project,mark.Name_Mark,archive.Date     DESC";
	//соединяем все запросы в один
	$Query = $Query1.$Query2.$Query3;
	//file_put_contents("query.txt",$Query);
	//рисуем таблицу
	ViewArchive($Query,"noreport");
}
//*************************************************************************************************************
// ViewArchive($Query,$Report) - функция отображения таблицы  записей, заносимых БВП
// Входные параметры:
//$Query - запрос
//$Report - флаг состояния(отчет или отображение состояния)
	//*************************************************************************************************************

function ViewArchive($Query,$Report)
{
	global $link;
	//результирующая таблица
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		print "<table class=\"f-table-zebra\">\n";
		print "<caption>Ведомость архивных проектов</caption>\n";
        print "<thead>\n";
		print "<TR>\n";
		if ($Report != "report")
		{
			print "<TH width=5% rowspan=2>Метка</TH>\n";
		}
		print "<TH width=10% rowspan=2>Дата</TH>\n";
		print "<TH width=10% rowspan=2>Номер<br>чертежа</TH>\n";
		print "<TH width=5% rowspan=2>Марка</TH>\n";
		print "<TH width=10% rowspan=2>Проект</TH>\n";
		print "<TH width=35% rowspan=2>Наименование</TH>\n";
		print "<TH width=10% colspan=2 >Листы</TH>\n";
		print "<TH width=10% rowspan=2>Исполнитель</TH>\n";
		print "<TH width=10% rowspan=2>Комментарий</TH>\n";
		print "</TR>\n";
		print "<TR>\n";
		print "<TD>А1</TD>\n";
		print "<TD>А4</TD>\n";
		print "</TR>\n";
        print "</thead>\n";

		$A1 = 0;
		$A4 = 0;
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			//если выбрано копирование, то выделяем красным
			if ( $_REQUEST['rbselect'] == "copy" AND
			FALSE !== array_search($row['Id'], $_REQUEST['design'], TRUE))
			{
				print "<TR align=center style=\"background-color:#FF7F50\">\n";
			}
			else {
				print "<TR align=center>\n";
			}
			if ($Report != "report")
			{
				print "<TD><input name=\"design[]\" type=\"checkbox\" value=\"{$row['Id']}\"></TD>\n";
			}
			print "<TD>".strftime("%d.%m.%y",$row['Date'] + $_SESSION['localTime'] * 3600)."</TD>\n";
			printZeroTD($row['NumberDraw']);
			printZeroTD($row['Name_Mark']);
			printZeroTD($row['Number_Project']);
			printZeroTD($row['Name_Project']);
			printZeroTD($row['Sheet_A1']);
			printZeroTD($row['Sheet_A4']);
			//заказчик
			print "<TD nowrap align=left>{$row['Family']} ".
			mb_substr($row['Name'],0,1,'utf8').".".
			mb_substr($row['Patronymic'],0,1,'utf8').
			".</TD>";
			printZeroTD($row['Comment']);
			$A1 = $A1 + round($row['Sheet_A1'],2);
			$A4 = $A4 + round($row['Sheet_A4'],2);
		}
		print "<TR align=center>\n";
		if ($Report != "report")
		{
			print "<TH colspan=6 align=right>Итого:</TH>";
		}
		else {
			print "<TH colspan=5 align=right>Итого:</TH>";
		}
		printZeroTH($A1);
		printZeroTH($A4);
		printZeroTH();
		printZeroTH();
		print "</TABLE>\n";
	}
}
//******************************************************************************
//ViewTableStatusProject_short() - краткий отчет по завершенности проектов
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function ViewTableStatusProject_short()
{
	global $link;
	$arr_nul =  array (
	'OKC' => 0,
	'Plan' => 0,
	'OverPlan' => 0,
	);
	//определим интервал дат по которым ведется поиск
	$QueryInterval =  getIntervalDate(
	$_SESSION['lstMonth'],
	$_SESSION['lstYear'],
	"project",
	"DateCloseProject",
	$_SESSION['rbPeriod']);
	
	//массив для хранения значений кол-ва работ находящиеся в работе
	$OpenWork = array (
	'OKC' => 0,
	'Plan' => 0,
	'OverPlan' => 0,
	'KO' => 0
	);
	//массив для хранения значений кол-ва работ завершеных в отчетном месяце
	$CloseWork = $OpenWork;
	$CloseWork_ManHour = $OpenWork;
	//массив для хранения значений кол-ва работ уже завершеных
	$CloseWorkAll = $OpenWork;
	$CloseWorkAll_ManHour = $OpenWork;
	//1 столбец ==============================================================================
	$CurrentDate  = mktime(); //определяем текущую дату
	//определим работы которые находятся в стадии разработки
	// если  DateCloseProject=0, то работа еще не определена, ее не включаем
	$QueryOpenWork = "SELECT StatusProject, COUNT(Id) AS Total, DateOpenProject, DateCloseProject".
	" FROM project ".
	" WHERE (DateCloseProject+24*3600 >= ".escapeshellarg($CurrentDate).
	" AND DateOpenProject<=".escapeshellarg($CurrentDate).
	")  AND (project.id <> 913)";//кроме заявления;
	$QueryOpenWork = $QueryOpenWork." GROUP BY StatusProject";
	//заносим в массив кол-во работ не оконченных (в разработке)
		if ( !($dbResultOW = mysql_query($QueryOpenWork, $link)) )
	{
		//print "Выборка $QueryOpenWork не удалась!\n".mysql_error();
		processingError("$QueryOpenWork ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $rowOW = mysql_fetch_array($dbResultOW, MYSQL_BOTH) )
		{
			$OpenWork[$rowOW['StatusProject']] = $rowOW['Total'];
		}
	}
	//добавляем 4 строку- кол-во работ бюро механизации КО
	$Query_KO = "SELECT Number_Project ".
	"FROM project,design, employee, department, office ".
	" WHERE (DateCloseProject+24*3600 >= ".escapeshellarg($CurrentDate).
	" AND DateOpenProject<=".escapeshellarg($CurrentDate).
	"  )  AND ".
	" (project.id=design.id_Project) AND ".
	" (employee.id=design.id_Employee) AND ".
	" (department.id=employee.id_Department) AND ".
	" (office.id=employee.id_Office) AND ".
	"(project.id <> 913) AND ".//кроме заявления
	" department.id=".escapeshellarg(21). " AND ".     //id=21 Конструкторский
	" office.id=".escapeshellarg(12);                  //id=12 бюро2 Куклина
	$Query_KO = $Query_KO." GROUP BY project.id";
	if ( !($dbResult = mysql_query($Query_KO, $link)) )
	{
		//print "Выборка $Query_KO не удалась!\n".mysql_error();
		processingError("$Query_KO ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		$OpenWork['KO'] = mysql_num_rows($dbResult);
	}
	//2 столбец==============================================================================
	//оконченные работы   за указанный период времени
	$QueryCloseWork = "SELECT StatusProject, COUNT(id) AS Total  FROM project WHERE 1=1 ";
	$QueryCloseWork = $QueryCloseWork.$QueryInterval." GROUP BY StatusProject";
	//заносим в массив кол-во оконченных работ
	if ( !($dbResultCW = mysql_query($QueryCloseWork, $link)) )
	{
		//print "Выборка $QueryCloseWork не удалась!\n".mysql_error();
		processingError("$QueryCloseWork ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $rowCW = mysql_fetch_array($dbResultCW, MYSQL_BOTH) )
		{
			$CloseWork[$rowCW['StatusProject']] = $rowCW['Total'];
		}
	}
	//добавляем 4 строку- кол-во работ бюро механизации КО  -выполненные работы
	$Query_KO = "SELECT Number_Project ".
	"FROM project,design, employee, department, office ".
	"WHERE ".
	" (project.id=design.id_Project) AND ".
	" (employee.id=design.id_Employee) AND ".
	" (department.id=employee.id_Department) AND ".
	" (office.id=employee.id_Office) AND ".
	"(project.id <> 913) AND ".//кроме заявления
	" department.id=".escapeshellarg(21). " AND ".
	" office.id=".escapeshellarg(12); //id=12 бюро2 Куклина
	$Query_KO = $Query_KO.$QueryInterval." GROUP BY project.id";
	if ( !($dbResult = mysql_query($Query_KO, $link)) )
	{
		//print "Выборка $Query_KO не удалась!\n".mysql_error();
		processingError("$Query_KO ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		$CloseWork['KO'] = mysql_num_rows($dbResult);
	}
	//3 столбец ====================================================================================
	//определим интервал дат по которым ведется поиск от начала заполнения базы
	$QueryToInterval =  getIntervalToDate(
	$_SESSION['lstMonth'],
	$_SESSION['lstYear'],
	"project",
	"DateCloseProject",
	$_SESSION['rbPeriod']);

	//оконченные работы с накоплением
	$QueryCloseWorkAll = "SELECT StatusProject, COUNT(id) AS Total  FROM project WHERE 1=1 ";
	$QueryCloseWorkAll = $QueryCloseWorkAll.$QueryToInterval." GROUP BY StatusProject";
	if ( !($dbResultCWA = mysql_query($QueryCloseWorkAll, $link)) )
	{
		//print "Выборка $QueryCloseWork не удалась!\n".mysql_error();
		processingError("$QueryCloseWorkAll ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $rowCWA = mysql_fetch_array($dbResultCWA, MYSQL_BOTH) )
		{
			$CloseWorkAll[$rowCWA['StatusProject']] = $rowCWA['Total'];
		}
	}
	//добавляем 4 строку- кол-во работ бюро механизации КО  -выполненные работы всего
	$Query_KO_All = "SELECT Number_Project ".
	"FROM project,design, employee, department, office ".
	"WHERE ".
	" (project.id=design.id_Project) AND ".
	" (employee.id=design.id_Employee) AND ".
	" (department.id=employee.id_Department) AND ".
	" (office.id=employee.id_Office) AND ".
	"(project.id <> 913) AND ".//кроме заявления
	" department.id=".escapeshellarg(21). " AND ".
	" office.id=".escapeshellarg(12). //id=12 бюро2 Куклина
	$QueryToInterval.
	" GROUP BY project.id";
	if ( !($dbResult = mysql_query($Query_KO_All, $link)) )
	{
		//print "Выборка $Query_KO не удалась!\n".mysql_error();
		processingError("$Query_KO_All ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		$CloseWorkAll['KO'] = mysql_num_rows($dbResult);
	}
	//4 столбец ====================================================================
	foreach($arr_nul as $index=>$value)
	{
		$QueryCloseWork_ManHour =
		"SELECT Number_Project ".
		"FROM project,design, employee, department, office ".
		"WHERE ".
		" (project.id=design.id_Project) AND ".
		" (employee.id=design.id_Employee) AND ".
		" (department.id=employee.id_Department) AND ".
		" (office.id=employee.id_Office) AND ".
		"(project.id <> 913) AND ".//кроме заявления
		"(design.Man_Hour_Sum>".escapeshellarg(0).") AND ".
		"(design.StatusBVP>".escapeshellarg(0).") AND ".
		"(project.StatusProject = ".escapeshellarg($index).")".
		$QueryInterval.
		" GROUP BY project.id";

		if ( !($dbResult = mysql_query($QueryCloseWork_ManHour, $link)) )
		{
			print "Выборка  не удалась!<br />\n".mysql_error();
			//processingError("$QueryCloseWorkAll_ManHour ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			$CloseWork_ManHour[$index] = mysql_num_rows($dbResult);
		}
	}
	//расчет для 4 строки для КО
	$QueryCloseWork_ManHour =
	"SELECT Number_Project ".
	"FROM project,design, employee, department, office ".
	"WHERE ".
	" (project.id=design.id_Project) AND ".
	" (employee.id=design.id_Employee) AND ".
	" (department.id=employee.id_Department) AND ".
	" (office.id=employee.id_Office) AND ".
	"(project.id <> 913) AND ".//кроме заявления
	"(design.Man_Hour_Sum>".escapeshellarg(0).") AND ".
	"(design.StatusBVP>".escapeshellarg(0).") AND ".
	" department.id=".escapeshellarg(21). " AND ".
	" office.id=".escapeshellarg(12). //id=12 бюро2 Куклина
	$QueryInterval.
	" GROUP BY project.id";
	if ( !($dbResult = mysql_query($QueryCloseWork_ManHour, $link)) )
	{
		print "Выборка  не удалась!<br />\n".mysql_error();
		//processingError("$QueryCloseWorkAll_ManHour ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		$CloseWork_ManHour['KO'] = mysql_num_rows($dbResult);
	}
	//==============================================================================
	//5 столбец ====================================================================
	foreach($arr_nul as $index=>$value)
	{
		$QueryCloseWorkAll_ManHour =
		"SELECT Number_Project ".
		"FROM project,design, employee, department, office ".
		"WHERE ".
		" (project.id=design.id_Project) AND ".
		" (employee.id=design.id_Employee) AND ".
		" (department.id=employee.id_Department) AND ".
		" (office.id=employee.id_Office) AND ".
		"(project.id <> 913) AND ".//кроме заявления
		"(design.Man_Hour_Sum>".escapeshellarg(0).") AND ".
		"(design.StatusBVP>".escapeshellarg(0).") AND ".
		"(project.StatusProject = ".escapeshellarg($index).")".
		$QueryToInterval.
		" GROUP BY project.id";
		if ( !($dbResult = mysql_query($QueryCloseWorkAll_ManHour, $link)) )
		{
			print "Выборка  не удалась!<br />\n".mysql_error();
			//processingError("$QueryCloseWorkAll_ManHour ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			$CloseWorkAll_ManHour[$index] = mysql_num_rows($dbResult);
		}
	}
	//расчет для 4 строки для КО
	$QueryCloseWorkAll_ManHour =
	"SELECT Number_Project ".
	"FROM project,design, employee, department, office ".
	"WHERE ".
	" (project.id=design.id_Project) AND ".
	" (employee.id=design.id_Employee) AND ".
	" (department.id=employee.id_Department) AND ".
	" (office.id=employee.id_Office) AND ".
	"(project.id <> 913) AND ".//кроме заявления
	"(design.Man_Hour_Sum>".escapeshellarg(0).") AND ".
	"(design.StatusBVP>".escapeshellarg(0).") AND ".
	" department.id=".escapeshellarg(21). " AND ".
	" office.id=".escapeshellarg(12). //id=12 бюро2 Куклина
	$QueryToInterval.
	" GROUP BY project.id";
	if ( !($dbResult = mysql_query($QueryCloseWorkAll_ManHour, $link)) )
	{
		print "Выборка  не удалась!<br />\n".mysql_error();
		//processingError("$QueryCloseWorkAll_ManHour ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		$CloseWorkAll_ManHour['KO'] = mysql_num_rows($dbResult);
	}
	//==============================================================================
	print "<TABLE   Border=\"2\" CellSpacing=\"0\" CellPadding=\"2\">\n";
	print "<TR>\n";
	print "<TH rowspan=2>Критерий проекта</TH>\n";
	print "<TH colspan=5>статус</TH>";
	print "</TR>\n";
	print "<TR>\n";
	print "<TH>В работе<br>с учетом перех.<br>проектов</TH>\n";
	print "<TH>Планируемое<br> выполнение<br> в отч.периоде</TH>\n";
	print "<TH>Всего выполнено<br>с учетом текущего<br>отч.периода</TH>\n";
	print "<TH>По 2 столбцу<br />учитывая <br />трудозатраты</TH>\n";
	print "<TH>По 3 столбцу<br />учитывая <br />трудозатраты</TH>\n";
	print "</TR>\n";
	foreach($OpenWork as $index=>$value)
	{
		print "<TR align=center>\n";
		switch ($index)
		{
			case "OKC":
			print "<td align = left>Кап.строительство</td>";
			break;
			case "Plan":
			print "<td align = left>ПОФ плановый</td>";
			break;
			case "OverPlan":
			print "<td align = left>ПОФ внеплановый</td>";
			break;
			//4 строка для бюро механизации
			case "KO":
			print "<td align = left>Вкл. механизацию</td>";
			break;
		}
		printZeroTD($value);
		printZeroTD($CloseWork[$index]);
		printZeroTD($CloseWorkAll[$index]);
		printZeroTD($CloseWork_ManHour[$index]);
		printZeroTD($CloseWorkAll_ManHour[$index]);
	}
	print "<TR align=center>\n";
	printZeroTH("Всего:");
	$pop1 = array_pop($OpenWork);
	$pop2 = array_pop($CloseWork);
	$pop3 = array_pop($CloseWorkAll);
	$pop4 = array_pop($CloseWork_ManHour);
	$pop5 = array_pop($CloseWorkAll_ManHour);
	printZeroTH(array_sum($OpenWork));
	printZeroTH(array_sum($CloseWork));
	printZeroTH(array_sum($CloseWorkAll));
	printZeroTH(array_sum($CloseWork_ManHour));
	printZeroTH(array_sum($CloseWorkAll_ManHour));
	print "</table>\n";
}
//******************************************************************************
//ViewTableStatusProject_long() - подробный отчет по завершенности проектов
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function ViewTableStatusProject_long()
{
	global $link;
	$CurrentDate  = mktime(); //определяем текущую дату
/*	$QueryInterval =  getIntervalDate(
	$_SESSION['lstMonth'],
	$_SESSION['lstYear'],
	"project",
	"DateCloseProject",
	$_SESSION['rbPeriod']);*/
	
	$QueryMonth = getIntervalDate(
		$_SESSION['lstMonth'],
		$_SESSION['lstYear'],
		"design",
		"StatusBVP",
		$_SESSION['rbPeriod']);

 
	if (!empty($_SESSION['DateBegin']) OR !empty($_SESSION['DateEnd']))
	{
		$QueryInterval = " AND ((project.DateCloseProject>=".escapeshellarg($_SESSION['DateBegin']).
		") AND (project.DateCloseProject<".escapeshellarg($_SESSION['DateEnd']+3600*24)."))";
	}
	else {
		$QueryInterval =  getIntervalDate(
			$_SESSION['lstMonth'],
			$_SESSION['lstYear'],
			"project",
			"DateCloseProject",
			$_SESSION['rbPeriod']);
	}

	$Query = " SELECT project.id, project.Number_Project, project.Name_Project, project.Number_UTR,
	project.StatusProject, project.DateOpenProject, project.DateCloseProject,
	project.id_Customer, customer.Name_Customer, customer.Number_Customer, ".
	" SUM(design.Man_Hour_Sum) AS Man_Hour_Sum ".
	" FROM project, customer, design
	WHERE (project.id_Customer = customer.id ) AND ".
	"(project.id = design.id_Project) AND ".
	"(project.id <> 913) AND ".//кроме заявления
	"(design.StatusBVP > ".escapeshellarg(0).") AND ".
  	"(design.Man_Hour_Sum > ".escapeshellarg(0).") " ;
	if ( $_SESSION['lstCustomer'] != "Выбор заказчика" )
	{
		$Query4 = " AND (project.Id_Customer=".escapeshellarg($_SESSION['lstCustomer']).")";
	}
	else {
		$Query4 = "";
	}
	$Query = $Query.$QueryInterval.$Query4." GROUP BY project.id ORDER BY BINARY Number_Customer ASC, Number_Project ASC ";

	//file_put_contents("query.txt",$Query);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		print "<TABLE   Border=\"2\" CellSpacing=\"0\" CellPadding=\"2\">\n";
		print "<TR>\n";
		print "<TH rowspan=2>№№</TH>\n";
		print "<TH width=8% rowspan=2>Шифр работы</TH>\n";
		print "<TH width=8% rowspan=2>Номер УТР</TH>\n";
		print "<TH rowspan=2>Наименование работы</TH>\n";
		print "<TH rowspan=2>Заказчик</TH>\n";
		print "<TH colspan=3>Вид работ</TH>\n";
		print "<TH rowspan=2>Статус</TH>\n";
		print "<TH rowspan=2>Дата начала работ</TH>\n";
		print "<TH rowspan=2>Дата окончания работ</TH>\n";
        print "<TH rowspan=2>Труд-ты,<br/>Месяч.</TH>\n";
        print "<TH rowspan=2>Труд-ты,<br/>ВСЕГО</TH>\n";
		print "</TR>\n";
		print "<TR>\n";
		print "<TH><h6>Капитальное<br>строительство</h6></TH>\n";
		print "<TH><h6>Плановый</h6></TH>\n";
		print "<TH><h6>Внеплановый</h6></TH>\n";
		print "</TR>\n";
		$n = 0;
		$ManHourSum = 0;
		$ManHourSumMonth = 0;
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			print "<TR align=center>\n";
			if ($row['DateOpenProject'] == $row['DateCloseProject'] AND $row['DateOpenProject'] != 0)
			{
				print "<TR align=center BGCOLOR=#FFCCCC>\n"; //выделяем красным
			}
			printZeroTD(++$n);
			print("<td align=left>".$row['Number_Customer']."-".$row['Number_Project']."</td>");
			printZeroTD($row['Number_UTR']);
			print "<td align=left>".$row['Name_Project']."</td>\n";
			print "<td align=left>".$row['Name_Customer']."</td>\n";
			switch ($row['StatusProject'])
			{
				case "OKC":
				print "<td>X</td>";
				printZeroTD();
				printZeroTD();
				break;
				case "Plan":
				printZeroTD();
				print "<td>X</td>";
				printZeroTD();
				break;
				case "OverPlan":
				printZeroTD();
				printZeroTD();
				print "<td>X</td>";
				break;
			}
			if ($row['DateCloseProject'] < $CurrentDate)
			{
				printZeroTD("завершено");
			}
			else {
				printZeroTD();
			}
			printZeroTD(strftime("%d.%m.%y",$row['DateOpenProject'] + $_SESSION['localTime'] * 3600));
			printZeroTD(strftime("%d.%m.%y",$row['DateCloseProject'] + $_SESSION['localTime'] * 3600));
			$a = round(getManHourByProject_id($row['id'], $QueryMonth), 0);
			$b = round($row['Man_Hour_Sum'],0);
			printZeroTD($a);
            printZeroTD($b);
			$ManHourSumMonth = $ManHourSumMonth + $a;
			$ManHourSum = $ManHourSum + $b;
		}
		print "<TR>\n";
		print "<TH colspan=11 align=right>Итого:</TH>";
		printZeroTH($ManHourSumMonth);
		printZeroTH($ManHourSum);
		print "</table>\n";
	}
}
//******************************************************************************
//ViewTableStatusProject_month() - месячно-номенклатурный план работ
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function ViewTableStatusProject_month()
{
	global $link;
	$CurrentDate  = mktime(); //определяем текущую дату
	$QueryFromInterval =  getIntervalFromDate(
		$_SESSION['lstMonth'],
		$_SESSION['lstYear'],
		"project",
		"DateCloseProject",
		$_SESSION['rbPeriod']);

	$QueryToInterval = getIntervalToDate(
		$_SESSION['lstMonth'],
		$_SESSION['lstYear'],
		"project",
		"DateOpenProject",
		$_SESSION['rbPeriod']);


	$Query = " SELECT Number_Project, Name_Project, Number_UTR, StatusProject, DateOpenProject, DateCloseProject, id_Customer, Name_Customer, Number_Customer ".
	" FROM project, customer WHERE (project.id_Customer = customer.id ) AND (project.id <> 913)";//кроме заявления";
	if ( $_SESSION['lstCustomer'] != "Выбор заказчика" )
	{
		$Query4 = " AND (project.Id_Customer=".escapeshellarg($_SESSION['lstCustomer']).")";
	}
	else {
		$Query4 = "";
	}
	$Query = $Query.$QueryFromInterval.$QueryToInterval.$Query4." ORDER BY BINARY StatusProject, Number_Customer ASC, Number_Project ASC ";
	//file_put_contents('reportMonth.txt', $Query); 
	//file_put_contents("query.txt",$Query);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		print "<center><h3>Месячно-номенклатурный план работ</h3></center>";
		print "<TABLE   Border=\"2\" CellSpacing=\"0\" CellPadding=\"2\">\n";
		print "<TR>\n";
		print "<TH>№№</TH>\n";
		print "<TH>Шифр работы</TH>\n";
		print "<TH>Номер<br> УТР</TH>\n";
		print "<TH>Наименование работы</TH>\n";
		print "<TH>Заказчик</TH>\n";
		print "<TH>Дата начала работ</TH>\n";
		print "<TH>Дата окончания работ</TH>\n";
		print "<TH>Статус</TH>\n";
		print "</TR>\n";
		$n = 0;
		$result = array();
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{	
			$timeStart 	= strftime("%d.%m.%y",$row['DateOpenProject'] + $_SESSION['localTime'] * 3600);
			$timeEnd 	= strftime("%d.%m.%y",$row['DateCloseProject'] + $_SESSION['localTime'] * 3600);
			print "<TR>\n";
			if ($row['DateOpenProject'] == $row['DateCloseProject'] AND $row['DateOpenProject'] != 0)
			{
				print "<TR BGCOLOR=#FFCCCC>\n"; //выделяем красным
			}
			printZeroTD(++$n);
			printZeroTD($row['Number_Customer']."-".$row['Number_Project']);
			printZeroTD($row['Number_UTR']);
			printZeroTD($row['Name_Project']);
			printZeroTD($row['Name_Customer']);
			printZeroTD($timeStart);
			printZeroTD($timeEnd);
			if ($row['DateCloseProject'] < $CurrentDate)
			{
				printZeroTD("завершено");
			}
			else {
				printZeroTD();
			}
			$result[$n]['Number_Project'] 		= $row['Number_Customer']."-".$row['Number_Project'];
			$result[$n]['Number_UTR'] 			= str_replace("&nbsp;","",$row['Number_UTR']);
			$result[$n]['Name_Project'] 		= $row['Name_Project'];
			$result[$n]['Name_Customer'] 		= $row['Name_Customer'];
			$result[$n]['DateOpenProject'] 		= $timeStart;
			$result[$n]['DateCloseProject'] 	= $timeEnd;
			
		}
		print "</table>\n";
	}
return $result;
}
//*********************************************************************************************
// ViewTableDepartment()- функция отображения таблицы отделов
// Входные параметры:
//*********************************************************************************************

function ViewTableDepartment()
{
	global $link;
	switch ($_REQUEST['rbselect'])
	{
		case "select":
		//выборка всей базы
		break;
		case "insert":
		$Query = "INSERT INTO department SET Name_Department=".escapeshellarg($_REQUEST['Name_Department']);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "update":
		$Comma_separated = implode(",", $_REQUEST['department']);
		$Query = "UPDATE department SET  Name_Department=".escapeshellarg($_REQUEST['Name_Department']).
		"  WHERE id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "delete":
		$Comma_separated = implode(",", $_REQUEST['department']);
		$Query = "DELETE FROM department WHERE id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
	}
	$Query = "SELECT * from Department ORDER BY Name_Department";   //выборка из таблицы
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		print "<H4>Всего ".mysql_num_rows($dbResult)." позиций </H4>";
		//таблица HTML
		print "<TABLE  Border=\"1\" CellSpacing=\"0\" CellPadding=\"2\">\n";
		print " <Caption><H4>Таблица 'отдел'</H4></Caption>\n";
		print "<TR>\n";
		print "<TH>Метка</TH>".
		"<TH>id</TH>".
		"<TH>Наименование отдела</TH>";
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			//если выбрано копирование, то выделяем красным
			if ( $_REQUEST['rbselect'] == "copy" AND
			FALSE !== array_search($row['Id'],$_REQUEST['department'],TRUE) )
			{
				print "<TR align=center BGCOLOR=#FF0000>\n";
			}
			else {
				print "<TR align=center>\n";
			}
			print "<TD><input name=\"department[]\" type=\"checkbox\" value=\"{$row['Id']}\">";
			printZeroTD($row['Id']);
			printZeroTD($row['Name_Department']);
		}
		print "</TABLE>\n";
	}
}
//*************************************************************************************************************
// ViewTableOffice()- функция отображения таблицы бюро
// Входные параметры:
//*************************************************************************************************************

function ViewTableOffice()
{
	global $link;
	switch ($_REQUEST['rbselect'])
	{
		case "select":
		//выборка всей базы
		break;
		case "insert":
		$Query = "INSERT INTO office SET Name_Office=".escapeshellarg($_REQUEST['Name_Office']);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//   	print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "update":
		$Comma_separated = implode(",", $_REQUEST['office']);
		$Query = "UPDATE office SET  Name_Office=".escapeshellarg($_REQUEST['Name_Office']).
		"  WHERE id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//   	print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "delete":
		$Comma_separated = implode(",", $_REQUEST['office']);
		$Query = "DELETE FROM office WHERE id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
	}
	$Query = "SELECT * from Office ORDER BY Name_Office";   //выборка из таблицы
	if ( !($dbResult = mysql_query($Query, $link)))
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		print "<H4>Всего ".mysql_num_rows($dbResult)." позиций </H4>";
		//таблица HTML
		print "<TABLE  Border=\"1\" CellSpacing=\"0\" CellPadding=\"2\">\n";
		print " <Caption><H4>Таблица 'бюро'</H4></Caption>\n";
		print "<TR>\n";
		print "<TH>Метка</TH>".
		"<TH>id</TH>".
		"<TH>Наименование бюро</TH>";
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			//если выбрано копирование, то выделяем красным
			if ( $_REQUEST['rbselect'] == "copy" AND
			FALSE !== array_search($row['Id'],$_REQUEST['office'],TRUE))
			{
				print "<TR align=center BGCOLOR=#FF0000>\n";
			}
			else {
				print "<TR align=center>\n";
			}
			print "<TD><input name=\"office[]\" type=\"checkbox\" value=\"{$row['Id']}\">";
			printZeroTD($row['Id']);
			printZeroTD($row['Name_Office']);
		}
		print "</TABLE>\n";
	}
}
//*************************************************************************************************************
// ViewTablePost()- функция отображения таблицы должностей
// Входные параметры:
//*************************************************************************************************************

function ViewTablePost()
{
	global $link;
	switch ($_REQUEST['rbselect'])
	{
		case "select":
		//выборка всей базы
		break;
		case "insert":
		$Query = "INSERT INTO post SET Name_Post=".escapeshellarg($_REQUEST['Name_Post']);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "update":
		$Comma_separated = implode(",", $_REQUEST['post']);
		$Query = "UPDATE post SET  Name_Post=".escapeshellarg($_REQUEST['Name_Post']).
		"  WHERE id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "delete":
		$Comma_separated = implode(",", $_REQUEST['post']);
		$Query = "DELETE FROM post WHERE id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
	}
	$Query = "SELECT * from Post ORDER BY Name_Post";   //выборка из таблицы
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		print "<H4>Всего ".mysql_num_rows($dbResult)." позиций </H4>";
		//таблица HTML
		//print "<div class=\"layer\">\n";
		print "<TABLE  align=left Border=\"1\" CellSpacing=\"0\" CellPadding=\"2\">\n";
		print "<caption><H4>Таблица 'должность'</H4></caption>\n";
		print "<THEAD align=center bgcolor = #E8E8E8 valign=top>\n";
		print "<TR>\n";
		print "<TH>Метка</TH>".
		"<TH>id</TH>".
		"<TH>Наименование должности</TH>";
		print "</THEAD>";
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			//если выбрано копирование, то выделяем красным
			if ( $_REQUEST['rbselect'] == "copy" AND
			FALSE !== array_search($row['Id'],$_REQUEST['post'],TRUE))
			{
				print "<TR align=center BGCOLOR=#FF0000>\n";
			}
			else {
				print "<TR align=center>\n";
			}
			print "<TD><input name=\"post[]\" type=\"checkbox\" value=\"{$row['Id']}\">";
			printZeroTD($row['Id']);
			printZeroTD($row['Name_Post']);
		}
		print "</TABLE>\n";
		//print "</div>";
	}
}
//*************************************************************************************************************
// ViewTableWork()- функция отображения таблицы работ
// Входные параметры:
//*************************************************************************************************************

function ViewTableWork()
{
	global $link;
	switch ($_REQUEST['rbselect'])
	{
		case "select":
		//выборка всей базы
		break;
		case "insert":
		$Query = "INSERT INTO Work SET Name_Work=".escapeshellarg($_REQUEST['Name_Work']).
		", Type_Work=".escapeshellarg($_REQUEST['Type_Work']).
		", Comment=".escapeshellarg($_REQUEST['Comment']);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "update":
		$Comma_separated = implode(",", $_REQUEST['work']);
		$Query = "UPDATE work SET  Name_Work=".escapeshellarg($_REQUEST['Name_Work']).
		", Type_Work=".escapeshellarg($_REQUEST['Type_Work']).
		", Comment=".escapeshellarg($_REQUEST['Comment']).
		"  WHERE id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "delete":
		$Comma_separated = implode(",", $_REQUEST['work']);
		$Query = "DELETE FROM Work WHERE id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
	}
	$Query = "SELECT * from Work ORDER BY Type_Work";   //выборка из таблицы
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		print "<H4>Всего ".mysql_num_rows($dbResult)." позиций </H4>";
		//таблица HTML
		//print "<div class=\"layer\">\n";
		print "<TABLE  Width=\"100%\" Border=\"1\" CellSpacing=\"0\" CellPadding=\"2\">\n";
		print "<caption><H4>Таблица 'работа'</H4></caption>\n";
		print "<THEAD align=center bgcolor = #E8E8E8 valign=top>\n";
		print "<TR>\n";
		print "<TH>Код\n";
		print "<TH>Метка</TH>".
		"<TH>Наименование работы</TH>".
		"<TH>Виды работы</TH>".
		"<TH>Комментарий</TH>";
		print "</THEAD>";
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			//если выбрано копирование, то выделяем красным
			if ( $_REQUEST['rbselect'] == "copy" AND
			FALSE !== array_search($row['Id'],$_REQUEST['work'],TRUE))
			{
				print "<TR align=center BGCOLOR=#FF0000>\n";
			}
			else {
				print "<TR align=center>\n";
			}
			print "<TD>{$row['Id']}\n";
			print "<TD><input name=\"work[]\" type=\"checkbox\" value=\"{$row['Id']}\">".
			"<TD align=left>{$row['Name_Work']}".
			"<TD align=left>{$row['Type_Work']}".
			"<TD>{$row['Comment']}";
		}
		print "</TABLE>\n";
		//print "</div>";
	}
}
//***********************************************************************************************
// ViewTableProject()- функция отображения таблицы проектов
// Входные параметры:
//***********************************************************************************************

function ViewTableProject()
{
	global $link;
	//при нажатии добавить отдел, все выбранные отделы из списка появятся в табл.
	if (isset($_REQUEST['btnDep']))
	{
		$Comma_separated = implode(",", $_REQUEST['project']);
		$Comma_Depart = implode(",", $_REQUEST['Depart']);
		$QueryDep =  "UPDATE project SET listDep = ".escapeshellarg($Comma_Depart).
		" WHERE id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($QueryDep, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$QueryDep ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
	}
	//при нажатии удаляются все отделы
	if (isset($_REQUEST['btnDepDel']))
	{
		$Comma_separated = implode(",", $_REQUEST['project']);
		$QueryDep =  "UPDATE project SET listDep = ".escapeshellarg('0').
		" WHERE id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($QueryDep, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$QueryDep ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
	}
	//при нажатии на кнопку ВРД идет формирование выделенного проекта в документ ВРД исп Эксель
	if (isset($_REQUEST['btnDoc']))
	{
		//$Comma_separated = array_shift($_REQUEST['project']);
		$Comma_separated = implode(",", $_REQUEST['project']);
		$QuerySelect =  "  SELECT ".
		"  project.id, Number_Project, Name_Project, Name_Customer ".
		"  FROM project, customer ".
		"  WHERE  ".
		"  project.id_Customer = customer.id AND ".
		"  project.id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($QuerySelect, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$QuerySelect ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH))
			{
				//сохраняем ВРД
//				save_to_excel($row, "c:\\CommDocs\\");
                $_SESSION['vrd'] = $row;
              	header("Location: ../Report/report_excel/saveExcel_VRD.php");
                exit;
			}
		}
	}
	//добавление скана --------------------------------
	if (isset($_REQUEST['btnScan']))
	{
		if ($_FILES['upload']['error'] == UPLOAD_ERR_OK)
		{
			//копирование в папку Scan
			$baseName = basename($_FILES['upload']['name']);
			copy($_FILES['upload']['tmp_name'], "../img/Scan/".$baseName);
			$Comma_separated = implode(",", $_REQUEST['project']);
			$QueryUpload = "UPDATE project SET Scan=".escapeshellarg($baseName)." WHERE id IN ($Comma_separated)";
			if ( !($dbResult = mysql_query($QueryUpload, $link)) )
			{
				//print "запрос $Query не выполнен\n".mysql_error();
				processingError("$QueryUpload ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
		}
	}
	//удаление скана --------------------------------
	if (isset($_REQUEST['btnScanDel']))
	{
		$Comma_separated = implode(",", $_REQUEST['project']);
		$q = "SELECT Scan FROM project WHERE id IN ($Comma_separated)";
		$dbResult = mysql_query($q, $link);
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			unlink("../img/Scan/".$row['Scan']);
		}
		$QueryDelScan = "UPDATE project SET Scan=null WHERE id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($QueryDelScan, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$QueryDelScan ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
	}
	//проверяем, если нажата кнопка Найти- ищем
	if (isset($_REQUEST['findProject']))
	{
		$find = " AND Number_Project RLIKE \"^{$_REQUEST['Number_Project']}\"";
	}
	//проверка, нажата ли кнопка найти утр
	elseif (isset($_REQUEST['findProjectUTR']))
	{
		$find = " AND Number_UTR RLIKE \"^{$_REQUEST['Number_UTR']}\"";
	}
	else {
		//текущий месяц
		$QueryDate = getIntervalFromDate($_SESSION['CurrentMonth'],
		$_SESSION['CurrentYear'],
		"project",
		"DateCloseProject",
		"1-31");
	}
	$QueryDateBVP = getIntervalFromDate(1,
	2008,
	"design",
	"statusBVP",
	"1-31");
	$orderby = " ORDER BY BINARY Number_Customer";
	switch ($_REQUEST['rbchksort'])
	{
		case "sort_customer":
		$orderby = " ORDER BY BINARY Number_Customer ";
		break;
		case "sort_gip":
		$orderby = " ORDER BY BINARY Family, Number_Customer";
		break;
	}
	//формируем запрос текущего месяца
	$Query = "SELECT project.Id, project.Number_Project, project.Name_Project, project.Number_UTR, project.Number_TORO, project.listDep,
	employee.Family, employee.Name, employee.Patronymic, project.Scan,
	project.DateOpenProject, project.DateCloseProject, project.Report, project.Report_ManHour, project.Customer_Service,project.Plant_Destination,
	project.StatusProject,  customer.Name_Customer, customer.Number_Customer
	FROM project, customer, employee
	WHERE ".
	"project.id_Customer = customer.Id AND ".
	"project.Manager = employee.Id ".
	$find.
	$QueryDate.
	$orderby;   //выборка из таблицы
	//если выбрана опция сохранения результатов, то запрос сохраняем в сессии
	if (isset($_REQUEST['findProject']) OR isset($_REQUEST['findProjectUTR']) OR empty($_SESSION['saveQuery']))
	{
		$_SESSION['saveQuery'] = $Query;
	}
	if (isset($_REQUEST['btnSubmit']))
	{
		//подготавливаем строки запросов в зависимости от выбора переключателей изменнения даты
		$strDate = array();
		foreach($_REQUEST['chkdate'] as $key=>$value)
		{
			switch ($value)
			{
				case "date_open":
				$strDate[] = "DateOpenProject = ".escapeshellarg($_REQUEST['DateOpen']);
				break;
				case "date_close":
				$strDate[] = "DateCloseProject = ".escapeshellarg($_REQUEST['DateClose']);
				break;
			}
		}
        if(count($strDate) > 0)
        {
           $strDate = implode(",", $strDate);
        }  else
        {
           $strDate = "";
        }

		switch ($_REQUEST['rbselect'])
		{
			case "select":
			//выборка всей базы
			$_SESSION['saveQuery'] = $Query;
			break;
			case "insert":
			$QueryInsert = "INSERT INTO project SET Number_Project=".escapeshellarg($_REQUEST['Number_Project']).
			", Name_Project=".escapeshellarg($_REQUEST['Name_Project']).
			", Number_UTR=".escapeshellarg($_REQUEST['Number_UTR']).
			", Number_TORO=".escapeshellarg($_REQUEST['Number_TORO']).
			", Manager=".escapeshellarg($_REQUEST['Manager']).
			", id_Customer=".escapeshellarg($_REQUEST['Customer']).
			", StatusProject = ".escapeshellarg($_REQUEST['rbstatus']).
			", DateOpenProject = ".escapeshellarg($_REQUEST['DateOpen']).
			", DateCloseProject = ".escapeshellarg($_REQUEST['DateClose']).
			", Report = ".escapeshellarg($_REQUEST['Report']).
			", Report_ManHour = ".escapeshellarg($_REQUEST['Report_ManHour']).
			", Customer_Service = ".escapeshellarg($_REQUEST['Customer_Service']).
			", Plant_Destination = ".escapeshellarg($_REQUEST['Plant_Destination']);

			if ( !($dbResult = mysql_query($QueryInsert, $link)) )
			{
				//print "запрос $Query не выполнен\n".mysql_error();
				processingError("$QueryInsert ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			break;
			case "update":
			$Comma_separated = implode(",", $_REQUEST['project']);
			$QueryUpdate =  "  UPDATE project SET ".
			"  Number_Project=".escapeshellarg($_REQUEST['Number_Project']).
			", Name_Project=".escapeshellarg($_REQUEST['Name_Project']).
			", Number_UTR=".escapeshellarg($_REQUEST['Number_UTR']).
			", Number_TORO=".escapeshellarg($_REQUEST['Number_TORO']).
			", id_Customer=".escapeshellarg($_REQUEST['Customer']).
			", Manager=".escapeshellarg($_REQUEST['Manager']).
			", StatusProject = ".escapeshellarg($_REQUEST['rbstatus']).
			", DateOpenProject = ".escapeshellarg($_REQUEST['DateOpen']).
			", DateCloseProject = ".escapeshellarg($_REQUEST['DateClose']).
			", Report = ".escapeshellarg($_REQUEST['Report']).
			", Report_ManHour = ".escapeshellarg($_REQUEST['Report_ManHour']).
			", Customer_Service = ".escapeshellarg($_REQUEST['Customer_Service']).
			", Plant_Destination = ".escapeshellarg($_REQUEST['Plant_Destination']).
			"  WHERE id IN ($Comma_separated)";

			if ( !($dbResult = mysql_query($QueryUpdate, $link)) )
			{
				print "запрос $Query не выполнен\n".mysql_error();
				//processingError("$QueryUpdate ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			break;
			case "delete":
			$Comma_separated = implode(",", $_REQUEST['project']);
			//инициируем замену каскадного удаления и з связанных таблиц
			$QueryDelete1 = "DELETE FROM task WHERE id_Project IN ($Comma_separated)";
			$QueryDelete2 = "DELETE FROM markbvp WHERE id_Project IN ($Comma_separated)";
			$QueryDelete3 = "DELETE FROM project WHERE id IN ($Comma_separated)";
			if ( !($dbResult = mysql_query($QueryDelete1, $link)) )
			{
				//print "запрос $Query не выполнен\n".mysql_error();
				processingError("$QueryDelete1 ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			if ( !($dbResult = mysql_query($QueryDelete2, $link)) )
			{
				//print "запрос $Query не выполнен\n".mysql_error();
				processingError("$QueryDelete2 ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			if ( !($dbResult = mysql_query($QueryDelete3, $link)) )
			{
				//print "запрос $Query не выполнен\n".mysql_error();
				processingError("$QueryDelete3 ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			break;
			case "customer":
			$Comma_separated = implode(",", $_REQUEST['project']);
			$QueryCustomer = "UPDATE project SET  id_Customer=".escapeshellarg($_REQUEST['Customer']).
			"  WHERE id IN ($Comma_separated)";
			if ( !($dbResult = mysql_query($QueryCustomer, $link)) )
			{
				//print "запрос $Query не выполнен\n".mysql_error();
				processingError("$QueryCustomer ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			break;
			case "status":  //изменяем статус и время
			$Comma_separated = implode(",", $_REQUEST['project']);
			$QueryStatus =  "  UPDATE project SET ".
			"  StatusProject = ".escapeshellarg($_REQUEST['rbstatus']).
			$strDate."  WHERE id IN ($Comma_separated)";
			if ( !($dbResult = mysql_query($QueryStatus, $link)) )
			{
				//print "запрос $Query не выполнен\n".mysql_error();
				processingError("$QueryStatus ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			break;
			case "close":  //изменяем только время
			$Comma_separated = implode(",", $_REQUEST['project']);
			$QueryClose =  "  UPDATE project SET ".
			$strDate."  WHERE id IN ($Comma_separated)";

			if ( !($dbResult = mysql_query($QueryClose, $link)) )
			{
				//print "запрос $Query не выполнен\n".mysql_error();
				processingError("$QueryClose ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			break;
		}
		} elseif (isset($_REQUEST['btnSearch']))
	{
	  //выбираем по какой дате проводить поиск-начала или окончания
	  switch ($_REQUEST['rbOpenClose'])
	  {
	    case "open":
      $OpenClose = "DateOpenProject";
	    break;

	    case "close":
      $OpenClose = "DateCloseProject";
      break;

	  }
		//выбираем статус проекта- по умолчанию - все
		switch ($_REQUEST['rbstatusSearch'])
		{
			case "All":
			$Status = "";
			break;
			default:
			$Status = " AND  StatusProject = ".escapeshellarg($_REQUEST['rbstatusSearch']);
		}
		if ($_REQUEST['CustomerSearch'] != "Выбор заказчика" )
		{
			$QueryCustomer = " AND (project.id_Customer=".escapeshellarg($_REQUEST['CustomerSearch']).")";
		}
		else {
			$QueryCustomer = "";
		}
		// составляем запрос на отображение информации
		//из списка указанных месяцев составляем сложный запрос-объединение
		foreach($_REQUEST['chkMonthSearch'] as $key=>$value)
		{
			if (!empty($_REQUEST['DateBegin']) AND !empty($_REQUEST['DateEnd']))
			{
				$QueryDate = " AND ((project.$OpenClose>=".escapeshellarg($_REQUEST['DateBegin']).
				") AND (project.$OpenClose<".escapeshellarg($_REQUEST['DateEnd']+3600*24)."))";
			}
			else {
				$QueryDate = getIntervalDate($key,
				$_REQUEST['rbYearSearch'],
				"project",
				"$OpenClose",
				"1-31");
			}
			$QT[] = "SELECT project.Id, project.Number_Project, project.Name_Project, project.Number_UTR, project.Number_TORO, project.listDep,
			employee.Family, employee.Name, employee.Patronymic, project.Scan,
			project.DateOpenProject, project.DateCloseProject, project.Report, project.Report_ManHour, project.Customer_Service,project.Plant_Destination,
			project.StatusProject,  customer.Name_Customer, customer.Number_Customer
			FROM project, customer, employee
			WHERE ".
			"project.id_Customer = customer.Id AND ".
			"project.Manager = employee.Id ".
			$Status.
			$QueryCustomer.
			$QueryDate;   //выборка из таблицы
		}
		$Query = "";
		$Count = count($QT);
		foreach($QT as $Key=>$Value)
		{
			if (($Count - 1) == $Key)
			{
				$Query = $Query."(".$Value.")";
				break;
			}
			else {
				$Query = $Query."(".$Value.") UNION ";
			}
		}
		$_SESSION['saveQuery'] = $Query.$orderby;
		print_r($_SESSION['saveQuery']);
		//file_put_contents('search.txt', $Query);
	}
	//===================================================================================
	
	if ( !($dbResult = mysql_query($_SESSION['saveQuery'], $link)) )
	{
		print "Выборка не удалась!\n".mysql_error();
		processingError(mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		print "<H4>Всего ".mysql_num_rows($dbResult)." позиций </H4>";
       print  $_REQUEST['hide'];

		//таблица HTML
		//print "<div class=\"layer\">\n";
		print "<TABLE   Border=\"1\" CellSpacing=\"0\" CellPadding=\"2\">\n";
		print "<caption><H4>Таблица 'проекты'</H4></caption>\n";
		print "<TR>\n";
		print "<TH>&nbsp;</TH>".
		"<TH>Номер заявки</TH>". 				//1 столбец
		"<TH>Название проекта</TH>". 			//2 столбец
		"<TH>Номер УТР</TH>".                  //3 столбец
		"<TH>Номер заявки <br/>на ремонт</TH>".//4 столбец
		"<TH>ГИП</TH>".                  		//5 столбец
		//"<TH>Заказчик</TH>".                 //5 столбец
		"<TH>Дата <br />регистр.</TH>".     	//6 столбец
		"<TH>Срок оконч.</TH>".             	//7 столбец
		"<TH width='50px'>Предпол. отделы</TH>".//7а столбец
		"<TH width='50px'>Отделы</TH>".		//8 столбец
		"<TH>От</TH>".            				//9 столбец
		"<TH>Кому</TH>".            			//10 столбец
		"<TH width='50px'>Примеч</TH>".        //11 столбец
		"<TH>Дата выдачи<br />задания</TH>".   //12 столбец
		"<TH>Марка</TH>".            			//13 столбец
		"<TH>Коммент. к марке</TH>".  			//14 столбец
		"<TH>Дата выдачи<br />в БВП</TH>".     //15 столбец
		"<TH>Дата выдачи<br />заказчику</TH>"; //16 столбец
		//если вкл опция "скрыть" то 2 столбца могут быть скрыты
		if ($_REQUEST['hide'] == 'TRUE')
		{
			print "<TH>Номер акта</TH>";        	    //17 столбец
			print "<TH>Тр-ты по<br />акту</TH>";        //18 столбец
			print "<TH>Служба<br />заказчика</TH>";     //19 столбец
			print "<TH>Цеха</TH>";                      //20 столбец

		}
		print "<TH>Фактич <br />тр-ты</TH>";       //21 столбец
		print "</THEAD>";
		$n = 0;
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			$i = 0;  //счетчик на подстроки
			//запрос на выборку данных из табл. task
			$arrayTask = getArrayFromTask($row['Id']);
			//запрос на выборку данных из табл. mark
			$arrayMark = getArrayFromMark($row['Id']);
			

			//подсчитаем размерность массивов
			$count1 = count($arrayTask['From']);
			$count2 = count($arrayMark['Mark']);
			//сливаем массивы отделов
			$arrayDep = array_merge($arrayTask['From'], $arrayTask['To']);
			$arrayDep = array_unique($arrayDep);
			asort($arrayDep);  //сортируем массив по возрастанию
			//преобразуем в строку
			$Comma_separated = implode("<br />", $arrayDep);
			//находим наибольший размер массивов
			if ($count1 >= $count2)
			{
				$maxcount = $count1;
			}
			else {
				$maxcount = $count2;
			}
			if($maxcount == 0)
			{
				$maxcount = 1; //rowspan может принимать Любое целое положительное число больше 1.
			}
			//если выбрано копирование, то выделяем красным
			if ( $_REQUEST['rbselect'] == "copy" AND
			FALSE !== array_search($row['Id'],$_REQUEST['project'],TRUE))
			{
				print "<TR align=left BGCOLOR=#FF0000>\n";
			}
			else
			if ($row['DateOpenProject'] == $row['DateCloseProject'] AND $row['DateOpenProject'] != 0)
			{
				print "<TR align=left BGCOLOR=#FFCCCC>\n"; //выделяем красным
				}  else {
				print "<TR align=left>\n";
			}
			print "<TD rowspan=$maxcount><input name=\"project[]\" type=\"checkbox\" value=\"{$row['Id']}\">";
			if ($row['Scan'] != null)
			{
				print "<a href='../img/Scan/{$row['Scan']}'><img src=\"../img/picture_link.png\" /></a>";
			}
			print "<TD rowspan=$maxcount><a href=\"AdminProject_Task.php?idProject={$row['Id']}\"  title='{$row['Name_Customer']}'>".$row['Number_Customer']."-".$row['Number_Project']."</a></TD>";//1 столбец
			print "<TD rowspan=$maxcount><a href=\"AdminProject_Mark.php?idProject={$row['Id']}\">{$row['Name_Project']}</a></TD>";	 //2 столбец
			//3 столбец  ---------------------------------------------------------
			if ($row['Number_UTR'] != '')
			{
				print "<td rowspan=$maxcount>".$row['Number_UTR']."</td>";
			}
			else {
				print "<td rowspan=$maxcount>&nbsp;</td>";
			}
			//4 столбец  ----------------------------------------------------------
			if ($row['Number_TORO'] != '')
			{
				print "<td rowspan=$maxcount>".$row['Number_TORO']."</td>";
			}
			else {
				print "<td rowspan=$maxcount>&nbsp;</td>";
			}			
			//5 столбец  ----------------------------------------------------------
			$fio = $row['Family']." ".mb_substr($row['Name'],0,1,'utf8').".".mb_substr($row['Patronymic'],0,1,'utf8');
			print "<TD rowspan=$maxcount nowrap align=left><a href=\"AdminProject_Comment.php?idProject={$row['Id']}\">".$fio.".</a></TD>";
			//5 столбец  ----------------------------------------------------------
			/*		if ( $row['Name_Customer'] == "без названия" )
			{
				print "<td rowspan=$maxcount>&nbsp;</td>";
			}
			else {
				print "<td rowspan=$maxcount>".$row['Name_Customer']."</td>";
			}
			*/
			//6 столбец  ----------------------------------------------------------
			if ($row['DateOpenProject'] > 0)
			{
				$timeOpen = strftime("%d.%m.%y",$row['DateOpenProject']+ $_SESSION['localTime'] * 3600);
				print "<td rowspan=$maxcount>".$timeOpen."</td>";
			}
			else {
				print "<td rowspan=$maxcount>&nbsp;</td>";
			}
			//7 столбец  ----------------------------------------------------------
			if ($row['DateCloseProject'] > 0)
			{
				$timeClose = strftime("%d.%m.%y",$row['DateCloseProject']+ $_SESSION['localTime'] * 3600);
				print "<td rowspan=$maxcount>".$timeClose."</td>";
			}
			else {
				print "<td rowspan=$maxcount>&nbsp;</td>";
			}
			//7 столбец  ----------------------------------------------------------
			if ($row['listDep'] > 0)
			{
				$arrayListDep = explode(',', $row['listDep']);  //переводим в массив
				$arrDep = array_map('getNameDepartment',$arrayListDep); //названия отделов
				asort($arrDep);        //сортируем
				$strDep = implode("<br />", $arrDep); //преобразуем в строку
				print "<td style='color:green' rowspan=$maxcount>".$strDep."</td>";
			}
			else {
				print "<td rowspan=$maxcount>&nbsp;</td>";
			}
			//8 столбец  ----------------------------------------------------------
			if ($Comma_separated != '')
			{
				print "<td rowspan=$maxcount>$Comma_separated</td>";
			}
			else {
				print "<td rowspan=$maxcount>&nbsp;</td>";
			}
			printZeroTD($arrayTask['From'][$i]);	   					//9 столбец
			printZeroTD($arrayTask['To'][$i]);	   						//10 столбец
			printZeroTD($arrayTask['Comment'][$i]);   	 				//11 столбец
			//12 столбец ---------------------------------------
			if ($arrayTask['Date'][$i] == '01.01.70')
			{
				printZeroTD();
			}
			else {
				printZeroTD($arrayTask['Date'][$i]);							//12 столбец
			}
			printZeroTD($arrayMark['Mark'][$i].$arrayMark['NumberMark'][$i]); 	//13 столбец
			printZeroTD($arrayMark['Comment'][$i]);	   							//14 столбец
			printZeroTD($arrayMark['DateBVP'][$i]);	   							//15 столбец
			printZeroTD($arrayMark['DateCustomer'][$i]);						//16 столбец
			//если вкл опция "скрыть" то 2 столбца могут быть скрыты
			if ($_REQUEST['hide'] == 'TRUE')
			{
				//17 столбец ----------------------------------------------------
				if ($row['Report'] == '')
				{
					print "<td rowspan=$maxcount>&nbsp;</td>";
				}
				else {
					print "<td rowspan=$maxcount>".$row['Report']."</td>";
				}
				//18 столбец ----------------------------------------------------
				if ($row['Report_ManHour'] == 0)
				{
					print "<td rowspan=$maxcount>&nbsp;</td>";
				}
				else {
					print "<td rowspan=$maxcount>".$row['Report_ManHour']."</td>";
				}
				//19 столбец ----------------------------------------------------
				if ($row['Customer_Service'] == '')
				{
					print "<td rowspan=$maxcount>&nbsp;</td>";
				}
				else {
					print "<td rowspan=$maxcount>".$row['Customer_Service']."</td>";
				}
				//20 столбец ----------------------------------------------------
				if ($row['Plant_Destination'] == '')
				{
					print "<td rowspan=$maxcount>&nbsp;</td>";
				}
				else {
					print "<td rowspan=$maxcount>".$row['Plant_Destination']."</td>";
				}

			}
			//21 столбец ----------------------------------------------------
			$man_hour = getManHourByProject_id($row['Id'], $QueryDateBVP);
			if ($man_hour > 0)
			{
				print "<td rowspan=$maxcount>".round($man_hour, 0)."</td>";
			}
			else {
				print "<td rowspan=$maxcount>&nbsp;</td>";
			}
			print "</tr>";
			$i = $i + 1;
			//если количество марок или отделов больше 1, то выполняется блок ниже
			//==========================начало блока ===============================================================
			while ($i <= $maxcount - 1)
			{
				print "<tr>\n";
				printZeroTD($arrayTask['From'][$i]);	   					//9 столбец
				printZeroTD($arrayTask['To'][$i]);	   						//10 столбец
				printZeroTD($arrayTask['Comment'][$i]);                     //11 столбец
				//12 столбец ---------------------------------------
				if ($arrayTask['Date'][$i] == '01.01.70')
				{
					printZeroTD();
				}
				else {
					printZeroTD($arrayTask['Date'][$i]);
				}
				printZeroTD($arrayMark['Mark'][$i].$arrayMark['NumberMark'][$i]);	//13 столбец
				printZeroTD($arrayMark['Comment'][$i]);	   							//14 столбец
				printZeroTD($arrayMark['DateBVP'][$i]);	   							//15 столбец
				printZeroTD($arrayMark['DateCustomer'][$i]);						//16 столбец
				$i = $i + 1;
				print "</tr>\n";
			}
			//==========================конец блока ===============================================================
			++$n;//в каждой n будут храниться строка результата
		//заносим результаты в массив для его сериализации и передачи в эксель
			$result[$n]['arrayTask'] 			= $arrayTask;
			$result[$n]['arrayMark'] 			= $arrayMark;
			$result[$n]['Number_Project'] 		= $row['Number_Customer']."-".$row['Number_Project'];//регистрационный номер в УПИРиИ
			$result[$n]['Name_Project']			= $row['Name_Project'];
			$result[$n]['Number_UTR'] 			= $row['Number_UTR'];
			$result[$n]['FIO'] 					= $fio;
			$result[$n]['DateOpenProject'] 		= $timeOpen;//дата начала проекта
			$result[$n]['DateCloseProject'] 	= $timeClose; //дата завершения проекта
			$result[$n]['listDep'] 				= str_replace("<br />",", ",$strDep);//список предполагаемых отделов (справочная информация в виде заметки гл.инж)
			$result[$n]['Dep'] 					= str_replace("<br />",", ",$Comma_separated);//отделы уч-ие в проектировании
			$result[$n]['Report'] 				= str_replace("&nbsp;","",$row['Report']);//номер акта
			$result[$n]['Report_ManHour'] 		= $row['Report_ManHour'];//трудозатраты показанные в акте
			$result[$n]['Customer_Service'] 	= str_replace("&nbsp;","",$row['Customer_Service']);//служба заказчика
			$result[$n]['Plant_Destination'] 	= str_replace("&nbsp;","",$row['Plant_Destination']);//цеха для которых осуществляется данная работа
			$result[$n]['Man_Hour'] 			= round($man_hour, 0);//трудозатраты по факту
			$result[$n]['count'] 				= $maxcount; //количество объединяемых строк в экселе
		}
		print "</TABLE>\n";
		//print "</div>";

	}
return $result;
}
//***********************************************************************************************
//save_to_excel($row, $path)- функция заполнения шаблона ВРД данными из БД
// Входные параметры: $row - массив значений
//$path - путь сохранения результата
// Выходные параметры:
// НЕ ИСПОЛЬЗУЕМАЯ ФУНКЦИЯ, ПОДЛЕЖИТ УДАЛЕНИЮ

//***********************************************************************************************

function save_to_excel($row, $path)
{
	$str = "Кемеровское ОАО 'Азот'\n";
	//запрос на выборку данных из табл. mark
	$arrayMark = getArrayFromMark($row['id']);
	//сливаем вместе марки и номера
	foreach($arrayMark['Mark'] as $key=>$value)
	{
		if ($arrayMark['NumberMark'][$key] == "")
		{
			$resultMark[][$arrayMark['id'][$key]] =  $arrayMark['Mark'][$key];
		}
		else {
			$resultMark[][$arrayMark['id'][$key]] =  $arrayMark['Mark'][$key].".".$arrayMark['NumberMark'][$key];
		}
		if ($arrayMark['NumberChange'][$key] == "")
		{
			$change[] =  "";
		}
		else
		{
			$change[] = $arrayMark['NumberChange'][$key];
		}
	}
	include_once("../modules/lib/PHPExcel/IOFactory.php");
	$objPHPExcel = PHPExcel_IOFactory::load("../modules/template/template_format.xls");
	$objPHPExcel->setActiveSheetIndex(0);
	$aSheet = $objPHPExcel->getActiveSheet();
	$cell = 5;
	$n = 1;
	foreach($resultMark as $arr)
	{
		foreach($arr as $id=>$NameMark)
		{
			$aSheet->setCellValue('B'.$cell,$n); //номер
			//обозначение
			$aSheet->setCellValue('D'.$cell,$row['Number_Project']."-".$NameMark);
			//наименование
			$aSheet->setCellValue('N'.$cell,getMark($id,"Comment_Mark"));
			//номер изменения
			if ($change[$n - 1] == "")
			{
				$aSheet->setCellValue('AI'.$cell,$change[$n - 1]);
			}
			else
			{
				$aSheet->setCellValue('AI'.$cell,"Изм. ".$change[$n - 1]);
			}
			$cell = $cell + 2;
			$n++;
		}
	}
	//заполнение основной надписи
	$aSheet->setCellValue('R50',$row['Number_Project']);
	$aSheet->setCellValue('AC50',$row['Number_Project']);
	$aSheet->setCellValue('N55',$row['Name_Project']);
	$aSheet->setCellValue('N52',$str.$row['Name_Customer']);
	//создаем объект класса-писателя
	include_once("../modules/lib/PHPExcel/Writer/Excel5.php");
	$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
	//сохраняем на диск
	$objWriter->save($path.$row['Number_Project'].mb_convert_encoding('-ВРД','windows-1251','utf-8').'.xls');


}
//***********************************************************************************************
//getManHourByProject_id($idProject, $QueryDateBVP)- функция возвращет трудозатраты  для проекта
// Входные параметры: $idProject - id проекта
//					  $QueryDateBVP - временной интервал
// Выходные параметры: значение трудозатрат
//***********************************************************************************************

function getManHourByProject_id($project_id, $QueryDateBVP)
{
	global $link;
	$Query = "SELECT SUM(Man_Hour_Sum) as sum FROM design WHERE id_Project=".escapeshellarg($project_id);
	$Query = $Query.$QueryDateBVP;
	//file_put_contents("query.txt",$Query);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		if (mysql_num_rows($dbResult) == 0)
		{
			return 0;
		}
		else {
			$row = mysql_fetch_array($dbResult);
			return $row['sum'];
		}
	}
}

//***********************************************************************************************
//getManHourByProjectEmployee($employee_id, $project_id, $intervalDate)- функция возвращет трудозатраты  для проекта по конкретному человеку
// Входные параметры: 
//						$employee_id - ид сотрудника
//						$idProject - id проекта
//					  	$intervalDate - временной интервал
// Выходные параметры: значение трудозатрат
//***********************************************************************************************
function getManHourByProjectEmployee($employee_id, $project_id, $intervalDate)
{
	global $link;
	$query = 	"SELECT SUM(design.Man_Hour_Sum) AS sum".
				" FROM".
				" design, employee, project".
				" WHERE ".
				" (design.id_employee=employee.Id) AND ".
				" (design.id_Project=project.Id) AND ".
				" employee.id=".escapeshellarg($employee_id);
	$query = $query.$project_id.$intervalDate." GROUP BY employee.id";


if ( !($dbResult = mysql_query($query, $link)) ) 
		{
			//print "Выборка $Query не удалась!\n".mysql_error();
			processingError($query.mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			if (mysql_num_rows($dbResult) == 0)	
			{
				return 0;
			}
			else
			{
				$row = mysql_fetch_array($dbResult);
				return $row['sum'];
			}
		}	
}

//***********************************************************************************************
//getArrayFromTask($idProject)- функция возвращет массив данных из табл Task
// Входные параметры: $idProject - id проекта
// Выходные параметры: массив данных
//***********************************************************************************************

function getArrayFromTask($idProject)
{
	global $link;
	$Query = " SELECT task.id, task.id_Project, task.From_Dep, task.To_Dep, task.Comment, ".
	" task.DateExtradition,  department1.Name_Department as Name1, department2.Name_Department as Name2".
	" FROM task, project, department as department1, department as department2 ".
	" WHERE ".
	" (task.From_Dep = department1.id) AND ".
	" (task.To_Dep = department2.id) AND ".
	" (task.id_Project = project.id) AND ".
	" (task.id_Project =".escapeshellarg($idProject).")".
	" ORDER BY BINARY Name1, Name2";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $QueryMan не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			$arrayTask['From'][] = $row['Name1'];
			$arrayTask['To'][] = $row['Name2'];
			$arrayTask['Date'][] = strftime("%d.%m.%y",$row['DateExtradition']);
			$arrayTask['Comment'][] = str_replace("&nbsp;","",$row['Comment']);
		}
	}
	return $arrayTask;
}
//***********************************************************************************************
//getArrayFromMark($idProject)- функция возвращет массив данных из табл markbvp
// Входные параметры: $idProject - id проекта
// Выходные параметры: массив данных
//***********************************************************************************************

function getArrayFromMark($idProject)
{
	global $link;
	$Query = " SELECT markbvp.id, markbvp.id_Project, markbvp.id_Mark, markbvp.NumberMark,
	markbvp.NumberChange, markbvp.DateExtraditionBVP, markbvp.DateCustomer, markbvp.Comment  ".
	" FROM markbvp, project, mark ".
	" WHERE ".
	" (markbvp.id_Mark = mark.id) AND ".
	" (markbvp.id_Project = project.id) AND ".
	" (markbvp.id_Project =".escapeshellarg($idProject).")".
	" ORDER BY mark.Name_Mark, markbvp.DateExtraditionBVP";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $QueryMan не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			$arrayMark['Mark'][] = getMark($row['id_Mark']);
			$arrayMark['id'][] = $row['id_Mark'];
			if ($row['NumberMark'] == 0)
			{
				$arrayMark['NumberMark'][] = '';
			}
			else {
				$arrayMark['NumberMark'][] = $row['NumberMark'];
			}
			if ($row['NumberChange'] == 0)
			{
				$arrayMark['NumberChange'][] = '';
			}
			else {
				$arrayMark['NumberChange'][] = $row['NumberChange'];
			}
			if ($row['DateExtraditionBVP'] == 0)
			{
				$arrayMark['DateBVP'][] = 0;
			}
			else {
				$arrayMark['DateBVP'][] = strftime("%d.%m.%y",$row['DateExtraditionBVP']);
			}
			if ($row['DateCustomer'] == 0)
			{
				$arrayMark['DateCustomer'][] = 0;
			}
			else {
				$arrayMark['DateCustomer'][] = strftime("%d.%m.%y",$row['DateCustomer']);
			}
			$arrayMark['Comment'][] = $row['Comment'];
		}
	}
	return $arrayMark;
}
//***********************************************************************************************
// ViewTableTender()- функция отображения таблицы заявок
// Входные параметры:
//***********************************************************************************************

function ViewTableTender()
{
	global $link;
	//обрезаем пробелы
	$_REQUEST['Number_Tender'] = trim($_POST['Number_Tender']);
	$_REQUEST['Name_Tender'] = trim($_POST['Name_Tender']);
	$_REQUEST['Customer'] = trim($_POST['Customer']);
	$_REQUEST['Number_UTR'] = trim($_POST['Number_UTR']);
	if (strlen($_REQUEST['Number_UTR']) == 0)
	{
		$_REQUEST['Number_UTR'] = "&nbsp;";
	}
	if (isset($_REQUEST['rbselect']))
	{
		if ( !is_numeric($_REQUEST['Customer']) AND
		$_REQUEST['Customer'] != "")
		{
			$_REQUEST['Customer'] = escapeshellarg(getIdCustomer($_REQUEST['Customer']));
		}
		switch ($_REQUEST['rbselect'])
		{
			case "select":
			//выборка всей базы
			break;
			case "insert":
			$Query = "INSERT INTO tender_geod SET Number_Tender=".escapeshellarg($_REQUEST['Number_Tender']).
			", Name_Tender=".escapeshellarg($_REQUEST['Name_Tender']).
			", Number_UTR=".escapeshellarg($_REQUEST['Number_UTR']).
			", id_Customer=".escapeshellarg($_REQUEST['Customer']);
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				//print "запрос $Query не выполнен\n".mysql_error();
				processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			break;
			case "update":
			$Comma_separated = implode(",", $_REQUEST['project']);
			$Query = "UPDATE tender_geod SET ".
			"  Number_Tender=".escapeshellarg($_REQUEST['Number_Tender']).
			", Name_Tender=".escapeshellarg($_REQUEST['Name_Tender']).
			", Number_UTR=".escapeshellarg($_REQUEST['Number_UTR']).
			", id_Customer=".escapeshellarg($_REQUEST['Customer']).
			"  WHERE id IN ($Comma_separated)";
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				//print "запрос $Query не выполнен\n".mysql_error();
				processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			break;
			case "delete":
			$Comma_separated = implode(",", $_REQUEST['project']);
			$Query = "DELETE FROM tender_geod WHERE id IN ($Comma_separated)";
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				//print "запрос $Query не выполнен\n".mysql_error();
				processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			break;
			case "customer":
			$Comma_separated = implode(",", $_REQUEST['project']);
			$Query = "UPDATE tender_geod SET  id_Customer=".escapeshellarg($_REQUEST['Customer']).
			"  WHERE id IN ($Comma_separated)";
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				//print "запрос $Query не выполнен\n".mysql_error();
				processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			break;
		}
	}
	//======================= Модуль постраничного вывода информации на экран ========================
	SheetProject();

	//======================= Конец модуля постраничного вывода информации на экран ========================
	//проверяем, если нажата кнопка Найти- ищем
	if (isset($_REQUEST['findTender']))
	{
		$find = "  AND Number_Tender = ".escapeshellarg($_REQUEST['Number_Tender']).")";
	}
	else {
		$find = " AND Number_Tender RLIKE \"{$_SESSION['pageProject']}\")";
	}
	$Query = "SELECT tender_geod.Id, tender_geod.Number_Tender, tender_geod.Name_Tender, tender_geod.Number_UTR,
	Customer.Name_Customer
	FROM tender_geod, customer
	WHERE (tender_geod.id_Customer = Customer.Id ".
	$find.
	" ORDER BY Number_Tender";   //выборка из таблицы
	//file_put_contents("query.txt",$Query);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		print "<H4>Всего ".mysql_num_rows($dbResult)." позиций </H4>";
		//таблица HTML
		//print "<div class=\"layer\">\n";
		print "<table class=\"f-table-zebra\">\n";
		print "<caption>Таблица 'заказы'</caption>\n";
		print "<THEAD>\n";
		print "<TR>\n";
		print "<TH>Метка</TH>".
		"<TH>Номер заявки</TH>".
		"<TH>Название заявки</TH>".
		"<TH>Номер УТР</TH>".
		"<TH>Название заказчика</TH>";
		print "</THEAD>";
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			print "<TR align=left>\n";
			//если выбрано копирование, то выделяем красным
			if ( $_REQUEST['rbselect'] == "copy" AND
			FALSE !== array_search($row['Id'],$_REQUEST['project'],TRUE))
			{
				print "<TR align=left BGCOLOR=#FF0000>\n";
			}
			print "<TD><input name=\"project[]\" type=\"checkbox\" value=\"{$row['Id']}\">";
			printZeroTD($row['Number_Tender']);
			printZeroTD($row['Name_Tender']);
			printZeroTD($row['Number_UTR']);
			if ( $row['Name_Customer'] == "без названия" )
			{
				printZeroTD();
			}
			else {
				printZeroTD($row['Name_Customer']);
			}
		}
		print "</table>\n";
		//print "</div>";
	}
}

//***********************************************************************************************
// ViewTableYear()- функция отображения таблицы лет
// Входные параметры:
//***********************************************************************************************
function ViewTableYear()
{
	global $link;
	$_REQUEST['Year'] = trim($_POST['Year']);
		switch ($_REQUEST['rbselect'])
		{
			case "select":
			//выборка всей базы
			break;
			
			case "insert":
			$Query = "INSERT INTO year SET Year=".escapeshellarg($_REQUEST['Year']);
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				//print "запрос $Query не выполнен\n".mysql_error();
				processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			break;
			
			case "update":
			$Comma_separated = implode(",", $_REQUEST['id_year']);
			$Query = "UPDATE year SET ".
			"  Year=".escapeshellarg($_REQUEST['Year']).
			"  WHERE id IN ($Comma_separated)";
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				//print "запрос $Query не выполнен\n".mysql_error();
				processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			break;
			
			case "delete":
			$Comma_separated = implode(",", $_REQUEST['id_year']);
			$Query = "DELETE FROM year WHERE id IN ($Comma_separated)";
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				//print "запрос $Query не выполнен\n".mysql_error();
				processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			break;
		}	
		
		$Query = "SELECT * FROM year ORDER BY year ASC";
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				//print "Выборка не удалась!\n".mysql_error();
				processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			else {
				?>
				<!-- таблица лет-->
				<table class="g-3 f-table-zebra">
					<caption>список лет</caption>
					<thead>
						<tr>
							<th class="g-1">метка</th>
							<th class="g-2">год</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH))
						{
							print "<tr>";
							//если выбрано копирование, то выделяем красным
							if ( $_REQUEST['rbselect'] == "copy" AND
							FALSE !== array_search($row['id'],$_REQUEST['id_year'],TRUE))
							{
								print "<tr style='background-color:#FF7F50'>\n";
							}
							print "<td><input name=\"id_year[]\" type=\"checkbox\" value=\"{$row['id']}\">";
							printZeroTD($row['Year']);							
							print "</tr>";
						}
						?>
					</tbody>
				</table>
				<?php
			}
		
}

//*************************************************************************************************************
// ViewTableTabelTime()- функция отображения таблицы табельного времени
// Входные параметры:
//*************************************************************************************************************
function ViewTableTabelTime()
{
	global $link;
	
	switch($_GET['rbCategry'])
	{
		case 'add':
		$arrYear = fillTabelTime($_GET['year']);
		foreach($arrYear as $month)
		{
			foreach($month as $id=>$insertQuery)
			{
				if ( !($dbResult = mysql_query($insertQuery, $link)) )
				{
					//print "запрос $Query не выполнен\n".mysql_error();
					processingError("$insertQuery ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
				}
				
			}
		}
		break;

		case 'del':
		$deleteQuery = "DELETE FROM tabel_time WHERE id_Year = ".getIdYear($_GET['year']);
		if ( !($dbResult = mysql_query($deleteQuery, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$insertQuery ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}		
		break;
			
		case 'upd';
			
		break;
	}



	//если нажата кнопка Изменить
	if (!empty($_REQUEST['changeTime']))
	{
		$Comma_separated = implode(",", $_REQUEST['id_time']);
		$Query = "UPDATE tabel_time SET  Hour=".escapeshellarg($_REQUEST['Time']).
		" WHERE id IN ($Comma_separated)";

		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}

	}	
	
//======================= Модуль постраничного вывода информации на экран ========================
	SheetOutput();
//======================= Конец модуля постраничного вывода информации на экран ========================
$QueryTime = getIntervalDate($_SESSION['pageMonth'], $_SESSION['pageYear'],"tabel_time", "TimeStamp", "1-31");

	$selectQuery =   " SELECT tabel_time.id, Day, Month, Year, Hour ". 
					 " FROM tabel_time, year, month, day ".
					 " WHERE ".
					 " (tabel_time.id_Year=year.id) AND ".
					 " (tabel_time.id_Month=month.id) AND ".
					 " (tabel_time.id_Day=day.id) ". $QueryTime.
					 " order by tabel_time.id";
			
			
			if ( !($dbResult = mysql_query($selectQuery, $link)) )
			{
				//print "Выборка не удалась!\n".mysql_error();
				processingError("$selectQuery ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			else { ?>
			<table class="f-table-zebra g-5">
				<caption>Таблица табельного времени</caption>
				<thead>
					<tr>
						<th class="g-1">id</th>
						<th class="g-1">День</th>
						<th class="g-1">Месяц</th>
						<th class="g-1">Год</th>
						<th class="g-1">Часы</th>
					</tr>
				</thead>
				<tbody>				
			<?php
				while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH))
				{
				print "<tr>";
				//если выбрано копирование, то выделяем красным
				if ( $_REQUEST['rbselect'] == "copy" AND
					FALSE !== array_search($row['id'],$_REQUEST['id_time'],TRUE))
					{
						print "<tr style='background-color:#FF7F50'>\n";
					}
				print "<td><input name=\"id_time[]\" type=\"checkbox\" value=\"{$row['id']}\"></td>";
				printZeroTD($row['Day']);
				printZeroTD($row['Month']);
				printZeroTD($row['Year']);
				printZeroTD($row['Hour']);
				print "</tr>";
				}

			}
	?>
	</tbody>
	</table>
	
	
	<?php

}


//*************************************************************************************************************
// fillTabelTime($year)- генерация insert в табл. tabel_time значениями по введенному году
// Входные параметры:$year - год
//*************************************************************************************************************
function fillTabelTime($year=null)
{
if($year)
{
	for($i = 1; $i < 13; $i++){
		$arr_day[$i] = cal_days_in_month(CAL_GREGORIAN, $i, $year); 
		for($j = 1; $j < $arr_day[$i]+1; $j++){
			$query[$i][$j]= "INSERT INTO tabel_time SET id_Year=".getIdYear($year).
				", id_Month=".$i.
				", id_Day=".$j.
				", Hour=8.2".
				", TimeStamp=".mktime(0,0,0,$i, $j, $year);			
		}

	}
	return $query;	
}	
else
{
	return false;
}	
}

//*************************************************************************************************************
// checkEmptyTime()- обработка ошибки ввода нечисловых данных в поле time
// Входные параметры:
//*************************************************************************************************************
function checkEmptyTime()
{
	global $errors;
	if ( empty($_REQUEST['Time']) )
	{
		$_REQUEST['Time'] = "0";
	} 
	//заменяем все запятые в полях ввода точками
	$_REQUEST['Time'] = str_replace(",", ".", $_REQUEST['Time']);

	//проверка на тождественность данных числам, а не строкам
	if ( !is_numeric($_REQUEST['Time']) )
	{
		$errors[] = "<H3>Время имеет нечисловое значение</H3>";
	}
	
}

//*************************************************************************************************************
// ViewTableCustomer()- функция отображения таблицы заказчиков
// Входные параметры:
//*************************************************************************************************************

function ViewTableCustomer()
{
	global $link;
	switch ($_REQUEST['rbselect'])
	{
		case "select":
		//выборка всей базы
		break;
		case "insert":
		$Query = "INSERT INTO customer SET Number_Customer=".escapeshellarg($_REQUEST['Number_Customer']).
		", Name_Customer=".escapeshellarg($_REQUEST['Name_Customer']);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "update":
		$Comma_separated = implode(",", $_REQUEST['customer']);
		$Query = "UPDATE customer SET  Number_Customer=".escapeshellarg($_REQUEST['Number_Customer']).
		", Name_Customer=".escapeshellarg($_REQUEST['Name_Customer']).
		" WHERE id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "delete":
		$Comma_separated = implode(",", $_REQUEST['customer']);
		$Query = "DELETE FROM customer WHERE id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
	}
	$Query = "SELECT * from customer ORDER BY BINARY Name_Customer";   //выборка из таблицы
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else  {
		print "<H4>Всего ".mysql_num_rows($dbResult)." позиций </H4>";
		//таблица HTML
		//print "<div class=\"layer\">\n";
		print "<TABLE  align=left Border=\"1\" CellSpacing=\"0\" CellPadding=\"2\">\n";
		print "<caption><H4>Таблица 'Заказчиков'</H4><caption>\n";
		print "<THEAD align=center bgcolor = #E8E8E8 valign=top>\n";
		print "<TR>\n";
		print "<TH>Метка</TH>".
		"<TH>Шифр <br />Заказчика</TH>".
		"<TH>Название Заказчика</TH>";
		print "</THEAD>";
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			//если выбрано копирование, то выделяем красным
			if ( $_REQUEST['rbselect'] == "copy" AND
			FALSE !== array_search($row['Id'],$_REQUEST['customer'],TRUE))
			{
				print "<TR align=left BGCOLOR=#FF0000>\n";
			}
			else {
				print "<TR align=left>\n";
			}
			print "<TD><input name=\"customer[]\" type=\"checkbox\" value=\"{$row['Id']}\">".
			"<TD>{$row['Number_Customer']}".
			"<TD>{$row['Name_Customer']}";
		}
		print "</TABLE>\n";
		//print "</div>";
	}
}
//*************************************************************************************************************
// ViewTableMark()- функция отображения таблицы марок
// Входные параметры:
//*************************************************************************************************************

function ViewTableMark()
{
	global $link;
	switch ($_REQUEST['rbselect'])
	{
		case "select":
		//выборка всей базы
		break;
		case "insert":
		$Query = "INSERT INTO mark SET Name_Mark=".escapeshellarg($_REQUEST['Name_Mark']).
		", Comment_Mark=".escapeshellarg($_REQUEST['Comment_Mark']);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "update":
		$Comma_separated = implode(",", $_REQUEST['mark']);
		$Query = "UPDATE mark SET  Name_Mark=".escapeshellarg($_REQUEST['Name_Mark']).
		", Comment_Mark=".escapeshellarg($_REQUEST['Comment_Mark']).
		"  WHERE id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "delete":
		$Comma_separated = implode(",", $_REQUEST['mark']);
		$Query = "DELETE FROM mark WHERE id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
	}
	$Query = "SELECT * from Mark ORDER BY BINARY Name_Mark";   //выборка из таблицы
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		print "<H4>Всего ".mysql_num_rows($dbResult)." позиций </H4>";
		//таблица HTML
		//print "<div class=\"layer\">\n";
		print "<TABLE  align= left Border=\"1\" CellSpacing=\"0\" CellPadding=\"2\">\n";
		print " <Caption><H4>Таблица 'марки'</H4></Caption>\n";
		print "<THEAD align=center bgcolor = #E8E8E8 valign=top>\n";
		print "<TR>\n";
		print "<TH>Метка</TH>".
		"<TH>id</TH>".
		"<TH>Марка</TH>".
		"<TH>Название марки</TH>";
		print "</THEAD>";
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			//если выбрано копирование, то выделяем красным
			if ( $_REQUEST['rbselect'] == "copy" AND
			FALSE !== array_search($row['Id'],$_REQUEST['mark'],TRUE))
			{
				print "<TR align=left BGCOLOR=#FF0000>\n";
			}
			else {
				print "<TR align=left>\n";
			}
			print "<TD><input name=\"mark[]\" type=\"checkbox\" value=\"{$row['Id']}\">".
			"<TD>{$row['Id']}".
			"<TD>{$row['Name_Mark']}".
			"<TD>{$row['Comment_Mark']}";
		}
		print "</TABLE>\n";
		//print "</div>";
	}
}
//*************************************************************************************************************
// ViewTableManHour()- функция отображения таблицы трудозатрат
// Входные параметры:
//*************************************************************************************************************

function ViewTableManHour()
{
	global $link;
	switch ($_REQUEST['rbselect'])
	{
		case "select":
		//выборка всей базы
		break;
		case "insert":
		checkMatchManHour();
		$Query = "INSERT INTO man_hour SET id_Mark=".escapeshellarg($_REQUEST['Name_Mark']).
		", id_Work=".escapeshellarg($_REQUEST['Name_Work']).
		", Man_Hour=".escapeshellarg($_REQUEST['Man_Hour']);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "update":
		$Comma_separated = implode(",", $_REQUEST['manhour']);
		$Query = "UPDATE man_hour SET id_Mark=".escapeshellarg($_REQUEST['Name_Mark']).
		", id_Work=".escapeshellarg($_REQUEST['Name_Work']).
		", Man_Hour=".escapeshellarg($_REQUEST['Man_Hour']).
		"  WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "delete":
		$Comma_separated = implode(",", $_REQUEST['manhour']);
		$Query = "DELETE FROM man_hour WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print("запрос $Query не выполнен\n");
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
	}
	//выборка из таблицы
	$Query = "SELECT man_hour.Id, man_hour.Man_Hour, mark.Name_Mark, work.Name_Work, work.Type_Work ".
	"FROM man_hour, mark, work ".
	"WHERE ".
	"((mark.id = man_hour.id_Mark) AND ".
	"(work.id = man_hour.id_Work)) ".
	"ORDER BY mark.Name_Mark, work.Type_Work";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		print "<H4>Всего ".mysql_num_rows($dbResult)." позиций </H4>";
		//таблица HTML
		//print "<div class=\"layer\">\n";
		print "<TABLE  Width=\"100%\" Border=\"1\" CellSpacing=\"0\" CellPadding=\"2\">\n";
		print "<caption><H4>Таблица 'Трудозатраты'</H4></caption>\n";
		print "<THEAD align=center bgcolor = #E8E8E8 valign=top>\n";
		print "<TR>\n";
		print "<TH>Метка</TH>".
		"<TH>id</TH>".
		"<TH>Марка</TH>".
		"<TH>Наименование работы</TH>".
		"<TH>Вид работ</TH>".
		"<TH>Трудозатраты</TH>";
		print "</TR>\n";
		print "</THEAD>";
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			//если выбрано копирование, то выделяем красным
			if ( $_REQUEST['rbselect'] == "copy" AND
			FALSE !== array_search($row['Id'],$_REQUEST['manhour'],TRUE))
			{
				print "<TR align=left BGCOLOR=#FF0000>\n";
			}
			else {
				print "<TR align=left>\n";
			}
			print("<TD><input name=\"manhour[]\" type=\"checkbox\" value=\"{$row['Id']}\">".
            "<TD>{$row['Id']}</TD>".
			"<TD>{$row['Name_Mark']}</TD>".
			"<TD>{$row['Name_Work']}</TD>".
			"<TD>{$row['Type_Work']}</TD>".
			"<TD>{$row['Man_Hour']}</TD>");
		}
		print "</TABLE>\n";
		//print "</div>";
	}
}
//******************************************************************************
//ViewTablePlanTime() - отображение таблицы плановых выработок отделов по месяцам
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function ViewTablePlanTime()
{
	global $link;
	switch ($_REQUEST['rbselect'])
	{
		case "select":
		//выборка всей базы
		break;
		case "insert":
		$Query = "INSERT INTO Plan_Time SET ".
		" id_Month = ".escapeshellarg($_REQUEST['lstMonth']).
		", id_Year = ".escapeshellarg(getIdYear($_REQUEST['lstYear'])).
		", id_Department = ".escapeshellarg($_REQUEST['Department']).
		", Plan = ".escapeshellarg($_REQUEST['PlanMonthTime']).   //плановое время общее
		", PlanOneMan = ".escapeshellarg($_REQUEST['PlanTime']).  //пересчитанное время на 1 чел
		", PlanDepartment = ".escapeshellarg($_REQUEST['PlanDepartment']); //время на подразделение
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "update":
		$Comma_separated = implode(",", $_REQUEST['design']);
		$Query = "UPDATE Plan_Time SET ".
		" id_Month = ".escapeshellarg($_REQUEST['lstMonth']).
		", id_Year = ".escapeshellarg(getIdYear($_REQUEST['lstYear'])).
		", id_Department = ".escapeshellarg($_REQUEST['Department']).
		", Plan = ".escapeshellarg($_REQUEST['PlanMonthTime']).   //плановое время общее
		", PlanOneMan = ".escapeshellarg($_REQUEST['PlanTime']).  //пересчитанное время на 1 чел
		", PlanDepartment = ".escapeshellarg($_REQUEST['PlanDepartment']). //время на подразделение
		"  WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "delete":
		$Comma_separated = implode(",", $_REQUEST['design']);
		$Query = "DELETE FROM Plan_Time WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
	}
	//======================= Модуль постраничного вывода информации на экран ========================
	SheetOutput();
	//======================= Конец модуля постраничного вывода информации на экран ========================
	$QueryTime = getIntervalDate($_SESSION['pageMonth'], $_SESSION['pageYear'],"design", "statusBVP");
	$QueryMHDep = "SELECT department.Name_Department, design.statusBVP, SUM(design.Man_Hour_Sum) AS Man_Hour_Sum ".
	"FROM design,employee,department,project ".
	"WHERE ".
	"(design.id_Employee = employee.Id) AND ".
	"(department.Id = employee.id_Department) AND ".
	"(design.id_Project = project.Id) AND ".
	//"(department.id <> 22) AND (department.id <> 24) AND (department.id <> 25) AND ". //кроме Руководства, ИТ, ТЭОиСДР
	"(Man_Hour_Sum>".escapeshellarg('0').") ". //сумма работ должна быть больше нуля
	$QueryTime.
	" GROUP BY department.id ";
	//file_put_contents("query.txt",$QueryMHDep);
	if ( !($dbResult = mysql_query($QueryMHDep, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$QueryMHDep ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			//записываем в массив данные по каждому месяцу- округлим до 2 знака после запятой
			$array_timeReport[$row['Name_Department']] = round($row['Man_Hour_Sum'],0);
		}
	}
	$queryGeod = "SELECT SUM(Time) AS sum FROM geod WHERE 1=1 ".
	getIntervalDate(
	$_SESSION['pageMonth'],
	$_SESSION['pageYear'],
	"geod",
	"Date");
	if ( !($dbResult = mysql_query($queryGeod, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$QueryMHDep ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		$row = mysql_fetch_array($dbResult, MYSQL_BOTH);
		$array_timeReport['ГД'] = round($row['sum'], 0);
	}
	$Query1 = "SELECT Plan_Time.Id, Month.Month, year.Year, Department.Name_Department, ".
	"Plan_Time.Plan, Plan_Time.PlanOneMan, Plan_Time.PlanDepartment ".
	" FROM Plan_Time, Month, Department, Year ".
	" WHERE ".
	" (Plan_Time.id_Month = Month.id) ".
	" AND (Plan_Time.id_Year = Year.id) ".
	" AND (Plan_Time.id_Department = Department.Id)";
	$Query2 = " AND Month.id = ".escapeshellarg($_SESSION['pageMonth']);
	$Query3 = " AND Year.Year = ".escapeshellarg($_SESSION['pageYear']);
	$Query4 = " ORDER BY year.Year,Month.Id DESC";



	//соединяем все запросы в один
	$Query = $Query1.$Query2.$Query3.$Query4;
	//file_put_contents("query.txt",$Query);
	print "<Table Border=\"1\" CellSpacing=\"0\" CellPadding=\"2\">\n";
	print "<caption><H4>Плановая выработка для отделов</H4></caption>\n";
	print "<TR>\n";
	print "<TH>\n";
	print "метка\n";
	print "<TH>\n";
	print "Год\n";
	print "<TH>\n";
	print "Месяц\n";
	print "<TH>\n";
	print "Отдел\n";
	print "<TH>\n";
	print "Плановый фонд <br />времени (час в мес.)\n";
	print "<TH>\n";
	print "План на 1 <br />исполнителя\n";
	print "<TH>\n";
	print "Расчет плановой выработки <br /> отдела пропорц.факт.числ.\n";
	print "<TH>\n";
	print "Фактическая <br />выработка отделов.\n";
	print "<TH>\n";
	print "Коэф-т выработки\n";
	print "</Tr>\n";
	if ( !($dbResult = mysql_query($Query,$link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		$SumPlanDepartment = 0;
		$SumFact = 0;
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			print "<TR align=center>\n";
			print "<TD><input name=\"design[]\" type=\"checkbox\" value=\"{$row['Id']}\"></TD>\n";
			printZeroTD($row['Year']);
			printZeroTD($row['Month']);
			printZeroTD($row['Name_Department']);
			printZeroTD($row['Plan']);
			printZeroTD($row['PlanOneMan']);
			printZeroTD($row['PlanDepartment']);
			printZeroTD($array_timeReport[$row['Name_Department']]);
			printZeroTD(round($array_timeReport[$row['Name_Department']]/$row['PlanDepartment'],2));
			$SumPlanDepartment =  $SumPlanDepartment + $row['PlanDepartment'];
			$SumFact = $SumFact + $array_timeReport[$row['Name_Department']];
			print "</Tr>\n";
		}
		print("<th colspan=6 align=right><b>Итого</b></th>");
		printZeroTH($SumPlanDepartment);
		printZeroTH($SumFact);
		printZeroTH(round($SumFact/$SumPlanDepartment, 2));
		print "</Table>\n";
	}
}

//******************************************************************************
//ViewTableTimeArchive() - отображение таблицы редактирования трудозатрат БВП, Архива,ГД
//Входные параметры:
//Выходные параметры:
//******************************************************************************
function ViewTableTimeArchive()
{
	global $link;
	switch ($_REQUEST['rbselect'])
	{
		case "select":
		//выборка всей базы
		break;
		case "insert":
		$Query = "INSERT INTO Plan_Time SET ".
		" id_Month = ".escapeshellarg($_REQUEST['lstMonth']).
		", id_Year = ".escapeshellarg(getIdYear($_REQUEST['lstYear'])).
		", id_Department = ".escapeshellarg($_REQUEST['Department']).
		", Plan = ".escapeshellarg($_REQUEST['PlanMonthTime']).   //плановое время общее
		", PlanOneMan = ".escapeshellarg($_REQUEST['PlanTime']).  //пересчитанное время на 1 чел
		", PlanDepartment = ".escapeshellarg($_REQUEST['PlanDepartment']); //время на подразделение
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "update":
		$Comma_separated = implode(",", $_REQUEST['design']);
		$Query = "UPDATE Plan_Time SET ".
		" id_Month = ".escapeshellarg($_REQUEST['lstMonth']).
		", id_Year = ".escapeshellarg(getIdYear($_REQUEST['lstYear'])).
		", id_Department = ".escapeshellarg($_REQUEST['Department']).
		", Plan = ".escapeshellarg($_REQUEST['PlanMonthTime']).   //плановое время общее
		", PlanOneMan = ".escapeshellarg($_REQUEST['PlanTime']).  //пересчитанное время на 1 чел
		", PlanDepartment = ".escapeshellarg($_REQUEST['PlanDepartment']). //время на подразделение
		"  WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
		case "delete":
		$Comma_separated = implode(",", $_REQUEST['design']);
		$Query = "DELETE FROM Plan_Time WHERE Id IN ($Comma_separated)";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "запрос $Query не выполнен\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		break;
	}
}



/*
***************************************************************************************
*************************************<< Функции получения данных >>********************
***************************************************************************************
*/
//*************************************************************************************
// getArrayProject()- функция заполнения списка номерами проектов
// Входные параметры:
// Выходные параметры:
//*************************************************************************************

function getArrayProject()
{
	global $link;
	$Query = "SELECT id, Number_Project  FROM project ORDER BY Number_Project";
	if ( !( $dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			print "<option value=\"{$row['id']}\">{$row['Number_Project']}</option>\n";
		}
	}
}
//*************************************************************************************
// getArrayTender()- функция заполнения списка номерами заявок
// Входные параметры:
// Выходные параметры:
//*************************************************************************************

function getArrayTender()
{
	global $link;
	$Query = "SELECT id, Number_Tender  FROM  tender_geod ORDER BY Number_Tender";
	if ( !( $dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			print "<option value=\"{$row['id']}\">{$row['Number_Tender']}</option>\n";
		}
	}
}
//*****************************************************************************************
// checkFullNumber()- функция проверки внесения проекта только с номером без б/н
// Входные параметры:
// Выходные параметры:
//*****************************************************************************************

function checkFullNumber()
{
	global $link,$errors;
	if( isset($_REQUEST['Number_Project']) AND intval(trim($_REQUEST['Number_Project'],'"')) == 114 )
	{
		$errors[] = "<H3>Ошибка! Введение проекта без номера невозможно! Для регистрации проекта обратитесь в ПЭБ!</H3>";
	}
}
//**************************************************************************************************
// getArrayWorkName()- функция заполнения списка именами работ
// Входные параметры:
// Выходные параметры:
//**************************************************************************************************

function getArrayWorkName($tmpNameMark)
{
	global $link;
	//если происходит копирование, то заполняем корректно список работ
	if ($_REQUEST['rbselect']=="copy")
	{
		$Query = "SELECT work.id, mark.Name_Mark, work.Name_Work, work.Type_Work ".
		"FROM man_hour, mark, work ".
		"WHERE ".
		"((mark.id = man_hour.id_Mark) AND ".
		"(work.id = man_hour.id_Work) AND ".
		"(mark.id =".escapeshellarg($tmpNameMark).")) ".
		"ORDER BY work.Type_Work";
	}
	else {
		$Query = "SELECT id, Name_Work, Type_Work FROM work ORDER BY Type_Work";
	}
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else  {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			print "<option value=\"{$row['id']}\">{$row['Name_Work']} - {$row['Type_Work']}</option>\n";
		}
	}
}
//*************************************************************************************************************
// getArrayNameCustomer()- функция заполнения списка именами заказчиков
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function getArrayNameCustomer()
{
	global $link;
	$Query = "SELECT id, Number_Customer, Name_Customer FROM customer ORDER BY Number_Customer";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else  {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			print "<option value=\"{$row['id']}\">{$row['Number_Customer']} - {$row['Name_Customer']}</option>\n";
		}
	}
}
//*************************************************************************************************************
// getArrayNameMark()- функция заполнения списка именами марок
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function getArrayNameMark()
{
	global $link;
	$Query = "SELECT id,Name_Mark,Comment_Mark FROM mark ORDER BY BINARY Name_Mark";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			print "<option value=\"{$row['id']}\">{$row['Name_Mark']} - {$row['Comment_Mark']}</option>\n";
		}
	}
}
//*************************************************************************************************************
// getArrayNameDepartment()- функция заполнения списка названиями отделов  для отчетов
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function getArrayNameDepartment($flag = 'notplan')
{
	global $link;
    if($flag == 'plan')
    {
        $Query = "(SELECT id, Name_Department FROM department WHERE Primary_Department='TRUE') ".
        "UNION ".
        "(SELECT id, Name_Department FROM department WHERE Primary_Department='FALSE' AND id IN (27,28,29))";
    }  else
    {
    	$Query = "SELECT id, Name_Department FROM department WHERE Primary_Department='TRUE' ";
    }
    $Order = "ORDER BY Name_Department";
    $Query = $Query.$Order;

	if ( !($dbResult = mysql_query($Query,$link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ($row = mysql_fetch_array($dbResult,MYSQL_BOTH) )
		{
			if (($_SESSION['Name_Post'] == "Экономист") OR
			($_SESSION['Name_Post'] == "Начальник управления") OR
			($_SESSION['Name_Post'] == "Администратор") OR
			(($_SESSION['Name_Post'] == "Работник архива")))
			{
				print "<option value=\"{$row['id']}\">{$row['Name_Department']}</option>\n";
			}
			else {
				if (($_SESSION['Name_Post'] == "Начальник отдела" OR $_SESSION['Name_Post'] == "Зам.начальника отдела") AND
				($_SESSION['Name_Department'] == $row['Name_Department']) )
				{
					print "<option value=\"{$row['id']}\">{$row['Name_Department']}</option>\n";
				}
				else {
					continue;//завершает текущую итерацию
				}
			}
		}
	}
}
//*************************************************************************************************************
// getArrayDepartment()- функция формирования списка отделов
// Входные параметры:
// Выходные параметры: $arrayDepartment - список
//*************************************************************************************************************

function getArrayDepartment()
{
	global $link;
	$Query = "SELECT id, Name_Department FROM department WHERE Primary_Department='TRUE' OR id='24' ORDER BY id";//включая отдел ИТ
	if ( !($dbResult = mysql_query($Query,$link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ($row = mysql_fetch_array($dbResult,MYSQL_BOTH) )
		{
			$arrayDepartment[$row['id']] = $row['Name_Department'];
		}
	}
	return $arrayDepartment;
}
//*************************************************************************************************************
// InitializationMatrixCard()- функция заполнения массива значениями 0
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function InitializationMatrixCard()
{
	global $link;
	//кроме вспомогательных отделов и   ГД(26)
		$Query = "SELECT Name_Department FROM department WHERE Primary_Department=".escapeshellarg('TRUE')."  and id<>26 ORDER BY Name_Department";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			for ($i = 1; $i < 13; $i++)
			{
				$array_timeReport[$row['Name_Department']][$i] = 0;
			}
		}
	}
	return $array_timeReport;
}
//******************************************************************************
//InitializationMatrixCardProject($Query2)-инициализация 2 мерного массива номер проекта-отделы
//Входные параметры:  $Query2- период времени
// $listDepartment - список отделов
//Выходные параметры:
//******************************************************************************

function InitializationMatrixCardProject($Query2, $listDepartment)
{
	global $link;
	$Query1 = "SELECT project.Number_Project,project.Name_Project,customer.Number_Customer ".
	"FROM design,project,customer ".
	"WHERE ".
	"(project.id_Customer = Customer.Id) AND ".
	"(design.id_Project = project.Id) ";
	//объединение запроса в 1 запрос
	$Query3 = "	ORDER BY customer.Number_Customer, project.Number_Project";
	$Query = $Query1.$Query2." GROUP BY project.id ".$Query3;
	//file_put_contents("query.txt",$Query);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			foreach($listDepartment as $index=>$NameDepartment)
			{
				$arrayDepartment["{$row['Number_Customer']}-{$row['Number_Project']}&nbsp;{$row['Name_Project']}"][$NameDepartment] = 0;
			}
			/*	  	$arrayDepartment["{$row['Number_Customer']}-{$row['Number_Project']}"]['ТО'] = 0;
			$arrayDepartment["{$row['Number_Customer']}-{$row['Number_Project']}"]['ЭО'] = 0;
			$arrayDepartment["{$row['Number_Customer']}-{$row['Number_Project']}"]['КО'] = 0;
			$arrayDepartment["{$row['Number_Customer']}-{$row['Number_Project']}"]['КИПиА'] = 0;
			$arrayDepartment["{$row['Number_Customer']}-{$row['Number_Project']}"]['СТРО'] = 0;
			$arrayDepartment["{$row['Number_Customer']}-{$row['Number_Project']}"]['ВКиОВ'] = 0;
			$arrayDepartment["{$row['Number_Customer']}-{$row['Number_Project']}"]['ГП'] = 0;
			$arrayDepartment["{$row['Number_Customer']}-{$row['Number_Project']}"]['ПСО'] = 0;*/
		}
	}
	if (isset($arrayDepartment))
	{
		return $arrayDepartment;
	}
	else {
		return;
	}
}
//*************************************************************************************************************
// InitializationSumCard($i)- функция заполнения массива[13] значениями 0
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function InitializationSumCard($i)
{
	$arraySum = array_fill(1, $i, 0);
	return $arraySum;
}
//*************************************************************************************************************
// getArrayNameOffice()- функция заполнения списка названиеями бюро
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function getArrayNameOffice()
{
	global $link;
	$Query = "SELECT id, Name_Office FROM office ORDER BY Name_Office";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			print "<option value=\"{$row['id']}\">{$row['Name_Office']}</option>\n";
		}
	}
}
//******************************************************************************
//getArrayNameMeasure() - функция заполнения списка названиями видов геод.измерений
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function getArrayNameMeasure()
{
	global $link;
	$Query = "SELECT Name_Measure FROM measure ORDER BY BINARY Name_Measure";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			print "<option value=\"{$row['Name_Measure']}\">{$row['Name_Measure']}</option>\n";
		}
	}
}
//*************************************************************************************************************
// getArrayNamePost()- функция заполнения списка должностей
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function getArrayNamePost()
{
	global $link;
	$Query = "SELECT id, Name_Post FROM post ORDER BY Name_Post";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			print "<option value=\"{$row['id']}\">{$row['Name_Post']}</option>\n";
		}
	}
}
//*************************************************************************************************************
// getArrayNameEmployeeGIP()- функция заполнения списка ГИПОВ
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function getArrayNameEmployeeGIP()
{
	global $link;
	$Query = "SELECT Employee.id, Employee.Family, Employee.Name, Employee.Patronymic, ".
	"Department.Name_Department ".
	"FROM Department, Employee ".
	"WHERE ".
	"(Employee.Status = ".escapeshellarg('TRUE').") AND ".
	"(Department.id = Employee.id_Department) AND ".
	"(Department.Name_Department=".escapeshellarg('Руководство').") ".
	"ORDER BY BINARY Employee.Family";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH))
		{
			print "<option value=\"{$row['id']}\">{$row['Family']} {$row['Name']} {$row['Patronymic']}</option>\n";
		}
	}
}
//*************************************************************************************************************
// getArrayNameEmployee()- функция заполнения списка сотрудников
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function getArrayNameEmployee()
{
	global $link;
	switch ($_SESSION['Name_Post'])
	{
		case "Начальник управления":
		case "Экономист":
		case "Администратор":
		case "Работник архива":
		case "Главный инженер":
		$Query = "SELECT Employee.id, Employee.Family, Employee.Name, Employee.Patronymic, ".
		"Department.Name_Department, Office.Name_Office ".
		"FROM Employee, Department, Office  ".
		"WHERE ".
		"(Employee.id_Department = Department.id) AND ".
		"(Employee.Status = ".escapeshellarg('TRUE').") AND ".
		"(Employee.id_Office = Office.id) ".
		"ORDER BY  BINARY Employee.Family";
		break;
		case "Начальник отдела":
		case "Зам.начальника отдела":
		case "Старший специалист":
		$Query = "SELECT Employee.id, Employee.Family, Employee.Name, Employee.Patronymic, ".
		"Department.Name_Department, Office.Name_Office ".
		"FROM Department, Office, Employee ".
		"WHERE ".
		"(Office.id = Employee.id_Office) AND ".
		"(Employee.Status = ".escapeshellarg('TRUE').") AND ".
		"(Department.id = Employee.id_Department) AND ".
		"(Department.Name_Department=".escapeshellarg($_SESSION['Name_Department']).") ".
		"ORDER BY BINARY Employee.Family";
		break;
		case "Начальник бюро":
		$Query = "SELECT Employee.id, Employee.Family, Employee.Name, Employee.Patronymic, ".
		"Department.Name_Department, Office.Name_Office ".
		"FROM Department, Office, Employee ".
		"WHERE ".
		"(Office.id = Employee.id_Office) AND ".
		"(Employee.Status = ".escapeshellarg('TRUE').") AND ".
		"(Department.id = Employee.id_Department) AND ".
		"(Department.Name_Department=".escapeshellarg($_SESSION['Name_Department']).") AND ".
		"(Office.Name_Office=".escapeshellarg($_SESSION['Name_Office']).") ".
		"ORDER BY BINARY Employee.Family ";
		break;
		default:
		$Query = "SELECT Employee.id, Employee.Family, Employee.Name, Employee.Patronymic ".
		"FROM Employee ".
		"WHERE ".
		"(Employee.Family = ".escapeshellarg($_SESSION['Family']).") AND ".
		"(Employee.Name = ".escapeshellarg($_SESSION['Name']).") AND ".
		"(Employee.Status = ".escapeshellarg('TRUE').") AND ".
		"(Employee.Patronymic = ".escapeshellarg($_SESSION['Patronymic']).")";
	}

	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH))
		{
			print "<option value=\"{$row['id']}\">{$row['Family']} {$row['Name']} {$row['Patronymic']}</option>\n";
		}
	}
}
//******************************************************************************
//getArrayNameEmployeeSAPR()-получение списка сотрудников бюро САПР
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function getArrayNameEmployeeSAPR()
{
	global $link;
	$Query = "SELECT Employee.id, Employee.Family, Employee.Name, Employee.Patronymic, ".
	"Office.Name_Office ".
	"FROM Office, Employee ".
	"WHERE ".
	"(Office.id = Employee.id_Office) AND ".
	"(Employee.Status = ".escapeshellarg('TRUE').") AND ".
	"(Office.Name_Office=\"бюро САПР\") ".
	"ORDER BY BINARY Employee.Family";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH))
		{
			print "<option value=\"{$row['id']}\">{$row['Family']} {$row['Name']} {$row['Patronymic']}</option>\n";
		}
	}
}
//*************************************************************************************
// getNameProject($id)- функция получения номера проекта по его коду
// Входные параметры:
// $id - код проекта
// Выходные параметры:
//************************************************************************************

function getNameProject($id)
{
	global $link;
	if ( isset($id) AND
	$id != "" )
	{
		$Query = "SELECT Number_Project FROM project WHERE id=".escapeshellarg($id)." LIMIT 1";
		//print $Query;
		//exit;
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else 	{
			while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
			{
				return $row['Number_Project'];
			}
		}
	}
}
//*************************************************************************************
// getNameTenderGeod($id)- функция получения номера проекта по его коду
// Входные параметры:
// $id - код проекта
// Выходные параметры:
//************************************************************************************

function getNameTenderGeod($id)
{
	global $link;
	if ( isset($id) AND
	$id != "" )
	{
		$Query = "SELECT Number_Tender FROM tender_geod WHERE id=$id";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else 	{
			while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
			{
				return $row['Number_Tender'];
			}
		}
	}
}
//****************************************************************************************
// getNameCustomer($id)- функция получения название заказчика  по его коду
// Входные параметры:
// $id - id заказчика
// Выходные параметры:
//****************************************************************************************

function getNameCustomer($id)
{
	global $link;
	if (isset($id) AND
	intval($id) != 0)
	{
		$Query = "SELECT id, Number_Customer, Name_Customer FROM customer WHERE id=".escapeshellarg(intval($id));
		//file_put_contents('errors.txt',$Query);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка $Query не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else  {
			while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
			{
				$x = "{$row['Number_Customer']} - {$row['Name_Customer']}";
			}
			return $x;
		}
		} else return "&nbsp;";
}
//*************************************************************************************************************
// getPlanMonth($index)- функция получения значения плана за месяц по ПУ
// Входные параметры:
// $index - индекс месяца
// Выходные параметры:
//*************************************************************************************************************

function getPlanMonth($index)
{
	global $link;
	//Получаем данные по плану за месяц по ПУ .
	$QueryPlanTime =  " SELECT SUM(PlanDepartment) as PlanDepartment ".
	" FROM plan_time, month,year ".
	" WHERE ".
	" (plan_time.id_Month = month.id) ".
	" AND (plan_time.id_Year = year.id) ".
	" AND (id_Month = ".escapeshellarg($index).")".
	" AND (id_Year = (SELECT id FROM year WHERE Year=".escapeshellarg($_SESSION['lstYear']).")) ".
	" GROUP BY month.id";
	if ( !($dbResult = mysql_query($QueryPlanTime,$link)) )
	{
		//print "Выборка $QueryPlanTime не удалась!\n".mysql_error();
		processingError("$QueryPlanTime ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			$Plan_All =  $row['PlanDepartment'];
		}
	}
	return $Plan_All;
}
//*************************************************************************************************************
// getManHourSumBVP($i)- функция получения значения утвержденных трудозатрат по ПУ за месяц
// Входные параметры:
// $i - индекс месяца
// Выходные параметры:
//*************************************************************************************************************

function getManHourSumBVP ($QueryDate)
{
	global $link;
	if ( $_SESSION['Department'] == "Выбор отдела" )
	{
		$QueryDepartment = "";
	}
	else
	{
		$QueryDepartment = " AND (department.id = ".escapeshellarg($_SESSION['Department']).") ";
	}
	$Query1 = "SELECT SUM(design.Man_Hour_Sum) AS Man_Hour_Sum  ".
	"FROM design,employee,department,project ".
	"WHERE ".
	"(design.id_Employee = employee.Id) AND ".
	"(department.Id = employee.id_Department) AND ".
	"(design.id_Project = project.Id) AND ".
	"(Man_Hour_Sum>".escapeshellarg('0').") ". //сумма работ должна быть больше нуля
	$QueryDepartment;
	/*	$Query3 = "(design.statusBVP>=".escapeshellarg($_SESSION['arrayMonth'][$i]).
	") AND (design.statusBVP<".escapeshellarg($_SESSION['arrayMonth'][$i+1]).")) ".
	" GROUP BY department.Name_Department ";*/
	$Query2 = " GROUP BY department.Name_Department WITH ROLLUP";
	//результирующий запрос
	$Query = $Query1.$QueryDate.$Query2;
	//file_put_contents("query.txt",$Query);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		@mysql_data_seek($dbResult,mysql_num_rows($dbResult)-1);
		$row = mysql_fetch_array($dbResult, MYSQL_BOTH);
		return $row['Man_Hour_Sum'];
	}
}


/**
* getDataByMark($id_work, $year, $period) - получение данных за год по конкретной работе по утвержденным проектам
* @param undefined $id_work - работа
* @param undefined $year - год
* @param undefined $period - календарный или отчетный
* 
*/
function getDataByMark($id_work, $year, $period)
{
	global $link;
	
	$time_interval = getIntervalDate('Выберите месяц', $year,'design','statusBVP',$period);
	$query = 
	"
	SELECT 
	SUM(design.Sheet_A1) AS A1,
	SUM(design.Sheet_A3) AS A3,
	SUM(design.Sheet_A4) AS A4,
	SUM(design.Time) AS Time,
	SUM(design.Man_Hour_Sum) AS Man_Hour_Sum
	FROM design
	INNER JOIN `work` ON design.id_Work=`work`.Id
	INNER JOIN mark ON design.id_Mark=mark.Id
	INNER JOIN man_hour ON `work`.Id=man_hour.id_Work AND mark.Id=man_hour.id_Mark
	WHERE
	design.id_Work=".escapeshellarg($id_work).$time_interval.
	" GROUP BY design.id_Work";
	
	if ( !($dbResult = mysql_query($query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		$row = mysql_fetch_array($dbResult, MYSQL_BOTH);
		return array("A1"=>$row['A1'], "A3"=>$row['A3'], "A4"=>$row['A4'], 'Time'=>$row['Time'], 'Man_Hour_Sum'=>$row['Man_Hour_Sum']);		
	}	
	
}

//*************************************************************************************************************
// getTabelTime()- функция получения фактического табельного времени
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function getTabelTime()
{
	global $link;
	if ( $_SESSION['Department'] == "Выбор отдела" )
	{
		$QueryDepartment = "";
	}
	else
	{
		$QueryDepartment = " AND (id_Department = ".escapeshellarg($_SESSION['Department']).") ";
	}
	//выбираем значения месяца и года
	if ( !($_SESSION['lstMonth'] == "Выберите месяц" OR
	$_SESSION['lstYear'] == "Выберите год") )
	{
		$Date = " (id_Year = ".escapeshellarg(getIdYear($_SESSION['lstYear'])).") ".
		" AND (id_Month = ".escapeshellarg($_SESSION['lstMonth'])." )";
	}
	//выбираем значения года
	elseif ( $_SESSION['lstMonth'] == "Выберите месяц" AND
	$_SESSION['lstYear'] != "Выберите год" )
	{
		$Date = " (id_Year = ".escapeshellarg(getIdYear($_SESSION['lstYear'])).") ";
	}
	//выбираем значения месяца
	elseif ( $_SESSION['lstMonth'] != "Выберите месяц" )
	{
		$Date = " (id_Year = ".escapeshellarg(getIdYear($_SESSION['lstYear'])).") ".
		" AND (id_Month = ".escapeshellarg($_SESSION['lstMonth'])." )";
	}
	else {   //неопределенная дата
		$Date = "";
	}
	$Query = "SELECT SUM(Tabel_Time) AS Tabel ".
	" FROM Plan_Time ".
	" WHERE ".
	$Date.
	$QueryDepartment;
	$Group = "GROUP BY id_Month";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		$row = mysql_fetch_array($dbResult, MYSQL_BOTH);
		return $row['Tabel'];
	}
}
//*************************************************************************************************************
// getFullNameProject($id)- функция получения названия проекта по его коду
// Входные параметры:
// $id - код проекта
// Выходные параметры:
//*************************************************************************************************************

function getFullNameProject($id)
{
	global $link;
	if ( isset($id) AND
	$id != "")
	{
		$Query = "SELECT Name_Project FROM project WHERE id=$id";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else	{
			while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
			{
				return $row['Name_Project'];
			}
		}
	}
}
//*********************************************************************************
// getIdProject($NumberProject)- функция получения кода проекта по его номеру
// Входные параметры:
// $NumberProject - номер проекта
// Выходные параметры:
//*********************************************************************************

function getIdProject($NumberProject)
{
	global $link;
	$Query = "SELECT id FROM project WHERE Number_Project=".escapeshellarg($NumberProject);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка в getIdProject не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			return $row['id'];
		}
	}
}
//*********************************************************************************
// getIdTender($NumberProject)- функция получения кода проекта по его номеру
// Входные параметры:
// $NumberProject - номер проекта
// Выходные параметры:
//*********************************************************************************

function getIdTender($NumberProject)
{
	global $link;
	$Query = "SELECT id FROM tender_geod WHERE Number_Tender=".escapeshellarg($NumberProject);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка в getIdProject не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			return $row['id'];
		}
	}
}
//*********************************************************************************
// getIdWork($NameWork)- функция получения названия работ по его коду
// Входные параметры:
// $NameWork - наименование работы
// Выходные параметры:
//*********************************************************************************

function getIdWork($NameWork)
{
	global $link;
	$strWork = explode("-", trim($NameWork));
	$Query = "SELECT id FROM work WHERE (Name_Work=".escapeshellarg(trim($strWork[0])).")".
	" AND (Type_Work=".escapeshellarg(trim($strWork[1])).")";
	//file_put_contents("query.txt",$Query);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка в getIdWork не удалась!<br />\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			return $row['id'];
		}
	}
}
//******************************************************************************
//getIdYear($NameYear) - функция получения id года
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function getIdYear($NameYear)
{
	global $link;
	$Query = "SELECT id FROM Year WHERE Year=".escapeshellarg($NameYear);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка в getIdYear не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			return $row['id'];
		}
	}
}
//*************************************************************************************************************
// getIdMark($NameMark)- функция получения названия марки по его коду
// Входные параметры:
// $NameMark - имя марки
// Выходные параметры:
//*************************************************************************************************************

function getIdMark($NameMark)
{
	global $link;
	$strMark = explode("-", trim($NameMark));
	$Query = "SELECT id FROM mark WHERE (Name_Mark=".escapeshellarg(trim($strMark[0])).")".
	" AND (Comment_Mark=".escapeshellarg(trim($strMark[1])).")";
	//file_put_contents("query.txt",$Query);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка в getIdMark не удалась!<br />\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH))
		{
			return $row['id'];
		}
	}
}
//*************************************************************************************************************
// getIdCustomer($NameCustomer)- функция получения названия заказчика по его коду
// Входные параметры:
// $NameMark - имя марки
// Выходные параметры:
//*************************************************************************************************************

function getIdCustomer($NameCustomer)
{
	global $link;
	$strCustomer = explode("-", trim($NameCustomer), 2);
	$Query = "SELECT id FROM customer WHERE (Number_Customer=".escapeshellarg(trim($strCustomer[0])).")";

	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка в getIdCustomer не удалась!<br />\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			return $row['id'];
		}
	}
}
//******************************************************************************
//getIdEmployee($NameEmployee) - функция получения ФИО по его коду
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function getIdEmployee($NameEmployee)
{
	global $link;
	$strEmployee = explode(" ", trim($NameEmployee));
	$Query = "SELECT id FROM employee WHERE (Family = ".escapeshellarg(trim($strEmployee[0])).")".
	" AND (Name = ".escapeshellarg(trim($strEmployee[1])).")".
	" AND (Patronymic = ".escapeshellarg(trim($strEmployee[2])).")";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка в getIdEmployee не удалась!<br />\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			return $row['id'];
		}
	}
}
//*************************************************************************************************************
// getIdDepartment($NameDepartment)- функция получения кода отдела по его названию
// Входные параметры:
// $NameDepartment - имя отдела
// Выходные параметры:
//*************************************************************************************************************

function getIdDepartment($NameDepartment)
{
	global $link;
	$Query = "SELECT id FROM Department WHERE Name_Department=".escapeshellarg($NameDepartment);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка в getIdDepartment не удалась!<br />\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			return $row['id'];
		}
	}
}
//*************************************************************************************************************
// getIdOffice($NameOffice)- функция получения кода бюро по его названию
// Входные параметры:
// $NameOffice - имя бюро
// Выходные параметры:
//*************************************************************************************************************

function getIdOffice($NameOffice)
{
	global $link;
	$Query = "SELECT id FROM Office WHERE Name_Office=".escapeshellarg($NameOffice);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка в getIdOffice не удалась!<br />\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			return $row['id'];
		}
	}
}
//*************************************************************************************************************
// getIdPost($NamePost)- функция получения кода должности по его названию
// Входные параметры:
// $NamePost - имя должности
// Выходные параметры:
//*************************************************************************************************************

function getIdPost($NamePost)
{
	global $link;
	$Query = "SELECT id FROM Post WHERE Name_Post=".escapeshellarg($NamePost);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка в getIdPost не удалась!<br />\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			return $row['id'];
		}
	}
}
//*************************************************************************************************************
// getDataMonthBVP($index,$QueryCustomer) - функция получения данных по месяцу- тредозатраты по выдаче в БВП
// Входные параметры:
// $index - номер месяца
// $QueryCustomer - строка заказчика
// Выходные параметры:
//*************************************************************************************************************

function getDataMonthBVP($index,$QueryCustomer)
{
	global $link;
	$Query1 =   "SELECT design.statusBVP, SUM(design.Man_Hour_Sum) AS Man_Hour_Sum ".
	"FROM design, project ".
	"WHERE ".
	"(design.id_Project = project.id) AND ";
	$Query2 = "(design.statusBVP>=".escapeshellarg($_SESSION['arrayMonth'][$index]).
	") AND (design.statusBVP<".escapeshellarg($_SESSION['arrayMonth'][$index+1]).") ".
	"  GROUP BY  statusBVP WITH ROLLUP";
	//результирующий запрос
	$Query = $Query1.$QueryCustomer.$Query2;
	//print($Query);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		/*		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			$answer = $row['Man_Hour_Sum'];
		}
		*/
		@mysql_data_seek($dbResult,mysql_num_rows($dbResult)-1);
		$answer = mysql_fetch_row($dbResult);
	}
	return $answer[1];
}
//*************************************************************************************************************
// getDataMonth($index,$QueryCustomer) - функция получения данных по месяцу-время, трудозатраты общие
// Входные параметры:
// $index - номер месяца
// $QueryCustomer - строка заказчика
// Выходные параметры:
//*************************************************************************************************************

function getDataMonth($index,$QueryCustomer)
{
	global $link;
	$Query1 =   "SELECT SUM(design.Time) AS Time, SUM(design.Man_Hour_Sum) AS Man_Hour_Sum ".
	"FROM design, project ".
	"WHERE ".
	"(design.id_Project = project.Id) AND ";
	$Query2 = "(design.Date>=".escapeshellarg($_SESSION['arrayMonth'][$index]).
	") AND (design.Date<".escapeshellarg($_SESSION['arrayMonth'][$index+1]).") ".
	"  GROUP BY  design.Date WITH ROLLUP";
	//результирующий запрос
	$Query = $Query1.$QueryCustomer.$Query2;
	//print($Query);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		/*		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			$answer = $row['Man_Hour_Sum'];
		}
		*/
		@mysql_data_seek($dbResult,mysql_num_rows($dbResult)-1);
		$answer = mysql_fetch_row($dbResult);
	}
	return $answer;
}
//*************************************************************************************************************
// display_errors()- функция вывода ошибок,сообщений,предупреждений
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function display_errors()
{
	global $infos,$successes,$warnings,$validations,$errors;
	if (!empty($infos))
	{
		foreach($infos as $inf)
		{
			print "<div class=\"info\">";
			print $inf;
			print "</div>";
		}
	}
	if (!empty($successes))
	{
		foreach($successes as $succ)
		{
			print "<div class=\"success\">";
			print $succ;
			print "</div>";
		}
	}
	if (!empty($warnings))
	{
		foreach($warnings as $warn)
		{
			print "<div class=\"warning\">";
			print $warn;
			print "</div>";
		}
	}
	if (!empty($validations))
	{
		foreach($validations as $val)
		{
			print "<div class=\"validation\">";
			print $val;
			print "</div>";
		}
	}
	if (!empty($errors))
	{
		foreach($errors as $err)
		{
			print "<div class=\"error\">";
			print $err;
			print "</div>";
		}
	}
}
//*************************************************************************************************************
// getNameExtProject($id)- функция получения полного названия проекта по его коду
// Входные параметры:
// $id - код проекта
// Выходные параметры:
//*************************************************************************************************************

function getNameExtProject($id)
{
	global $link;
	if ( isset($id) AND
	$id != "")
	{
		$Query = "SELECT Name_Project FROM project WHERE id=$id";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//	print "Выборка не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else 	{
			while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH))
			{
				return $row['Name_Project'];
			}
		}
	}
}
//*************************************************************************************************************
// getNameWork($id)- функция получения названия работ по его коду
// Входные параметры:
// $id - код работ
// Выходные параметры:
//*************************************************************************************************************

function getNameWork($id)
{
	global $link;
	if (isset($id) AND
	$id != "")
	{
		$Query = "SELECT Name_Work,Type_Work FROM work WHERE id=$id LIMIT 1";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
			{
				$x = "{$row['Name_Work']}-{$row['Type_Work']}";
			}
		}
	}
	return $x;
}
//*************************************************************************************************************
// getNameMark($id)- функция получения названия марки по его коду
// Входные параметры:
// $id - код марки
// Выходные параметры:
//*************************************************************************************************************

function getNameMark($id)
{
	global $link;
	if (isset($id) AND
	$id != "")
	{
		$Query = "SELECT Name_Mark, Comment_Mark FROM mark WHERE id=$id LIMIT 1";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка в getNameMark не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else 	{
			while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH))
			{
				$x = "{$row['Name_Mark']} - {$row['Comment_Mark']}";
			}
		}
	}
	return $x;
}
//*************************************************************************************************************
// getMark($id)- функция получения названия марки без- пояснений по его коду
// Входные параметры:
// $id - код марки
// Выходные параметры:
//*************************************************************************************************************

function getMark($id, $name="Name_Mark")
{
	global $link;
	if (isset($id) AND $id != "")
	{
		if ($name == "Name_Mark")
		{
			$Query = "SELECT Name_Mark FROM mark WHERE id=$id LIMIT 1";
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				//print "Выборка в getNameMark не удалась!\n".mysql_error();
				processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			else 	{
				while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH))
				{
					$x = $row['Name_Mark'];
				}
			}
		}
		//если есть параметр $name - то меняем запрос
		if ($name == "Comment_Mark")
		{
			$Query = "SELECT Comment_Mark FROM mark WHERE id=$id LIMIT 1";
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				//print "Выборка в getNameMark не удалась!\n".mysql_error();
				processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			else 	{
				while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH))
				{
					$x = $row['Comment_Mark'];
				}
			}
		}
		return $x;
	}
}
//*************************************************************************************************************
// getNameEmployee($id)- функция получения имени сотрудника по его коду
// Входные параметры:
// $id - код сотрудника
// Выходные параметры:
//*************************************************************************************************************

function getNameEmployee($id)
{
	global $link;
	if ( isset($id) )
	{
		$Query = "SELECT Family,Name,Patronymic FROM employee WHERE id=$id LIMIT 1";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else 	{
			while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
			{
				$x = "{$row['Family']} {$row['Name']} {$row['Patronymic']}";
			}
		}
	}
	return $x;
}
//*************************************************************************************************************
// getNameEmployee_short($id)- функция получения имени сотрудника по его коду(сокр.запись)
// Входные параметры:
// $id - код сотрудника
// Выходные параметры:
//*************************************************************************************************************

function getNameEmployee_short($id)
{
	global $link;
	if ( isset($id) )
	{
		$Query = "SELECT Family,Name,Patronymic FROM employee WHERE id=$id LIMIT 1";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else 	{
			while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
			{
				$x = $row['Family']." ".mb_substr($row['Name'],0,1,'utf8').".". mb_substr($row['Patronymic'],0,1,'utf8').".";
			}
		}
	}
	return $x;
}
//*************************************************************************************************************
// getNameDepartment($id)- функция получения названия департамента по его коду
// Входные параметры:
// $id - код отдела
// Выходные параметры:
//*************************************************************************************************************

function getNameDepartment($id)
{
	global $link;
	if (isset($id) AND
	$id != "")
	{
		$Query = "SELECT Name_Department FROM department WHERE id=$id";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			while ( $row = mysql_fetch_array($dbResult,MYSQL_BOTH) )
			{
				return $row['Name_Department'];
			}
		}
	}
}
//*************************************************************************************************************
// getNameOffice($id)- функция получения названия бюро по его коду
// Входные параметры:
// $id - код бюро
// Выходные параметры:
//*************************************************************************************************************

function getNameOffice($id)
{
	global $link;
	if (isset($id) AND
	$id != "")
	{
		$Query = "SELECT Name_Office FROM office WHERE id=$id";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
			{
				return $row['Name_Office'];
			}
		}
	}
}
//*************************************************************************************************************
// getNamePost($id)- функция получения названия должности по его коду
// Входные параметры:
// $id - код должности
// Выходные параметры:
//*************************************************************************************************************

function getNamePost($id)
{
	global $link;
	if ( isset($id) AND
	$id != "")
	{
		$Query = "SELECT Name_Post FROM post WHERE id=$id";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else	{
			while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
			{
				return $row['Name_Post'];
			}
		}
	}
}
//*************************************************************************************************************
// checkEmpty()- функция проверки введенных данных на пустоту
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function checkEmpty()
{
	global $link,$errors;
	//если все поля пустые, то нет смысла их вводить в базу
	if (empty($_REQUEST['Sheet_A']) AND
	empty($_REQUEST['Time']) AND
	empty($_REQUEST['prov']) AND
	empty($_REQUEST['Time_Collection']) AND
	empty($_REQUEST['Time_Agreement']))
	{
		$errors[] = "<H3>Укажите объемы работ</H3>";
	}
	//проверяем по отдельности каждый параметр, если он пустой, то инициализируем
	if (empty($_REQUEST['Сf_Man_Hour']))
	{
		$_REQUEST['Сf_Man_Hour'] = "1";
		$_REQUEST['Сf_Man_Hour1'] = "1";
		$_REQUEST['Сf_Man_Hour3'] = "1";
		$_REQUEST['Сf_Man_Hour4'] = "1";
	}
	if (empty($_REQUEST['Sheet_A']))
	{
		$_REQUEST['Sheet_A'] = "0";
	}
	if (empty($_REQUEST['Sheet_A1']))
	{
		$_REQUEST['Sheet_A1'] = "0";
	}
	if (empty($_REQUEST['Sheet_A3']))
	{
		$_REQUEST['Sheet_A3'] = "0";
	}
	if (empty($_REQUEST['Sheet_A4']))
	{
		$_REQUEST['Sheet_A4'] = "0";
	}
	if (empty($_REQUEST['Time']))
	{
		$_REQUEST['Time'] = "0";
	}
	if (empty($_REQUEST['Time1']))
	{
		$_REQUEST['Time1'] = "0";
	}
	if (empty($_REQUEST['Time3']))
	{
		$_REQUEST['Time3'] = "0";
	}
	if (empty($_REQUEST['Time4']))
	{
		$_REQUEST['Time4'] = "0";
	}
	if (empty($_REQUEST['Time_Collection']))
	{
		$_REQUEST['Time_Collection'] = "0";
	}
	if (empty($_REQUEST['Time_Agreement']))
	{
		$_REQUEST['Time_Agreement'] = "0";
	}
	if (empty($_REQUEST['num_cher']))
	{
		$_REQUEST['num_cher'] = "&nbsp;";
	}
	if (empty($_REQUEST['prov']))
	{
		$_REQUEST['prov'] = "0";
	}
	//записываем флаг состояния сдачи работы в бюро выпуска проектов
	if (empty($_REQUEST['checkBVP']))
	{
		$_REQUEST['checkBVP'] = "FALSE";
	}
	//заменяем все запятые в полях ввода точками
	$_REQUEST['Sheet_A'] = str_replace(",", ".", $_REQUEST['Sheet_A']);
	$_REQUEST['Time'] = str_replace(",", ".", $_REQUEST['Time']);
	$_REQUEST['Сf_Man_Hour'] = str_replace(",", ".", $_REQUEST['Сf_Man_Hour']);
	$_REQUEST['Time_Collection'] = str_replace(",", ".", $_REQUEST['Time_Collection']);
	$_REQUEST['Time_Agreement'] = str_replace(",", ".", $_REQUEST['Time_Agreement']);
	$_REQUEST['prov'] = str_replace(",", ".", $_REQUEST['prov']);
	$_REQUEST['Сf_Sheet'] = str_replace(",", ".", $_REQUEST['Сf_Sheet']);
	//проверка на тождественность данных числам, а не строкам
	if ( !is_numeric($_REQUEST['Sheet_A']) )
	{
		$errors[] = "<H3>Листаж имеет нечисловое значение</H3>";
	}
	if ( !is_numeric($_REQUEST['Time']) )
	{
		$errors[] = "<H3>Фактическое время на выполнение работы имеет нечисловое значение</H3>";
	}
	if ( !is_numeric($_REQUEST['Сf_Man_Hour']) )
	{
		$errors[] = "<H3>Коэффициент имеет нечисловое значение</H3>";
	}
	if ( !is_numeric($_REQUEST['Time_Collection']) )
	{
		$errors[] = "<H3>Время на сбор исходных данных имеет нечисловое значение</H3>";
	}
	if ( !is_numeric($_REQUEST['Time_Agreement']) )
	{
		$errors[] = "<H3>Время на согласование данных имеет нечисловое значение</H3>";
	}
	if ( !is_numeric($_REQUEST['prov']) )
	{
		$errors[] = "<H3>Проверочное имя имеет нечисловое значение</H3>";
	}
	//коэффициент не может быть по модулю больше 1
	if ($_REQUEST['Сf_Man_Hour'] < 0 OR
	$_REQUEST['Сf_Man_Hour'] > 1.15)
	{
		$errors[] = "<H3>Коэффициент должен иметь значение 0< X <=1.15</H3>";
	}
	//коэффициент заполнения листа не может быть по модулю больше 1
	if ($_REQUEST['Сf_Sheet'] <= 0 OR
	$_REQUEST['Сf_Sheet'] > 1)
	{
		$errors[] = "<H3>Коэффициент заполнения листа должен иметь значение 0< X <=1</H3>";
	}
	//проверка, если выбор из списков не числовой и не пустой, то заносим их код
	if ( !is_numeric($_REQUEST['Number_Project']) AND
	$_REQUEST['Number_Project'] != "")
	{
		$_REQUEST['Number_Project'] = escapeshellarg(getIdProject($_REQUEST['Number_Project']));
	}
	if ( !is_numeric($_REQUEST['Name_Mark']) AND
	$_REQUEST['Name_Mark'] != "")
	{
		$_REQUEST['Name_Mark'] = escapeshellarg(getIdMark($_REQUEST['Name_Mark']));
	}
	if ( !is_numeric($_REQUEST['Name_Work']) AND
	$_REQUEST['Name_Work'] != "")
	{
		$_REQUEST['Name_Work'] = escapeshellarg(getIdWork($_REQUEST['Name_Work']));
	}
	//если хотя бы 1 из выпадающих списков пустой, то ошибка
	if(empty($_REQUEST['Number_Project']) OR
	empty($_REQUEST['Name_Work']) OR
	empty($_REQUEST['Name_Mark']))
	{
		$errors[] = "<H3>Должны быть выбраны элементы из списков</H3>";
	}
	//выборка значения трудозатрат из таблицы Man_Hour
	$Query = " SELECT man_hour.Id, man_hour.Man_Hour, mark.Name_Mark, work.Name_Work, work.Comment ".
	" FROM man_hour,work,mark ".
	" WHERE ".
	"((man_hour.id_Work=work.Id) AND ".
	"(man_hour.id_Mark=mark.Id) AND ".
	"(mark.Id=".escapeshellarg($_REQUEST['Name_Mark']).") AND ".
	"(work.Id=".escapeshellarg($_REQUEST['Name_Work']).")) ".
	" LIMIT 1";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		//результирующий массив пустой, то значит марка и вид работы не соответствуют друг другу
		if ( mysql_num_rows($dbResult) == 0 )
		{
			$errors[] = "<H3>Марка не соответствует типу работы</H3>";
		}
		//обнуляем трудозатраты
		$_REQUEST['Man_Hour1'] = "0";
		$_REQUEST['Man_Hour3'] = "0";
		$_REQUEST['Man_Hour4'] = "0";
		//в зависимости от формата документа в переменные заносятся листы, время, трудозатраты, коэффициент
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			if ($row['Comment'] == "формат А1")
			{
				$_REQUEST['Sheet_A1'] = $_REQUEST['Sheet_A'];
				$_REQUEST['Time1'] = $_REQUEST['Time'];
				$_REQUEST['Сf_Man_Hour1'] = $_REQUEST['Сf_Man_Hour'];
				$_REQUEST['Man_Hour1'] = $row['Man_Hour'];
			}
			elseif ($row['Comment'] == "формат А3")
			{
				$_REQUEST['Sheet_A3'] = $_REQUEST['Sheet_A'];
				$_REQUEST['Time3'] = $_REQUEST['Time'];
				$_REQUEST['Сf_Man_Hour3'] = $_REQUEST['Сf_Man_Hour'];
				$_REQUEST['Man_Hour3'] = $row['Man_Hour'];
			}
			elseif ($row['Comment'] == "формат А4")
			{
				$_REQUEST['Sheet_A4'] = $_REQUEST['Sheet_A'];
				$_REQUEST['Time4'] = $_REQUEST['Time'];
				$_REQUEST['Сf_Man_Hour4'] = $_REQUEST['Сf_Man_Hour'];
				$_REQUEST['Man_Hour4'] = $row['Man_Hour'];
			}
			//
			else {
				$_REQUEST['Time4'] = $_REQUEST['Time'];
			}
		}
	}
}
//******************************************************************************
//checkEmptyGeod()-проверка правильности заполнения входных данных в модуле геодезия
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function checkEmptyGeod()
{
	global $errors;
	//если все поля пустые, то нет смысла их вводить в базу
	if (empty($_REQUEST['Name_Work']) AND
	empty($_REQUEST['Comment']) AND
	empty($_REQUEST['Number']) AND
	empty($_REQUEST['Price']))
	{
		$errors[] = "<H3>Укажите объемы работ</H3>";
	}
	if (empty($_REQUEST['lstProject']))
	{
		$errors[] = "<H3>Укажите номер проекта</H3>";
	}
	if (empty($_REQUEST['lstTender']))
	{
		$errors[] = "<H3>Укажите номер заявки</H3>";
	}
	if (empty($_REQUEST['Category']))
	{
		$errors[] = "<H3>Укажите категорию сложности</H3>";
	}
	if (empty($_REQUEST['Unit_Measurement']))
	{
		$errors[] = "<H3>Укажите единицу измерения</H3>";
	}
	if (empty($_REQUEST['Time']))
	{
		$_REQUEST['Time'] = "0";
	}
	if (empty($_REQUEST['Name_Work']))
	{
		$_REQUEST['Name_Work'] = "0";
	}
	if (empty($_REQUEST['Comment']))
	{
		$_REQUEST['Comment'] = "0";
	}
	if (empty($_REQUEST['Number']))
	{
		$_REQUEST['Number'] = "1";
	}
	if (empty($_REQUEST['Price']))
	{
		$_REQUEST['Price'] = "0";
	}
	//при пустых флагах коэффициенты равны 1
	if (empty($_REQUEST['k_light']))
	{
		$_REQUEST['k_light'] = 1;
	}
	if (empty($_REQUEST['k_vibration']))
	{
		$_REQUEST['k_vibration'] = 1;
	}
	if (empty($_REQUEST['k_season']))
	{
		$_REQUEST['k_season'] = 1;
	}
	if (empty($_REQUEST['k_bridge']))
	{
		$_REQUEST['k_bridge'] = 1;
	}
	if (empty($_REQUEST['k_regime']))
	{
		$_REQUEST['k_regime'] = 1;
	}
	if (empty($_REQUEST['k_regime_kam']))
	{
		$_REQUEST['k_regime_kam'] = 1;
	}
	if (empty($_REQUEST['k_plane']))
	{
		$_REQUEST['k_plane'] = 1;
	}
	if (empty($_REQUEST['k_profil']))
	{
		$_REQUEST['k_profil'] = 1;
	}
	if (empty($_REQUEST['k_it']))
	{
		$_REQUEST['k_it'] = 1;
	}
	if (empty($_REQUEST['k_kalka']))
	{
		$_REQUEST['k_kalka'] = 1;
	}
	if (empty($_REQUEST['k_smeta']))
	{
		$_REQUEST['k_smeta'] = 1;
	}
	if (empty($_REQUEST['k_index']))
	{
		$_REQUEST['k_index'] = 1;
	}
	if (empty($_REQUEST['k_field']))
	{
		$_REQUEST['k_field'] = 1;
	}
	if (empty($_REQUEST['k_correction']))
	{
		$_REQUEST['k_correction'] = 1;
	}
	if ($_POST['Category']=="")
	{
		$_REQUEST['Category']= 0;
	}
	//в случае ошибки ввода дробной части числа, легко заменяем разделитель на точку
	$symbol = array(",", ":", ";", "-", "|", "\\", "/", "~","?");
	$_REQUEST['Number'] = str_replace($symbol, ".", $_REQUEST['Number']);
	$_REQUEST['k_smeta'] = str_replace($symbol, ".", $_REQUEST['k_smeta']);
	$_REQUEST['k_index'] = str_replace($symbol, ".", $_REQUEST['k_index']);
	$_REQUEST['k_correction'] = str_replace($symbol, ".", $_REQUEST['k_correction']);
	$_REQUEST['Price'] = str_replace($symbol, ".", $_REQUEST['Price']);
	$_REQUEST['Time'] = str_replace(",", ".", $_REQUEST['Time']);
	if ( !is_numeric($_REQUEST['Time']) )
	{
		$errors[] = "<H3>Время {$_REQUEST['Time']} -  нечисловое значение</H3>";
	}
	//проверка на тождественность данных числам, а не строкам
	if ( !is_numeric($_REQUEST['Number']) )
	{
		$errors[] = "<H3>Количество {$_REQUEST['Number']} - нечисловое значение</H3>";
	}
	if ( !is_numeric($_REQUEST['Price']) )
	{
		$errors[] = "<H3>Цена {$_REQUEST['Price']} -  нечисловое значение</H3>";
	}
	if ( !is_numeric($_REQUEST['k_index']) )
	{
		$errors[] = "<H3>Коэффициент индексации {$_REQUEST['k_index']} -  нечисловое значение</H3>";
	}
	if ( !is_numeric($_REQUEST['k_smeta']) )
	{
		$errors[] = "<H3>Коэффициент к сметной стоимости {$_REQUEST['k_smeta']} -  нечисловое значение</H3>";
	}
	if ( !is_numeric($_REQUEST['k_correction']) )
	{
		$errors[] = "<H3>Корректировочный коэффициент {$_REQUEST['k_correction']} -  нечисловое значение</H3>";
	}
	//проверка, если выбор из списков не числовой и не пустой, то заносим их код
	if ( !is_numeric($_REQUEST['lstProject']) AND $_REQUEST['lstProject'] != "")
	{
		$_REQUEST['lstProject'] = escapeshellarg(getIdProject($_REQUEST['lstProject']));
	}
	//по каким то причинам $_REQUEST['lstTender'] при копировании не строка а число, поэтому отрицание убираем
	if ( !is_numeric($_REQUEST['lstTender']) AND $_REQUEST['lstTender'] != "")
	{
		$_REQUEST['lstTender'] = escapeshellarg(getIdTender($_REQUEST['lstTender']));
	}
}
//******************************************************************************
//checkEmptySapr() -  проверка правильности заполнения входных данных в модуле сапр
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function checkEmptySapr()
{
	global $errors;
	if (empty($_REQUEST['Time']))
	{
		$_REQUEST['Time'] = "0";
	}
	if (empty($_REQUEST['Comment']))
	{
		$_REQUEST['Comment'] = " ";
	}
	if (empty($_REQUEST['Name_Work']))
	{
		$errors[] = "<H3>Заполните поле 'Наименование работы' </H3>";
	}
	$_REQUEST['Time'] = str_replace(",", ".", $_REQUEST['Time']);
	if ( !is_numeric($_REQUEST['Time']) )
	{
		$errors[] = "<H3>Время {$_REQUEST['Time']} -  нечисловое значение</H3>";
	}
	if (empty($_REQUEST['lstEmployee']) OR
	empty($_REQUEST['lstPerformer']))
	{
		$errors[] = "<H3>Должны быть выбраны элементы из списков</H3>";
	}
	//проверка, если выбор из списков не числовой и не пустой, то заносим их код
	if ( !is_numeric($_REQUEST['lstEmployee']) AND
	$_REQUEST['lstEmployee'] != "")
	{
		$_REQUEST['lstEmployee'] = escapeshellarg(getIdEmployee($_REQUEST['lstEmployee']));
	}
	if ( !is_numeric($_REQUEST['lstPerformer']) AND
	$_REQUEST['lstPerformer'] != "")
	{
		$_REQUEST['lstPerformer'] = escapeshellarg(getIdEmployee($_REQUEST['lstPerformer']));
	}
}
//*************************************************************************************************************
// checkEmptyDate()- функция проверки  даты на пустоту
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function checkEmptyDate()
{
	if ( isset($_REQUEST['rbselect']) )
	{
		if ( empty($_REQUEST['Date']) )
		{
			//если поле даты пустое то заполняем его текущей датой
			$_REQUEST['Date'] = mktime();
		}
		else {
			$_REQUEST['Date'] = inputDate($_REQUEST['Date']);
		}
	}
	//AdminProject
	elseif ( isset($_REQUEST['btnSubmit']))
	{
		if ( empty($_REQUEST['DateOpen']) )
		{
			//если поле даты пустое то заполняем его текущей датой
			$_REQUEST['DateOpen'] = mktime();
		}
		else {
			$_REQUEST['DateOpen'] = inputDate($_REQUEST['DateOpen']);
		}
		if ( empty($_REQUEST['DateClose']) )
		{
			//если поле даты пустое то заполняем его текущей датой
			$_REQUEST['DateClose'] = mktime();
		}
		else {
			$_REQUEST['DateClose'] = inputDate($_REQUEST['DateClose']);
		}
		if ( empty($_REQUEST['DateOutput']) )
		{
			//если поле даты пустое то заполняем его текущей датой
			$_REQUEST['DateOutput'] = mktime();
		}
		else {
			$_REQUEST['DateOutput'] = inputDate($_REQUEST['DateOutput']);
		}
	}
	elseif ( isset($_REQUEST['rbstatusBVP']) )
	{
		if ( empty($_REQUEST['Date']) )
		{
			$CurrentDate  = getdate(); //определяем текущую дату
			$_REQUEST['Date'] = $CurrentDate['mday'].".".$CurrentDate['mon'].".".$CurrentDate['year'];
			//отправляем на контроль
			$_REQUEST['Date'] = inputDate($_REQUEST['Date']);

		}
		else {
			$_REQUEST['Date'] = inputDate($_REQUEST['Date']);
		}
		} elseif (isset($_REQUEST['rbstatusBVP_Archive']))
	{
		if ( empty($_REQUEST['DateArchive']) )
		{
			//если поле даты пустое то заполняем его текущей датой
			//$_REQUEST['DateArchive'] = mktime();     //ничего не делать , обрабатывается в запросе UPDATE
			}  else {
			$_REQUEST['DateArchive'] = inputDate($_REQUEST['DateArchive']);
		}
	}
}
//*************************************************************************************************************
// checkEmptyDateUniversal()- функция проверки  даты на пустоту
// Входные параметры:
//$btnOK - кнопка подтверждения
//$date - проверочное поле даты
// Выходные параметры:
//$date
//*************************************************************************************************************

function checkEmptyDateUniversal($btnOK, $date)
{
	if ( isset($btnOK) )
	{
		if ( empty($date) )
		{
			//если поле даты пустое то заполняем его текущей датой
			$date = mktime();
		}
		else {
			$date = inputDate($date);
		}
	}
	return $date;
}
//*************************************************************************************************************
// inputDate($InputDate)- функция проверки и перевода даты в число секунд с проверкой 
// Входные параметры: $InputDate-входная дата
// Выходные параметры:
//*************************************************************************************************************

function inputDate($InputDate)
{
	global $errors;
	//замена различных знаков на точку на точку
	$symbol = array(",", ":", ";", "-", "|", "\\", "/", "~","?");
	$str = str_replace($symbol, ".", $InputDate);
	//разбить строку на подстроки используя точку
	$Date = explode(".",$str);
	
	//проверить на правильность даты
	if (checkdate($Date[1], $Date[0], $Date[2]) )
	{
		$month = $Date[1];
		$day =  $Date[0];
		$year = $Date[2];
	}
	elseif(checkdate($Date[1], $Date[2], $Date[0]))
	{
		$month = $Date[1];
		$day =  $Date[2];
		$year = $Date[0];
	}
	else {
		$errors[] = "<H3>Введенная дата имеет неправильный формат</H3>";
		exit;
	}	

    //если год сокращенный, то приводим к единому формату 20ХХ
	if (strlen($year) == 2)
	{
     $year = $year + 2000;
	}
	//перевести в секунды и занести в базу данных
	//$Date[1]-месяц
	//$Date[0]-день
	//$Date[2]-год
	$currentDate = mktime();//текущая дата
	//утвердить можно текущим периодом ( до 26 числа предыдущего месяца )
		if ($_SESSION['LimitDate'] == TRUE)
	{
        //

		//по расп №54 от 01.08.2011
		//25 число также блокируется, день предназначен для экономистов
		if ( (($day <= 25 AND $month < $_SESSION['CurrentMonth'] AND $year == $_SESSION['CurrentYear']) OR
	 	($day <= 25 AND $year < $_SESSION['CurrentYear']) OR
		($day <= 25 AND $month == $_SESSION['CurrentMonth'] AND $_SESSION['CurrentDay'] > 24) ) AND
		(($_SESSION['Name_Post'] != "Администратор") AND
		($_SESSION['Name_Post'] != "Работник архива") AND
		($_SESSION['Name_Post'] != "Экономист")))
		{
			$errors[] = "<H3>Работа не может быть добавлена, утверждена, удалена!
			Срок сдачи отчетов вышел!</H3>";
			//возвращаем введенную дату в секундах
			$InputDate = mktime(2, 0, 0, $month, $day, $year);
		}
		else {
			//проверка на ввод будущих дат-нельзя добавлять будущие даты
			if (($currentDate < mktime(2, 0, 0, $month, $day, $year)) AND
			(($_SESSION['Name_Post'] != "Администратор") AND
			($_SESSION['Name_Post'] != "Работник архива") AND
			($_SESSION['Name_Post'] != "Экономист")))
			{
				$errors[]="<H3>Такая дата еще не наступила!</H3>";
			}
			else {
				//иначе
				$InputDate = mktime(2, 0, 0, $month, $day, $year);   //введеное время
			}
		}
		} else {
		$InputDate = mktime(2, 0, 0, $month, $day, $year);   //введеное время блокировки не учитываются
	}

	return $InputDate;
}
//*************************************************************************************************************
// notCheckInputDate($InputDate)- функция перевода даты в число секунд
// Входные параметры: $InputDate-входная дата
// Выходные параметры:
//*************************************************************************************************************

function notCheckInputDate($InputDate)
{

	global $errors;
	//замена различных знаков на точку на точку
	$symbol = array(",", ":", ";", "-", "|", "\\", "/", "~","?");
	$str = str_replace($symbol, ".", $InputDate);
	//разбить строку на подстроки используя точку
	$Date = explode(".",$str);


	//проверить на правильность даты
	if (checkdate($Date[1], $Date[0], $Date[2]) )
	{
		$month = $Date[1];
		$day =  $Date[0];
		$year = $Date[2];
	}
	elseif(checkdate($Date[1], $Date[2], $Date[0]))
	{
		$month = $Date[1];
		$day =  $Date[2];
		$year = $Date[0];
	}
	else {
		$errors[] = "<H3>Введенная дата имеет неправильный формат</H3>";
		exit;
	}
		$InputDate = mktime(2, 0, 0, $month, $day, $year);   //введеное время
		return $InputDate;		

}
//*******************************************************************************
// checkDateBE() - функция проверки корректности заполения даты (Начало и Конца)
	// Входные параметры:
// Выходные параметры:
//********************************************************************************

function checkDateBE()
{
	global $errors;
	switch ($_REQUEST['rbdate'])
	{
		case "date_all":
		//проверка на корректность введение даты-
		if ($_REQUEST['DateClose'] < $_REQUEST['DateOpen'])
		{
			$errors[] = "<H3>Дата окончания наступает ранее даты начала проекта</H3>";
		}
		break;
		default:
	}
}
//************************************************************************************************
// checkStatementBVP()- функция проверки, не является ли данная работа уже утвержденной в архиве или нач.отделов
// Входные параметры:
// Выходные параметры:
//************************************************************************************************

function checkStatementBVP()
{
	global $link,$errors;
	$Comma_separated = implode(",", $_REQUEST['design']);
	$Query = "SELECT statusBVP, approvalBVP, offPlan  FROM design WHERE Id IN ($Comma_separated)";
	if ( !($dbResult = mysql_query($Query,$link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			//если статус работы равно "Архивная работа", то выдаем ошибку.
			if ( $row['approvalBVP'] > 0)
			{
				$errors[]="<H3>Ошибка! Работа находится в Архиве!</H3>";
				break;
			}
			//никто кроме ответственных лиц, не имеет право утверждать работы или возвращать в разработку
			elseif ( ($row['statusBVP'] > 0 OR $row['offPlan'] > 0) AND
			$_SESSION['Name_Post'] != "Администратор" AND
			$_SESSION['Name_Post'] != "Начальник отдела" AND
			$_SESSION['Name_Post'] != "Зам.начальника отдела" AND
			$_SESSION['Sanction'] != "TRUE")
			{
				$errors[]="<H3>Ошибка! Работа уже является утвержденной!</H3>";
				break;
			}
		}
	}
}
//************************************************************************************************
// checkBVP()- переутвердить можно только работы уоторые были утверждены в текущем периоде
// Входные параметры:     остается тонкий момент- нач может утвердить работы прошлых месяцев, но которые еще не были утверждены(т.е. переходящие)
	// Выходные параметры:
//************************************************************************************************

function checkBVP()
{
	global $link, $errors;
	//найдем границу разрешенного интервала утверждения
    if ($_SESSION['CurrentDay'] < 25)
    {
    	//если не наступило 25 число
		$BeginDate = mktime(0, 0, 0, $_SESSION['CurrentMonth'] - 1, 26, $_SESSION['CurrentYear']);
    } else  {
    	//если наступило 25 число
		$BeginDate = mktime(0, 0, 0, $_SESSION['CurrentMonth'], 26, $_SESSION['CurrentYear']);
    }
	$Comma_separated = implode(",", $_REQUEST['design']);
	$Query = "SELECT statusBVP, approvalBVP, offPlan  FROM design WHERE Id IN ($Comma_separated)";
	if ( !($dbResult = mysql_query($Query,$link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
            //Подтвержденную ранее в прошедшем периоде, работу переутвердить нельзя
			if ( $row['statusBVP'] > 0 AND
			$row['statusBVP'] < $BeginDate AND
			$_SESSION['Name_Post'] != "Администратор" )
			{
				$errors[]="<H3>Ошибка! Вернуть в разработку или переутвердить можно только работу текущего периода!</H3>";
				break;
			}
            //Подтвержденную ранее в прошедшем периоде, работу переутвердить нельзя
			if ( $row['offPlan'] > 0 AND
			$row['offPlan'] < $BeginDate AND
			$_SESSION['Name_Post'] != "Администратор" )
			{
				$errors[]="<H3>Ошибка! Вернуть в разработку или переутвердить можно только работу текущего периода!</H3>";
				break;
			}
		}
	}
}
//*************************************************************************************************************
// checkStatementTime()- поиск в табл. Plan_Time на совпадение значений
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function checkStatementTime()
{
	global $link,$errors;
	$Query = "SELECT id_Month, id_Year, id_Department ".
	" FROM Plan_Time ".
	" WHERE ".
	" id_Month = ".escapeshellarg($_REQUEST['lstMonth']).
	" AND id_Year = (SELECT id FROM Year WHERE Year=".escapeshellarg($_REQUEST['lstYear']).")".
	" AND id_Department = ".escapeshellarg($_REQUEST['Department']);
	if ( !($dbResult = mysql_query($Query,$link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		if (mysql_num_rows($dbResult) > 0)
		{
			$errors[] = "<H3>Данные на указанный месяц уже есть в таблице</H3>";
		}
	}
}
//*************************************************************************************************************
// checkEmptyDepartment()- функция проверки  названия отдела на пустоту
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function checkEmptyDepartment()
{
	global $errors;
	if ( empty($_REQUEST['Name_Department']) )
	{
		$errors[] = "<H3>Введите название отдела</H3>\n";
	}
}
//*************************************************************************************************************
// checkEmptyEmployee()- функция проверки  параметров сотрудника на пустоту
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function checkEmptyEmployee()
{
	global $errors;
	if ( $_REQUEST['rbselect'] != "updateNotParol" )
	{
		if ( empty($_REQUEST['Login']) )
		{
			$errors[] = "<H3>Введите логин</H3>";
		}
		if ( empty($_REQUEST['Password']) )
		{
			$errors[] = "<H3>Введите пароль</H3>";
		}
	}
	if($_REQUEST['Department'] == "" OR
	$_REQUEST['Office'] == "" OR
	$_REQUEST['Post'] == "")
	{
		$errors[] = "<H3>Должны быть выбраны элементы из списков</H3>";
	}
	if ( empty($_REQUEST['Family']) )
	{
		$errors[] = "<H3>Введите фамилию</H3>";
	}
	if ( empty($_REQUEST['Name']) )
	{
		$errors[] = "<H3>Введите имя</H3>";
	}
	if ( empty($_REQUEST['Patronymic']) )
	{
		$errors[] = "<H3>Введите отчество/<H3>";
	}
	if ( empty($_REQUEST['Tabel_Number']) )
	{
		$errors[] = "<H3>Введите табельный номер /<H3>";
	}
	//если флаг статуса сотрудника не проставлен, значит он еще работает
	if ( empty($_REQUEST['Status']) )
	{
		$_REQUEST['Status'] = "TRUE";
	}
	//если флаг утверждения работ сотрудником не проставлен, значит он не утверждающий
	if ( empty($_REQUEST['Sanction']) )
	{
		$_REQUEST['Sanction'] = "FALSE";
	}
	//проверка, если выбор из списков не числовой и не пустой, то заносим их код
	if ( !is_numeric($_REQUEST['Department']) AND
	$_REQUEST['Department'] != "")
	{
		$_REQUEST['Department'] = escapeshellarg(getIdDepartment($_REQUEST['Department']));
	}
	if ( !is_numeric($_REQUEST['Office']) AND
	$_REQUEST['Office'] != "")
	{
		$_REQUEST['Office'] = escapeshellarg(getIdOffice($_REQUEST['Office']));
	}
	if ( !is_numeric($_REQUEST['Post']) AND
	$_REQUEST['Post'] != "" )
	{
		$_REQUEST['Post'] = escapeshellarg(getIdPost($_REQUEST['Post']));
	}
}
//*************************************************************************************************************
// checkEmptyTask($ArrayTask)- функция проверки таблицы заданий
// Входные параметры:
//$ArrayTask -  массив значений полей
// Выходные параметры:
//$ArrayTask -  исправленный массив значений полей
//*************************************************************************************************************

function checkEmptyTask($ArrayTask)
{
	global $errors;
	foreach($ArrayTask['from_dep'] as $key=>$value)
	{
		if(empty($value))
		{
			$errors[] = "<H3>Не выбран отдел выдающий задание!</H3>";
		}
		//проверка, если выбор из списков не числовой и не пустой, то заносим их код
		if ( !is_numeric($value) AND $value != "")
		{
			$ArrayTask['from_dep'][$key] = escapeshellarg(getIdDepartment($value));
		}
	}
	foreach($ArrayTask['to_dep'] as $key=>$value)
	{
		if(empty($value))
		{
			$errors[] = "<H3>Не выбран отдел принимающий задание!</H3>";
		}
		if ( !is_numeric($value) AND $value != "")
		{
			$ArrayTask['to_dep'][$key] = escapeshellarg(getIdDepartment($value));
		}
	}
	foreach($ArrayTask['comment'] as $key=>$value)
	{
		if(empty($value))
		{
			$ArrayTask['comment'][$key] = "&nbsp;";
		}
	}
	if (isset($_REQUEST['ok']))
	{
		$button = $_REQUEST['ok'];
	}
	elseif(isset($_REQUEST['btncopy']))
	{
		$button = $_REQUEST['btncopy'];
	}
	foreach($ArrayTask['date_extradition'] as $key=>$value)
	{
		$ArrayTask['date_extradition'][$key] = checkEmptyDateUniversal($button, $value);
	}
	return $ArrayTask;
}
//*************************************************************************************************************
// checkEmptyMarkBVP($ArrayMark) - функция проверки таблицы
// Входные параметры:
//$ArrayTask -  массив значений полей
// Выходные параметры:
//$ArrayTask -  исправленный массив значений полей
//*************************************************************************************************************

function checkEmptyMarkBVP($ArrayMark)
{
	global $errors;
	foreach($ArrayMark['mark'] as $key=>$value)
	{
		if(empty($value))
		{
			$errors[] = "<H3>Не выбрана марка!</H3>";
		}
		//проверка, если выбор из списков не числовой и не пустой, то заносим их код
		if ( !is_numeric($value) AND $value != "")
		{
			$ArrayMark['mark'][$key] = escapeshellarg(getIdMark($value));
		}
	}
	foreach($ArrayMark['num_mark'] as $key=>$value)
	{
		if ($value == '')
		{
			$ArrayMark['num_mark'][$key] = 0;
			continue;
		}
		if (!intval($value))
		{
			$errors[] = "<H3>Порядковый номер марки должен быть числом!</H3>";
		}
	}
	foreach($ArrayMark['num_change'] as $key=>$value)
	{
		if ($value == '')
		{
			$ArrayMark['num_change'][$key] = 0;
			continue;
		}
		if (!intval($value))
		{
			$errors[] = "<H3>Порядковый номер изменения должен быть числом!</H3>";
		}
	}
	if (isset($_REQUEST['ok']))
	{
		$button = $_REQUEST['ok'];
	}
	elseif(isset($_REQUEST['btncopy']))
	{
		$button = $_REQUEST['btncopy'];
	}
	foreach($ArrayMark['date_extradition_BVP'] as $key=>$value)
	{
		$ArrayMark['date_extradition_BVP'][$key] = checkEmptyDateUniversal($button, $value);
	}
	//в поле дата выдачи заказчику можно ничего не ставить- тогда в базу запишется 0, а не текущая дата
	foreach($ArrayMark['date_customer'] as $key=>$value)
	{
		if ( empty($value) )
		{
			//если поле даты пустое то заполняем 0
			$ArrayMark['date_customer'][$key] = 0;
		}
		else {
			$ArrayMark['date_customer'][$key] = inputDate($value);
		}
	}
	return $ArrayMark;
}
//*************************************************************************************************************
// checkEmptyManHour()- функция проверки  параметров таблицы  Трудозатрат
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function checkEmptyManHour()
{
	global $errors;
	//заменяем запятые на точки
	$_REQUEST['Man_Hour'] = str_replace(",", ".", $_REQUEST['Man_Hour']);
	if($_REQUEST['Name_Mark'] == "" OR
	$_REQUEST['Name_Work'] == "")
	{
		$errors[] = "<H3>Должны быть выбраны элементы из списков</H3>";
	}
	if ( empty($_REQUEST['Man_Hour']) )
	{
		$errors[] = "<H3>Введите значение трудозатрат</H3>";
	}
	//если произошло копирование, то данные полей являются текстовым значением, а не числами
	if ( !is_numeric($_REQUEST['Name_Mark']) AND
	$_REQUEST['Name_Mark'] != "")
	{
		$_REQUEST['Name_Mark'] = escapeshellarg(getIdMark($_REQUEST['Name_Mark']));
	}
	if ( !is_numeric($_REQUEST['Name_Work']) AND
	$_REQUEST['Name_Work'] != "")
	{
		$_REQUEST['Name_Work'] = escapeshellarg(getIdWork($_REQUEST['Name_Work']));
	}
}
//*************************************************************************************************************
// cryptParol($Parol)- функция шифрования методом md5
// Входные параметры: $Parol-входная последовательность символов
// Выходные параметры:
//*************************************************************************************************************

function cryptParol($Parol)
{
	//шифрование методом BLOWFISH входящих данных
	$str = crypt($Parol, CRYPT_BLOWFISH);
	return $str;
}
//*************************************************************************************************************
// checkEmptyMark()- функция проверки  марки на пустоту
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function checkEmptyMark()
{
	global $errors;
	if ( empty($_REQUEST['Name_Mark']) )
	{
		$errors[] = "<H3>Введите марку</H3>";
	}
	if ( empty($_REQUEST['Comment_Mark']) )
	{
		$errors[] = "<H3>Введите расшифровку марки</H3>";
	}
}
//*************************************************************************************************************
// checkEmptyOffice()- функция проверки  названия бюро на пустоту
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function checkEmptyOffice()
{
	global $errors;
	if ( empty($_REQUEST['Name_Office']) )
	{
		$errors[] = "<H3>Введите название бюро</H3>";
	}
}
//*************************************************************************************************************
// checkEmptyPost()- функция проверки  названия должности на пустоту
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function checkEmptyPost()
{
	global $errors;
	if ( empty($_REQUEST['Name_Post']) )
	{
		$errors[] = "<H3>Введите название должности</H3>";
	}
}
//********************************************************************************************
// checkEmptyProject()- функция проверки  параметров проекта на пустоту
// Входные параметры:
// Выходные параметры:
//*********************************************************************************************

function checkEmptyProject()
{
	global $errors;
	//обрезаем пробелы
	$_REQUEST['Number_Project'] = trim($_POST['Number_Project']);
	$_REQUEST['Name_Project'] = trim($_POST['Name_Project']);
	$_REQUEST['Customer'] = trim($_POST['Customer']);  //не обязательно, тк выбирается из списка
	$_REQUEST['Number_UTR'] = trim($_POST['Number_UTR']);
    $_REQUEST['Customer_Service'] = trim($_POST['Customer_Service']);
    $_REQUEST['Plant_Destination'] = trim($_POST['Plant_Destination']);
	$_REQUEST['Report'] = trim($_POST['Report']);
	$_REQUEST['Report_ManHour'] = trim($_POST['Report_ManHour']);

	if ( empty($_REQUEST['Report_ManHour']))
	{
		$_REQUEST['Report_ManHour'] = 0;
	}
	if (strlen($_REQUEST['Number_UTR']) == 0)
	{
		$_REQUEST['Number_UTR'] = "&nbsp;";
	}
	if (strlen($_REQUEST['Report']) == 0)
	{
		$_REQUEST['Report'] = "&nbsp;";
	}

	if (strlen($_REQUEST['Customer_Service']) == 0)
	{
		$_REQUEST['Customer_Service'] = "&nbsp;";
	}

	if (strlen($_REQUEST['Plant_Destination']) == 0)
	{
		$_REQUEST['Plant_Destination'] = "&nbsp;";
	}

	if ( $_REQUEST['rbselect'] != "customer" )
	{
		if ( empty($_REQUEST['Number_Project']))
		{
			$errors[] = "<H3 >Введите номер проекта</H3>";
		}
		if ( empty($_REQUEST['Name_Project']) )
		{
			$errors[] = "<H3>Введите наименование проекта</H3>";
		}
		if ( empty($_REQUEST['Manager']) )
		{
			$errors[] = "<H3>Выберите менеджера проекта </H3>";
		}
	}
	if( empty($_REQUEST['Customer']) )
	{
		$errors[] = "<H3>Должны быть выбран заказчик проекта</H3>";
	}
	if ( !is_numeric($_REQUEST['Report_ManHour']) )
	{
		$errors[] = "<H3>Актованные трудозатраты имеют нечисловое значение</H3>";
	}
	//если произошло копирование, то данные полей являются текстовым значением, а не числами
	if ( !is_numeric($_REQUEST['Customer']) AND !empty($_REQUEST['Customer']) )
	{
		$_REQUEST['Customer'] = escapeshellarg(getIdCustomer($_REQUEST['Customer']));
	}
	if ( !is_numeric($_REQUEST['Manager']) AND  !empty($_REQUEST['Manager']) )
	{
		$_REQUEST['Manager'] = escapeshellarg(getIdEmployee($_REQUEST['Manager']));
	}
}
//********************************************************************************************
// checkEmptyTender()- функция проверки  параметров проекта на пустоту
// Входные параметры:
// Выходные параметры:
//*********************************************************************************************

function checkEmptyTender()
{
	global $errors;
	if ( $_REQUEST['rbselect'] != "customer" )
	{
		if ( empty($_REQUEST['Number_Tender']))
		{
			$errors[] = "<H3 >Введите номер заявки</H3>";
		}
		if ( empty($_REQUEST['Name_Tender']) )
		{
			$errors[] = "<H3>Введите наименование заявки</H3>";
		}
	}
	if( empty($_REQUEST['Customer']) )
	{
		$errors[] = "<H3>Должны быть выбраны элементы из списка</H3>";
	}
	//если произошло копирование, то данные полей являются текстовым значением, а не числами
	if ( !is_numeric($_REQUEST['Customer']) AND
	!empty($_REQUEST['Customer']) )
	{
		$_REQUEST['Customer'] = escapeshellarg(getIdCustomer($_REQUEST['Customer']));
	}
}
//*************************************************************************************************************
// checkEmptyCustomer()- функция проверки названия заказчика на пустоту
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function checkEmptyCustomer()
{
	global $errors;
	if ( empty($_REQUEST['Name_Customer']) )
	{
		$errors[] = "<H3>Введите название заказчика</H3>";
	}
	if ( empty($_REQUEST['Number_Customer']) )
	{
		$errors[] = "<H3>Введите номер заказчика</H3>";
	}
}
//*************************************************************************************************************
// checkMatchManHour()- функция проверки  совпадения записей таблицы трудозатраты
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function  checkMatchManHour()
{
	global $link,$errors;
	$Query = "SELECT * FROM man_hour WHERE id_Mark=".escapeshellarg($_REQUEST['Name_Mark']).
	" AND id_Work=".escapeshellarg($_REQUEST['Name_Work']);
	if ( !($dbResult = mysql_query($Query,$link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		if ( mysql_num_rows($dbResult) !=0 )
		{
			$errors[] = "<H3>Такая запись в таблице трудозатрат уже существует</H3>";
		}
	}
}
//*************************************************************************************************************
// checkMatchEmployee()- функция проверки  совпадения записей таблицы сотрудник
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function checkMatchEmployee()
{
	global $link,$errors;
	$Query = "SELECT * FROM employee WHERE Login=".escapeshellarg($_REQUEST['Login']);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		if ( mysql_num_rows($dbResult) != 0 )
		{
			$errors[] = "<H3>Сотрудник с таким логином уже существует</H3>";
		}
	}
}
//**************************************************************************************************
// checkMatchProject()- функция проверки  совпадения проектов
// Входные параметры:
// Выходные параметры:
//***************************************************************************************************

function checkMatchProject()
{
	global $link,$errors;
	if (strlen($_REQUEST['Number_Project']) != 0)
	{
		//ищем совпадения по номеру проекта или названию
		$Query = "SELECT * FROM project WHERE Number_Project=".escapeshellarg($_REQUEST['Number_Project']);
		
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка $Query не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			if ( mysql_num_rows($dbResult) != 0 )
			{
				$errors[] = "<H3>Проект с таким номером или названием уже существует </H3>";
			}
		}
	}
	if (strlen($_REQUEST['Number_UTR']) != 0)
	{
		//ищем совпадения с номером УТР
		$QueryUTR = "SELECT * FROM project WHERE Number_UTR=".escapeshellarg($_REQUEST['Number_UTR']);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка $Query не удалась!\n".mysql_error();
			processingError("$QueryUTR ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			if ( mysql_num_rows($dbResult) != 0 )
			{
				$errors[] = "<H3>Проект с таким номером УТР уже существует </H3>";
			}
		}
	}
}
//**************************************************************************************************
// checkMatchTender()- функция проверки  совпадения заявок
// Входные параметры:
// Выходные параметры:
//***************************************************************************************************

function checkMatchTender()
{
	global $link,$errors;
	if (strlen($_REQUEST['Number_Tender']) != 0)
	{
		//ищем совпадения по номеру проекта или названию
		$Query = "SELECT * FROM tender_geod WHERE Number_Tender=".escapeshellarg($_REQUEST['Number_Tender']).
		" OR Name_Tender=".escapeshellarg($_REQUEST['Name_Tender']);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка $Query не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			if ( mysql_num_rows($dbResult) != 0 )
			{
				$errors[] = "<H3>Заявка с таким номером или названием уже существует </H3>";
			}
		}
	}
	if (strlen($_REQUEST['Number_UTR']) != 0)
	{
		//ищем совпадения с номером УТР
		$QueryUTR = "SELECT * FROM tender_geod WHERE Number_UTR=".escapeshellarg($_REQUEST['Number_UTR']);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка $Query не удалась!\n".mysql_error();
			processingError("$QueryUTR ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			if ( mysql_num_rows($dbResult) != 0 )
			{
				$errors[] = "<H3>Заявка с таким номером УТР уже существует </H3>";
			}
		}
	}
}
//***********************************************************************************************
// checkMismatchProject()- функция проверки  на несовпадения проектов
// Входные параметры:
// Выходные параметры:
//***********************************************************************************************

function checkMismatchProject()
{
	global $link,$errors,$successes;
	if (strlen($_REQUEST['Number_Project']) != 0)
	{
		//проверяем проект на существование с указанным номером
		$Query = "SELECT id FROM project WHERE Number_Project=".escapeshellarg($_REQUEST['Number_Project']);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка $Query не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			if ( mysql_num_rows($dbResult) == 0 )
			{
				$successes[] = "<H3>Проекта с таким номером не существует </H3>";
			}
			else {
				$errors[] = "<H3>Проект с таким номером уже существует </H3>";
			}
		}
	}
	if (strlen($_REQUEST['Number_UTR']) != 0)
	{
		//проверяем на существование проекта с указанным номером УТР
		$Query = "SELECT id FROM project WHERE Number_UTR=".escapeshellarg($_REQUEST['Number_UTR']);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка $Query не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			if ( mysql_num_rows($dbResult) == 0 )
			{
				$successes[] = "<H3>Проекта с таким номером УТР не существует </H3>";
			}
			else {
				$errors[] = "<H3>Проект с таким номером УТР существует </H3>";
			}
		}
	}
}
//************************************************************************************************
// checkMismatchTender()- функция проверки  на несовпадения заявок в геодезии
// Входные параметры:
// Выходные параметры:
//************************************************************************************************

function checkMismatchTender()
{
	global $link,$errors,$successes;
	if (strlen($_REQUEST['Number_Tender']) != 0)
	{
		//проверяем проект на существование с указанным номером
		$Query = "SELECT id FROM tender_geod WHERE Number_Tender=".escapeshellarg($_REQUEST['Number_Tender']);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка $Query не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			if ( mysql_num_rows($dbResult) == 0 )
			{
				$successes[] = "<H3>Заявка с таким номером не существует </H3>";
			}
			else {
				$errors[] = "<H3>Заявка с таким номером уже существует </H3>";
			}
		}
	}
	if (strlen($_REQUEST['Number_UTR']) != 0)
	{
		//проверяем на существование проекта с указанным номером УТР
		$Query = "SELECT id FROM tender_geod WHERE Number_UTR=".escapeshellarg($_REQUEST['Number_UTR']);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка $Query не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			if ( mysql_num_rows($dbResult) == 0 )
			{
				$successes[] = "<H3>Заявка с таким номером УТР не существует </H3>";
			}
			else {
				$errors[] = "<H3>Заявка с таким номером УТР существует </H3>";
			}
		}
	}
}
//*************************************************************************************************************
// checkMatchCustomer()- функция проверки  совпадения заказчиков
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function checkMatchCustomer()
{
	global $link,$errors;
	$Query = "SELECT * FROM customer WHERE Number_Customer=".escapeshellarg($_REQUEST['Number_Customer']);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		if ( mysql_num_rows($dbResult) != 0 )
		{
			$errors[] = "<H3>Заказчик с таким номером уже существует</H3>";
		}
	}
}
//*************************************************************************************************************
// checkMatchWork()- функция проверки  совпадения работы
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function checkMatchWork()
{
	global $link,$errors;
	$Query = "SELECT * FROM work WHERE Name_Work=".escapeshellarg($_REQUEST['Name_Work']).
	" AND Type_Work=".escapeshellarg($_REQUEST['Type_Work']);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		if ( mysql_num_rows($dbResult) != 0 )
		{
			$errors[] = "<H3>Такая работа уже существует</H3>";
		}
	}
}
//*************************************************************************************************************
// checkEmptyWork()- функция проверки  параметров работ на пустоту
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function checkEmptyWork()
{
	global $errors;
	if ( empty($_REQUEST['Name_Work']) )
	{
		$errors[] = "<H3>Введите имя работы</H3>";
	}
	if ( empty($_REQUEST['Type_Work']) )
	{
		$errors[] = "<H3>Введите тип работ</H3>";
	}
	if ( empty($_REQUEST['Comment']) )
	{
		$_REQUEST['Comment'] = "no comment";
	}
}

//**********************************************************************************************
// getIntervalDateYear($year, $table,$column) - функция,возвращающая временной интервал года
// Входные параметры:
//$year- год по которому осуществляется поиск
//$table - по какой таблице ведется поиск
//$column- по какому столбцу ведется поиск
// Выходные параметры:  строка интервала
//**********************************************************************************************
/*function getIntervalDateYear($year, $table,$column, $period = "26-25")
{
	//инициализируем границы временного отрезка
	$DateBegin = 0;
	$DateEnd = mktime();	   //инициализируем границы временного отрезка
	if ($period == "26-25")
	{
			$DateBegin = mktime(0, 0, 0, 0, 26, $year);   //начало отчетного периода с 26 числа предыдущего декабря
			$DateEnd = DateAdd('m', 12, $DateBegin);
			$QueryDate = " AND (($table.$column>=".escapeshellarg($DateBegin).") AND ($table.$column<".escapeshellarg($DateEnd)."))";

	} elseif($period == "1-31")
	{
			$DateBegin = mktime(0, 0, 0, 1, 1, $year);   //начало отчетного периода с 1 числа текущего месяца
			$DateEnd = DateAdd('m', 12, $DateBegin);
			$QueryDate = " AND (($table.$column>=".escapeshellarg($DateBegin).") AND ($table.$column<".escapeshellarg($DateEnd)."))";

	}
	return  $QueryDate;

}*/
//**********************************************************************************************
// getIntervalDate($month, $year, $table,$column) - функция,возвращающая временной интервал
// Входные параметры:
//$month-месяц по которому осуществляется поиск
//$year- год по которому осуществляется поиск
//$table - по какой таблице ведется поиск
//$column- по какому столбцу ведется поиск
// Выходные параметры:  строка интервала
//**********************************************************************************************

function getIntervalDate($month, $year, $table,$column, $period = "26-25")
{
	//инициализируем границы временного отрезка
	$DateBegin = 0;
	$DateEnd = mktime();	   //инициализируем границы временного отрезка
	if ($period == "26-25")
	{
		//==========================================================================
		//выбираем значения месяца и года
		if ( !($month == "Выберите месяц" OR $year == "Выберите год") )
		{
			$DateBegin = mktime(0, 0, 0, $month-1, 26, $year); //начало отчетного периода с 26 числа
			$DateEnd = DateAdd('m', 1, $DateBegin);
			$QueryDate = " AND (($table.$column>=".escapeshellarg($DateBegin).") AND ($table.$column<".escapeshellarg($DateEnd)."))";
		}
		//выбираем значения года
		elseif ( $month == "Выберите месяц" AND $year != "Выберите год")
		{
			$DateBegin = mktime(0, 0, 0, 0, 26, $year);   //начало отчетного периода с 26 числа предыдущего декабря
			$DateEnd = DateAdd('m', 12, $DateBegin);
			$QueryDate = " AND (($table.$column>=".escapeshellarg($DateBegin).") AND ($table.$column<".escapeshellarg($DateEnd)."))";
		}
		//выбираем значения месяца
		elseif ( $month != "Выберите месяц" AND $year == "Выберите год")
		{
			$DateBegin = mktime(0, 0, 0, $month-1, 26, $_SESSION['CurrentYear']);//начало отчетного периода с 26 числа
			$DateEnd = DateAdd('m', 1, $DateBegin);
			$QueryDate = " AND (($table.$column>=".escapeshellarg($DateBegin).") AND ($table.$column<".escapeshellarg($DateEnd)."))";
		}
		else {
			$QueryDate = " AND ($table.$column>".escapeshellarg($DateBegin).")";
		}
		} elseif($period == "1-31")
	{
		//------------------------------------------------------------------------------
		//выбираем значения месяца и года
		if ( !($month == "Выберите месяц" OR $year == "Выберите год") )
		{
			$DateBegin = mktime(0, 0, 0, $month, 1, $year); //начало отчетного периода с 1 числа
			$DateEnd = DateAdd('m', 1, $DateBegin);
			$QueryDate = " AND (($table.$column>=".escapeshellarg($DateBegin).") AND ($table.$column<".escapeshellarg($DateEnd)."))";
		}
		//выбираем значения года
		elseif ( $month == "Выберите месяц" AND $year != "Выберите год")
		{
			$DateBegin = mktime(0, 0, 0, 1, 1, $year);   //начало отчетного периода с 1 числа текущего месяца
			$DateEnd = DateAdd('m', 12, $DateBegin);
			$QueryDate = " AND (($table.$column>=".escapeshellarg($DateBegin).") AND ($table.$column<".escapeshellarg($DateEnd)."))";
		}
		//выбираем значения месяца
		elseif ( $month != "Выберите месяц" )
		{
			$DateBegin = mktime(0, 0, 0, $month, 1, $_SESSION['CurrentYear']);//начало отчетного периода с 1 числа
			$DateEnd = DateAdd('m', 1, $DateBegin);
			$QueryDate = " AND (($table.$column>=".escapeshellarg($DateBegin).") AND ($table.$column<".escapeshellarg($DateEnd)."))";
		}
		else {
			$QueryDate = " AND ($table.$column>".escapeshellarg($DateBegin).")";
		}
	}
	return  $QueryDate;
}
//******************************************************************************
// getIntervalToDate($month, $year, $table,$column) - функция,возвращающая временной интервал  до указанной даты
// Входные параметры:
//$month-месяц по которому осуществляется поиск
//$year- год по которому осуществляется поиск
//$table - по какой таблице ведется поиск
//$column- по какому столбцу ведется поиск
// Выходные параметры:  строка интервала
//******************************************************************************

function getIntervalToDate($month, $year, $table,$column, $period = "26-25")
{
	//инициализируем границы временного отрезка
	$DateBegin = 0;
	if ($period == "26-25")
	{
		$day = 26;  //по 25 включительно
		//выбираем значения месяца и года
		if ( !($month == "Выберите месяц" OR $year == "Выберите год") )
		{
			$DateEnd = mktime(0, 0, 0, $month, $day, $year); //конец отчетного периода до 26 числа текущего месяца
			$QueryDate = " AND (($table.$column>=".escapeshellarg($DateBegin).") AND ($table.$column<".escapeshellarg($DateEnd)."))";
		}
		//выбираем значения года
		elseif ( $month == "Выберите месяц" AND $year != "Выберите год")
		{
			$DateEnd = mktime(0, 0, 0, 12, $day, $year);   //конец отчетного периода до 26 числа декабря текущего года
			$QueryDate = " AND (($table.$column>=".escapeshellarg($DateBegin).") AND ($table.$column<".escapeshellarg($DateEnd)."))";
		}
		//выбираем значения месяца
		elseif ( $month != "Выберите месяц" AND $year == "Выберите год")
		{
			$DateEnd = mktime(0, 0, 0, $month, $day, $_SESSION['CurrentYear']);//конец отчетного периода до 26 числа
			$QueryDate = " AND (($table.$column>=".escapeshellarg($DateBegin).") AND ($table.$column<".escapeshellarg($DateEnd)."))";
		}
		else {
			$QueryDate = " AND ($table.$column>".escapeshellarg($DateBegin).")";
		}
		} elseif($period == "1-31")
	{
		$day = 1;
		//выбираем значения месяца и года
		if ( !($month == "Выберите месяц" OR $year == "Выберите год") )
		{
			$DateEnd = mktime(0, 0, 0, $month+1, $day, $year); //конец отчетного периода до 1 числа след. месяца
			$QueryDate = " AND (($table.$column>=".escapeshellarg($DateBegin).") AND ($table.$column<".escapeshellarg($DateEnd)."))";
		}
		//выбираем значения года
		elseif ( $month == "Выберите месяц" AND $year != "Выберите год")
		{
			$DateEnd = mktime(0, 0, 0, 1, $day, $year+1);   //конец отчетного периода до 1 января след года
			$QueryDate = " AND (($table.$column>=".escapeshellarg($DateBegin).") AND ($table.$column<".escapeshellarg($DateEnd)."))";
		}
		//выбираем значения месяца
		elseif ( $month != "Выберите месяц" AND $year == "Выберите год")
		{
			$DateEnd = mktime(0, 0, 0, $month+1, $day, $_SESSION['CurrentYear']);//конец отчетного периода до 26 числа
			$QueryDate = " AND (($table.$column>=".escapeshellarg($DateBegin).") AND ($table.$column<".escapeshellarg($DateEnd)."))";
		}
		else {
			$QueryDate = " AND ($table.$column>".escapeshellarg($DateBegin).")";
		}
	}
	return  $QueryDate;
}
//******************************************************************************
// getIntervalFromDate($month, $year, $table,$column) - функция,возвращающая временной интервал от указанной даты
// Входные параметры:
//$month-месяц по которому осуществляется поиск
//$year- год по которому осуществляется поиск
//$table - по какой таблице ведется поиск
//$column- по какому столбцу ведется поиск
// Выходные параметры:  строка интервала
//******************************************************************************

function getIntervalFromDate($month, $year, $table,$column, $period = "26-25")
{
	//инициализируем границы временного отрезка
	if ($period == "26-25")
	{
		//==========================================================================
		//выбираем значения месяца и года
		if ( !($month == "Выберите месяц" OR $year == "Выберите год") )
		{
			$DateBegin = mktime(0, 0, 0, $month-1, 26, $year); //начало отчетного периода с 26 числа
		}
		//выбираем значения года
		elseif ( $month == "Выберите месяц" AND $year != "Выберите год")
		{
			$DateBegin = mktime(0, 0, 0, 0, 26, $year);   //начало отчетного периода с 26 числа предыдущего декабря
		}
		//выбираем значения месяца
		elseif ( $month != "Выберите месяц" AND $year == "Выберите год")
		{
			$DateBegin = mktime(0, 0, 0, $month-1, 26, $_SESSION['CurrentYear']);//начало отчетного периода с 26 числа
		}
		} elseif($period == "1-31")
	{
		//------------------------------------------------------------------------------
		//выбираем значения месяца и года
		if ( !($month == "Выберите месяц" OR $year == "Выберите год") )
		{
			$DateBegin = mktime(0, 0, 0, $month, 1, $year); //начало отчетного периода с 1 числа
		}
		//выбираем значения года
		elseif ( $month == "Выберите месяц" AND $year != "Выберите год")
		{
			$DateBegin = mktime(0, 0, 0, 1, 1, $year);   //начало отчетного периода с 1 числа текущего месяца
		}
		//выбираем значения месяца
		elseif ( $month != "Выберите месяц" )
		{
			$DateBegin = mktime(0, 0, 0, $month, 1, $_SESSION['CurrentYear']);//начало отчетного периода с 1 числа
		}
	}
	$QueryDate = " AND ($table.$column>".escapeshellarg($DateBegin).")";
	return  $QueryDate;
}
//***********************************************************************************************
// ViewTableSearchDesign()- функция отображения рабочей таблицы с учетом данных по сотрудникам, объектам и времени
// Входные параметры:
// Выходные параметры:
//***********************************************************************************************

function ViewTableSearchDesign()
{
	global $link;
	//Если не нажаты никакие кнопки то завершение работы скрипта
	if ( empty($_REQUEST['rbstatusBVP']) AND
	empty($_REQUEST['rbstatusBVP_Archive']) AND
	empty($_REQUEST['Поиск']))
	{
		return;
	}

	//при нажатии на кнопку "утвердить" производятся действия обновления, остальные данные находятся в сессии
	if ( isset($_REQUEST['rbstatusBVP']) )
	{
		switch ($_REQUEST['rbstatusBVP'])
		{
			case "addstatus":
			$Comma_separated = implode(",", $_REQUEST['design']);
			//если утвердить данную работу, то статус проверки и утверждения измениться на TRUE (сдано, утверждено)
			$Query = "UPDATE design SET checkBVP=".escapeshellarg('TRUE').
            ", offPlan=".escapeshellarg('0').
			", statusBVP=".escapeshellarg($_REQUEST['Date']).
			"  WHERE Id IN ($Comma_separated)";

			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				print "запрос $Query не выполнен\n".mysql_error();
				processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			break;
			case "delstatus":
			$Comma_separated = implode(",", $_REQUEST['design']);
			//если вернуть в разработку данную работу, то время утверждения измениться на 0 (нет), статус checkBVP на False
			$Query = "UPDATE design SET statusBVP=".escapeshellarg('0').
            ", offPlan=".escapeshellarg('0').
			", checkBVP=".escapeshellarg('FALSE').
			"  WHERE Id IN ($Comma_separated)";
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				//print"запрос $Query не выполнен\n".mysql_error();
				processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			break;
			case "substatus":
			$Comma_separated = implode(",", $_REQUEST['design']);
			//если утвердить данную работу, то статус проверки и утверждения измениться на TRUE (сдано, утверждено)
				$Query = "UPDATE design SET checkBVP=".escapeshellarg('TRUE').
                ", statusBVP=".escapeshellarg('0').
    			", offPlan=".escapeshellarg($_REQUEST['Date']).
	    		"  WHERE Id IN ($Comma_separated)";

			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				print "запрос $Query не выполнен\n".mysql_error();
				processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			break;
		}
		} elseif ( isset($_REQUEST['rbstatusBVP_Archive']) )	{
		switch ($_REQUEST['rbstatusBVP_Archive'])
		{
			case "addstatus":
			$Comma_separated = implode(",", $_REQUEST['design']);
			//если поле ввода даты оставить пустым,то произойдет копирование даты утверждения в дату подтверждения
			if ( empty($_REQUEST['DateArchive']) )
			{
				//если подтвердить данную работу, то статус проверки и утверждения не изменяются
				$Query = "UPDATE design SET approvalBVP=statusBVP ".//escapeshellarg($_REQUEST['DateArchive']).
				"  WHERE Id IN ($Comma_separated)";
			}
			else  {
				$Query = "UPDATE design SET approvalBVP=".escapeshellarg($_REQUEST['DateArchive']).
				"  WHERE Id IN ($Comma_separated)";
			}
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				//print "запрос $Query не выполнен\n".mysql_error();
				processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			break;
			case "delstatus":
			$Comma_separated = implode(",", $_REQUEST['design']);
			//если вернуть на комплектацию данную работу, то статус проверки и утверждения измениться на FALSE (нет)
				$Query = "UPDATE design SET approvalBVP=".escapeshellarg('0').
			"  WHERE Id IN ($Comma_separated)";
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				//print"запрос $Query не выполнен\n".mysql_error();
				processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
			}
			break;
		}
		} else {
		//иначе формируем запрос заново и помещаем данные в сессию
		if ( isset($_REQUEST['Поиск']) )
		{
/*				if ( empty($_REQUEST['lstEmployee']) )
				{
					//если список будет пустым, то по умолчанию выбирается текущий пользователь
					$_SESSION['NameSelectedEmployee'] = getNameEmployee($_SESSION['Id_Employee']);
					$Query2 = " AND (employee.id=".escapeshellarg($_SESSION['Id_Employee']).")";
				}
				else {
					//дополнение к основному запросу-выбираются записи всех сотрудников, выделенных в списке
					//если выбран 1 сотрудник то заносим его в сессию
					if ( count($_REQUEST['lstEmployee']) == 1 )
					{
						$_SESSION['NameSelectedEmployee'] = getNameEmployee(current($_REQUEST['lstEmployee']));
					}
					$Comma_separated = implode(",", $_REQUEST['lstEmployee']);
					$Query2 = " AND (employee.id IN ($Comma_separated))";
				}*/


                if($_REQUEST['lstEmployee'] == "Выбор ФИО")
                {
                    //если список будет пустым, то по умолчанию выбирается текущий пользователь
                    $_SESSION['NameSelectedEmployee'] = getNameEmployee($_SESSION['Id_Employee']);
					$Query2 = " AND (employee.id=".escapeshellarg($_SESSION['Id_Employee']).")";

                        //проверка на выбор подразделения
                        if( $_REQUEST['Department'] == "Выбор отдела" AND
        				    $_REQUEST['Office'] == "Выбор бюро")
        				{

        					if ($_SESSION['Name_Post'] == "Начальник отдела" OR
        					$_SESSION['Name_Post'] == "Зам.начальника отдела" OR
        					$_SESSION['Name_Post'] == "Старший специалист")
        					{
        						$Query2 = " AND (department.id=".escapeshellarg(getIdDepartment($_SESSION['Name_Department'])).")";
        					}
        					else {
        						$Query2 = "";
        					}
                           unset($_SESSION['NameSelectedEmployee']);
        					}
                        elseif ( $_REQUEST['Department'] != "Выбор отдела" AND
                                     $_REQUEST['Office'] == "Выбор бюро")
        				{
        					//выбирается отдел
           					$Query2 = " AND (department.id=".escapeshellarg($_REQUEST['Department']).")";
                            unset($_SESSION['NameSelectedEmployee']);

        				}
        				elseif ( $_REQUEST['Department'] != "Выбор отдела" AND
        				         $_REQUEST['Office'] != "Выбор бюро")
        				{
        					//выбирается отдел и бюро
        					$Query2 = " AND (department.id=".escapeshellarg($_REQUEST['Department']).") AND ".
        					"(office.id=".escapeshellarg($_REQUEST['Office']).")";
                             unset($_SESSION['NameSelectedEmployee']);

        				}
        				else	{
        					print "<H3>Ошибка! Укажите название отдела<H3>";
        					return;
        				}

                } else
                {
                    $_SESSION['NameSelectedEmployee'] = getNameEmployee($_REQUEST['lstEmployee']);
                    $Query2 = " AND (employee.id=".escapeshellarg($_REQUEST['lstEmployee']).")";
                }



			//дополнение к основному запросу-выбираются объекты, выделенные в списке
			if ( $_REQUEST['lstObject'] != "Выберите объект" )
			{
				$Query3 = " AND (project.id=".escapeshellarg($_REQUEST['lstObject']).")";
			}
			else {
			    //иначе выбираются все объекты
				$Query3 = "";
			}



			//unset($_SESSION['lstMonth']); //можно ли убрать-переменные обнуляются при каких то действиях
			//unset($_SESSION['lstYear']);
			//если указана плавающая дата, то принимаем ее во внимание, иначе период поиска берется фиксированный

			
			if (!empty($_REQUEST['DateBegin']) OR !empty($_REQUEST['DateEnd']))
			{

				$Query4 = " AND ((design.Date>=".escapeshellarg($_REQUEST['DateBegin']).
				") AND (design.Date<".escapeshellarg($_REQUEST['DateEnd']+3600*24)."))";
				//утверждена ли данная работа-----------------------------------
				if ( $_REQUEST['statusBVP'] == "TRUE" )
				{
					$Query4 = "";//если ищется утвержденные работы, то поиск ведется только по полю дата утверждения
					$Query6 = " AND ((design.statusBVP>=".escapeshellarg($_REQUEST['DateBegin']).
					") AND (design.statusBVP<".escapeshellarg($_REQUEST['DateEnd']+3600*24)."))";
				}
				else {
					$Query6 = "";
				}
			} else 
			{
				$Query4 =  getIntervalDate(
				$_REQUEST['lstMonth'],
				$_REQUEST['lstYear'],
				"design",
				"Date",
				$_REQUEST['rbPeriod']);
				//утверждена ли данная работа-----------------------------------
				if ( $_REQUEST['statusBVP'] == "TRUE" )
				{
					$Query4 = "";//если ищется утвержденные работы, то поиск ведется только по полю дата утверждения
					$Query6 =  getIntervalDate($_REQUEST['lstMonth'],$_REQUEST['lstYear'],	"design","statusBVP",$_REQUEST['rbPeriod']);
				}
				else {
					$Query6 = "";
				}
			}
			// сдана ли данная работа на проверку---------------------------
			if ( $_REQUEST['searchBVP'] == "TRUE" )
			{
				$Query5 = " AND (design.checkBVP=".escapeshellarg($_REQUEST['searchBVP']).")";
			}
			else {
				$Query5 = "";
			}
			if ( $_REQUEST['Name_Mark'] != "Выберите марку" )
			{
				$Query7 = " AND (mark.id=".escapeshellarg($_REQUEST['Name_Mark']).")";
			}
			else {
				$Query7 = "";
			}
			//из запроса убрана таблица work
			$Query1 = "SELECT design.Id, design.Date, design.checkBVP, design.statusBVP, design.approvalBVP, design.offPlan, ".
			"employee.Family, employee.Name, employee.Patronymic, post.Name_Post, ".
			"project.Number_Project, project.Name_Project,".
			"department.Name_Department, department.id, office.Name_Office, office.id, ".
			"mark.Name_Mark, ".
			"design.Time1, design.Sheet_A1, design.Man_Hour1, design.k_Sheet, ".
			"design.Time3, design.Sheet_A3, design.Man_Hour3, design.num_cher,".//Добавил design.num_cher Ден
			"design.Time4, design.Sheet_A4, design.Man_Hour4, design.prov,".//Добавил design.prov, Ден
			"design.Time_Collection, design.Time_Agreement, design.Time, design.Man_Hour_Sum ".
			"FROM design, employee, project, mark, department, office, post ".
			"WHERE ".
			"((design.id_Mark = mark.id) AND ".
			"(design.id_Project = project.id) AND ".
			"(design.id_Employee = employee.id) AND ".
			"(employee.id_Post = post.id) AND ".
			"(employee.id_Department = department.id) AND ".
			"(employee.id_Office = office.id))";
			//соединяем все запросы в один
			$Query = $Query1.$Query2.$Query3.$Query4.$Query5.$Query6.$Query7." ORDER BY design.Date DESC";
			//print $Query;

			//добавим запрос в сессию
			$_SESSION['Query'] = $Query;
			$_SESSION['lstMonth'] = $_REQUEST['lstMonth'];
			$_SESSION['lstYear'] = $_REQUEST['lstYear'];
			$_SESSION['Department'] = $_REQUEST['Department'];
			$_SESSION['Office'] = $_REQUEST['Office'];
		}
	}
	//file_put_contents("query.txt",$_SESSION['Query']);
	if ( !($dbResult = mysql_query($_SESSION['Query'], $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else   {
		print "<H4>Всего ".mysql_num_rows($dbResult)." позиций </H4>";
		//таблица HTML

		print "<table  class=\"f-table-zebra\">\n";
        print "<caption>Объектная карта</caption>";
		print "<THEAD>\n";
		//переделал таблицу
		print "<TR>\n";
		print "<TH rowspan=2>Метка</TH>\n";
		print "<TH rowspan=2>Дата внесения</TH>\n";
		print "<TH rowspan=2>Сотрудник</TH>\n";
		if ($_SESSION['Name_Post'] == "Администратор" or $_SESSION['Name_Post']=="Экономист")
		{
			print "<TH rowspan=2>Должность</TH>\n";	
		}
		print "<TH rowspan=2>Номер объекта</TH>\n";
		print "<TH rowspan=2>Номер чертежа</TH>\n";
		print "<TH rowspan=2>Марка</TH>\n";
		print "<TH colspan=3>Графическая часть</TH>\n";
		print "<TH colspan=6>Текстовая часть</TH>\n";
		print "<TH rowspan=2>К-т заполн листа</TH>\n";
		print "<TH rowspan=2>Сбор данных</TH>\n";
		print "<TH rowspan=2>Согл.-ие</TH>\n";
		if (($_SESSION['Name_Post']=="Экономист") OR
		($_SESSION['Name_Post'] == "Начальник управления") OR
		($_SESSION['Name_Post'] == "Начальник отдела") OR
		($_SESSION['Name_Post'] == "Зам.начальника отдела") OR
		($_SESSION['Name_Post'] == "Начальник бюро") OR
		($_SESSION['Name_Post'] == "Администратор"))
		{
			print "<TH rowspan=2>Проверка</TH>\n";
		}
		print "<TH rowspan=2>Итого часов <br />(табель)</TH>\n";
		print "<TH rowspan=2>Итого чел.-час <br />(трудозатраты)</TH>\n";
		print "<TH rowspan=2>Сдача на проверку</TH>\n";
		print "<TH rowspan=2>Дата утверждения</TH>\n";
		print "<TH rowspan=2>Вне плана</TH>\n";
		print "<TH rowspan=2>Подтвер-\nждение БВП</TH>\n";
		print "</TR>\n";
		print "<TR>\n";
		print "<TH>Лист А1</TH>\n";
		print "<TH>Факт. время</TH>\n";
		print "<TH>Труд.-ты</TH>\n";
		print "<TH>Лист А3</TH>\n";
		print "<TH>Факт. время</TH>\n";
		print "<TH>Труд.-ты</TH>\n";
		print "<TH>Лист А4</TH>\n";
		print "<TH>Факт. время</TH>\n";
		print "<TH>Труд.-ты</TH>\n";
		print "</TR>\n";
		print "</THEAD>";
		$Sheet_A1 = 0;
		$Time1 = 0;
		$Sheet_A3 = 0;
		$Time3 = 0;
		$Sheet_A4 = 0;
		$Time4 = 0;
		$Time_Collection = 0;
		$Time_Agreement = 0;
		$prov = 0;
		$Time = 0;
		$ManHourSum = 0;
		print "<TBODY>\n";
		while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			if ( $row['checkBVP'] == "TRUE" )
			{
				$str =  "<TR align=center style=\"background-color:#C0E4FF\">\n"; //голубой
			}
            if ($row['statusBVP'] <> 0)
			{
				$str =  "<TR align=center style=\"background-color:#D8FFC0\">\n"; //зеленый
			}
            if ($row['offPlan'] <> 0)
			{
				$str =  "<TR align=center style=\"background-color:#CFC0EF\">\n"; //фиолетовый
			}
			if ($row['checkBVP'] == "FALSE" AND	$row['statusBVP'] == 0 AND $row['offPlan'] == 0)
			{
				$str =  "<TR align=center>\n";
			}

            print $str;
			//все записи запоминаются в скрытой переменной-массиве
			print "<input name=\"designall[]\" type=\"hidden\" value=\"{$row['Id']}\">\n";
			if ($_REQUEST['rbstatusBVP'] == "select_all" OR $_REQUEST['rbstatusBVP_Archive'] == "select_all")
			{
				print "<TD><input name=\"design[]\" type=\"checkbox\" value=\"{$row['Id']}\" checked>\n";
			}
			else {
				print "<TD><input name=\"design[]\" type=\"checkbox\" value=\"{$row['Id']}\">\n";
				//print "<TD>{$row['Id']}</TD>\n";
			}
			if ($_SESSION['Name_Post'] == "Администратор")
			{
				print "<TD>".getDateTime($row['Date'] + $_SESSION['localTime'] * 3600)."</TD>\n";
			}
			else {
				print "<TD>".strftime("%d.%m.%y",$row['Date'] + $_SESSION['localTime'] * 3600)."</TD>\n";
			}
			print "<TD nowrap align=left>{$row['Family']} ".mb_substr($row['Name'],0,1,'utf8').".".	mb_substr($row['Patronymic'],0,1,'utf8').".</TD>\n";
			if ($_SESSION['Name_Post'] == "Администратор" or $_SESSION['Name_Post']=="Экономист")
			{
			printZeroTD($row['Name_Post']);
			}
			printZeroTD($row['Number_Project']);
			printZeroTD($row['num_cher']);
			printZeroTD($row['Name_Mark']);
			printZeroTD($row['Sheet_A1']);
			printZeroTD($row['Time1']);
			printZeroTD($row['Man_Hour1']);
			printZeroTD($row['Sheet_A3']);
			printZeroTD($row['Time3']);
			printZeroTD($row['Man_Hour3']);
			printZeroTD($row['Sheet_A4']);
			printZeroTD($row['Time4']);
			printZeroTD($row['Man_Hour4']);
			printZeroTD($row['k_Sheet']);
			printZeroTD($row['Time_Collection']);
			printZeroTD($row['Time_Agreement']);
			if (($_SESSION['Name_Post'] == "Экономист") OR
			($_SESSION['Name_Post'] == "Начальник управления") OR
			($_SESSION['Name_Post'] == "Начальник отдела") OR
			($_SESSION['Name_Post'] == "Зам.начальника отдела") OR
			($_SESSION['Name_Post'] == "Начальник бюро") OR
			($_SESSION['Name_Post'] == "Администратор"))
			{
				printZeroTD($row['prov']);
			}
			printZeroTD($row['Time']);
			printZeroTD(round($row['Man_Hour_Sum'],2));
			if ( $row['checkBVP'] == "TRUE" )
			{
				printZeroTD("<img class= \"icon\" src=\"img/success.png\" align=\"middle\">");
			}
			else {
				printZeroTD("<img class= \"icon\" src=\"img/validation.png\" align=\"middle\">");
			}
			if ( $row['statusBVP'] <> 0 )
			{
				printZeroTD(strftime("%d.%m.%y",$row['statusBVP'] + $_SESSION['localTime'] * 3600));
				//print "<TD>".getDateTime($row['statusBVP'])."</TD>\n";
			}
			else {
				printZeroTD("<img class= \"icon\" src=\"img/validation.png\" align=\"middle\">");
			}
			if ( $row['offPlan'] <> 0 )
			{
				printZeroTD(strftime("%d.%m.%y",$row['offPlan'] + $_SESSION['localTime'] * 3600));
				//print "<TD>".getDateTime($row['statusBVP'])."</TD>\n";
			}
			else {
				printZeroTD("<img class= \"icon\" src=\"img/validation.png\" align=\"middle\">");
			}
			if ( $row['approvalBVP'] <> 0 )
			{
				printZeroTD(strftime("%d.%m.%y",$row['approvalBVP'] + $_SESSION['localTime'] * 3600));
				//print "<TD>".getDateTime($row['approvalBVP'])."</TD>\n";
			}
			else {
				printZeroTD("<img class= \"icon\" src=\"img/validation.png\" align=\"middle\">");
			}
            print("</tr>\n");
			$Sheet_A1 = $Sheet_A1 + $row['Sheet_A1'];
			$Sheet_A3 = $Sheet_A3 + $row['Sheet_A3'];
			$Sheet_A4 = $Sheet_A4 + $row['Sheet_A4'];
			$Time1 = $Time1 + $row['Time1'];
			$Time3 = $Time3 + $row['Time3'];
			$Time4 = $Time4 + $row['Time4'];
			$Time_Collection = $Time_Collection + $row['Time_Collection'];
			$Time_Agreement = $Time_Agreement + $row['Time_Agreement'];
			$prov = $prov + $row['prov'];
			$Time = $Time + $row['Time'];
			$ManHourSum = $ManHourSum+$row['Man_Hour_Sum'];
		}
		print "<TR align=center>\n";
		if ($_SESSION['Name_Post'] == "Администратор" or $_SESSION['Name_Post']=="Экономист")
		{
			print "<TH colspan=7 align=right>Итого:</TH>\n";
		} else
		{
			print "<TH colspan=6 align=right>Итого:</TH>\n";
		}
		
		printZeroTH(round($Sheet_A1, 1));
		printZeroTH(round($Time1, 1));
		print "<TH>&nbsp</TH>\n";
		printZeroTH(round($Sheet_A3, 1));
		printZeroTH(round($Time3, 1));
		print "<TH>&nbsp</TH>\n";
		printZeroTH(round($Sheet_A4, 1));
		printZeroTH(round($Time4, 1));
		print "<TH>&nbsp</TH>\n";
		print "<TH>&nbsp</TH>\n";
		printZeroTH(round($Time_Collection, 1));
		printZeroTH(round($Time_Agreement, 1));
		if ( ($_SESSION['Name_Post'] == "Экономист" ) OR
		($_SESSION['Name_Post'] == "Начальник управления") OR
		($_SESSION['Name_Post'] == "Начальник отдела") OR
		($_SESSION['Name_Post'] == "Зам.начальника отдела") OR
		($_SESSION['Name_Post'] == "Начальник бюро") OR
		($_SESSION['Name_Post'] == "Администратор"))
		{
			printZeroTH(round($prov, 1));
		}
		printZeroTH(round($Time, 1));
		printZeroTH(round($ManHourSum, 1));
		print "<TH>&nbsp</TH>\n";
		print"<TH>&nbsp</TH>\n";
		print"<TH>&nbsp</TH>\n";
		print"<TH>&nbsp</TH>\n";
		print "</TBODY>\n";
		print"</TABLE>\n";
		//print "</div>";
	}
}
//*************************************************************************************************************
// ViewTableSearch_One()- функция отображения рабочей таблицы для 1 сотрудника в модуле поиска
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function ViewTableSearch_One()
{
	//global $Month,$Year;
	global $link;
	$Month = $_SESSION['Month'];
	$Year =  $_SESSION['Year'];
	if ( !($dbResult = mysql_query($_SESSION['Query'], $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("{$_SESSION['Query']} ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else
	{
		print "<Table  Width=\"70%\" Border=\"1\" CellSpacing=\"0\" CellPadding=\"2\" rules=\"all\">\n";
		print "<Tr>\n";
		print "<Td>";
		print "Месяц:";
		print($Month[$_SESSION['lstMonth']]);
		//---------------
		print "<Td>";
		print "Год ";
		print($Year[$_SESSION['lstYear']]);
		print "<Tr>\n";
		print "<Td>";
		print "Отдел: ";
		if ( $_SESSION['Department'] != "Выбор отдела" )
		{
			print getNameDepartment($_SESSION['Department']);
		}
		print "<Td>Плановый фонд времени:";
		print "<Tr>\n";
		print "<Td>";
		print "Сотрудник: ";
		print "{$_SESSION['NameSelectedEmployee']}";
		print "<Td>Плановый объем трудозатрат:";
		print "</Table>\n";
		print "<br /><br />";
		//таблица HTML
		print "<table  class=\"f-table-zebra\">\n";
		print "<caption>Объектная карта</caption>\n";
        print "<thead>\n";
		print "<TR>\n";
		print "<TH rowspan=2>Дата</TH>\n";
		print "<TH rowspan=2>Номер объекта</TH>\n";
		print "<TH rowspan=2>Наименование объекта</TH>\n";
		print "<TH rowspan=2>Номер чертежа</TH>\n";
		print "<TH rowspan=2>Марка</TH>\n";
		print "<TH colspan = 3>Графическая часть</TH>\n";
		print "<TH colspan = 6>Текстовая часть</TH>\n";
		print "<TH rowspan = 2>К-т заполн листа</TH>\n";
		print "<TH rowspan = 2>Сбор данных</TH>\n";
		print "<TH rowspan=2>Согл.-ие</TH>\n";
		if (($_SESSION['Name_Post'] == "Экономист") OR
		($_SESSION['Name_Post'] == "Начальник управления") OR
		($_SESSION['Name_Post'] == "Начальник отдела") OR
		($_SESSION['Name_Post'] == "Зам.начальника отдела") OR
		($_SESSION['Name_Post'] == "Начальник бюро") OR
		($_SESSION['Name_Post'] == "Администратор"))
		{
			print "<TH rowspan=2>Проверка</TH>\n";
		}
		print "<TH rowspan=2>Итого часов <br />(табель)</TH>\n";
		print "<TH rowspan=2>Итого чел.-час <br />(трудозатраты)</TH>\n";
		print "</TR>\n";
		print "<TR>\n";
		print "<TD>Лист А1</TD>\n";
		print "<TD>Факт. время</TD>\n";
		print "<TD>Труд.-ты</TD>\n";
		print "<TD>Лист А3</TD>\n";
		print "<TD>Факт. время</TD>\n";
		print "<TD>Труд.-ты</TD>\n";
		print "<TD>Лист А4</TD>\n";
		print "<TD>Факт. время</TD>\n";
		print "<TD>Труд.-ты</TD>\n";
		print "</TR>\n";
        print "</thead>\n";
		$Sheet_A1 = 0;
		$Time1 = 0;
		$Sheet_A3 = 0;
		$Time3 = 0;
		$Sheet_A4 = 0;
		$Time4 = 0;
		$Time_Collection = 0;
		$Time_Agreement = 0;
		$prov = 0;
		$Time = 0;
		$ManHourSum = 0;
		while ( $row = mysql_fetch_array($dbResult,MYSQL_BOTH) )
		{
			print "<TR align=center>\n";
			print "<TD>".strftime("%d.%m.%y",$row['Date']+ $_SESSION['localTime'] * 3600)."</TD>\n";
			printZeroTD($row['Number_Project']);
			printZeroTD($row['Name_Project']);
			printZeroTD($row['num_cher']);
			printZeroTD($row['Name_Mark']);
			printZeroTD($row['Sheet_A1']);
			printZeroTD($row['Time1']);
			printZeroTD($row['Man_Hour1']);
			printZeroTD($row['Sheet_A3']);
			printZeroTD($row['Time3']);
			printZeroTD($row['Man_Hour3']);
			printZeroTD($row['Sheet_A4']);
			printZeroTD($row['Time4']);
			printZeroTD($row['Man_Hour4']);
			printZeroTD($row['k_Sheet']);
			printZeroTD($row['Time_Collection']);
			printZeroTD($row['Time_Agreement']);
			if ( ($_SESSION['Name_Post'] == "Экономист") OR
			($_SESSION['Name_Post'] == "Начальник управления") OR
			($_SESSION['Name_Post'] == "Начальник отдела") OR
			($_SESSION['Name_Post'] == "Зам.начальника отдела") OR
			($_SESSION['Name_Post'] == "Начальник бюро") OR
			($_SESSION['Name_Post'] == "Администратор") )
			{
				printZeroTD($row['prov']);
			}
			printZeroTD($row['Time']);
			printZeroTD(round($row['Man_Hour_Sum'],2));
			$Sheet_A1 = $Sheet_A1 + $row['Sheet_A1'];
			$Sheet_A3 = $Sheet_A3 + $row['Sheet_A3'];
			$Sheet_A4 = $Sheet_A4 + $row['Sheet_A4'];
			$Time1 = $Time1 + $row['Time1'];
			$Time3 = $Time3 + $row['Time3'];
			$Time4 = $Time4 + $row['Time4'];
			$Time_Collection = $Time_Collection + $row['Time_Collection'];
			$Time_Agreement = $Time_Agreement + $row['Time_Agreement'];
			$prov = $prov + $row['prov'];
			$Time = $Time + $row['Time'];
			$ManHourSum = $ManHourSum + $row['Man_Hour_Sum'];
		}
		print "<TR align=center>\n";
		print "<TH colspan=5 align=right>Итого:</TH>";
		printZeroTH(round($Sheet_A1, 1));
		printZeroTH(round($Time1, 1));
		print "<TH>&nbsp</TH>\n";
		printZeroTH(round($Sheet_A3, 1));
		printZeroTH(round($Time3, 1));
		print "<TH>&nbsp</TH>\n";
		printZeroTH(round($Sheet_A4, 1));
		printZeroTH(round($Time4, 1));
		print "<TH>&nbsp</TH>\n";
		print "<TH>&nbsp</TH>\n";
		printZeroTH(round($Time_Collection, 1));
		printZeroTH(round($Time_Agreement, 1));
		if (($_SESSION['Name_Post'] == "Экономист") OR
		($_SESSION['Name_Post'] == "Начальник управления") OR
		($_SESSION['Name_Post'] == "Начальник отдела") OR
		($_SESSION['Name_Post'] == "Зам.начальника отдела") OR
		($_SESSION['Name_Post'] == "Начальник бюро") OR
		($_SESSION['Name_Post'] == "Администратор") )
		{
			printZeroTH(round($prov, 1));
		}
		printZeroTH(round($Time, 1));
		printZeroTH(round($ManHourSum, 1));
		print"</table>\n";
	}
}
//*************************************************************************************************************
// ViewTableSearch_All()- функция отображения рабочей таблицы для нескольких сотрудника в модуле поиска
// Входные параметры:
// Выходные параметры:
//*************************************************************************************************************

function ViewTableSearch_All()
{
	//global $Month,$Year;
	global $link;
	$Month = $_SESSION['Month'];
	$Year =  $_SESSION['Year'];
	if ( !($dbResult = mysql_query($_SESSION['Query'], $link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("{$_SESSION['Query']} ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		print "<Table  Width=\"50%\" Border=\"1\" CellSpacing=\"0\" CellPadding=\"2\" rules=\"all\">\n";
		print "<Tr>\n";
		print "<Td>";
		print "Месяц: ";
		print $Month[$_SESSION['lstMonth']];
		//---------------
		print "<Td>";
		print "Год ";
		print $Year[$_SESSION['lstYear']];
		print "<Tr>\n";
		print "<Td>Отдел: ";
		if ( $_SESSION['Department'] != "Выбор отдела" AND
		$_SESSION['Office'] != "Выбор бюро")
		{
			print getNameDepartment($_SESSION['Department'])."-".getNameOffice($_SESSION['Office']);
		}
		elseif ( $_SESSION['Department'] != "Выбор отдела" )
		{
			print getNameDepartment($_SESSION['Department']);
		}
		print "<Td>Плановый фонд времени:";
		print "<Tr>\n";
		print "<Td>Сотрудник:";
		print "<Td>Плановый объем трудозатрат:";
		print "</Table>\n";
		print "<br /><br />";
		//таблица HTML
		print "<TABLE  class=\"f-table-zebra\">\n";
		print "<caption>Объектная карта</caption>\n";
        print "<thead>";
		print "<TR>\n";
		print "<TH rowspan=2>Дата</TH>\n";
		print "<TH rowspan=2>Сотрудник</TH>\n";
		if ($_SESSION['Name_Post'] == "Администратор" or $_SESSION['Name_Post']=="Экономист")
		{
			print "<TH rowspan=2>Должность</TH>\n";	
		}
		print "<TH rowspan=2>Номер объекта</TH>\n";
		print "<TH rowspan=2>Наименование объекта</TH>\n";
		print "<TH rowspan=2>Номер чертежа</TH>\n";
		print "<TH rowspan=2>Марка</TH>\n";
		print "<TH colspan = 3>Графическая часть</TH>\n";
		print "<TH colspan = 6>Текстовая часть</TH>\n";
		print "<TH rowspan = 2>К-т заполн листа</TH>\n";
		print "<TH rowspan = 2>Сбор данных</TH>\n";
		print "<TH rowspan=2>Согл.-ие</TH>\n";
		if (($_SESSION['Name_Post'] == "Экономист") OR
		($_SESSION['Name_Post'] == "Начальник управления") OR
		($_SESSION['Name_Post'] == "Начальник отдела") OR
		($_SESSION['Name_Post'] == "Зам.начальника отдела") OR
		($_SESSION['Name_Post'] == "Начальник бюро") OR
		($_SESSION['Name_Post'] == "Администратор"))
		{
			print "<TH rowspan=2>Проверка</TH>\n";
		}
		print "<TH rowspan=2>Итого часов <br />(табель)</TH>\n";
		print "<TH rowspan=2>Итого чел.-час <br />(трудозатраты)</TH>\n";
		print "</TR>\n";
		print "<TR  align=center>\n";
		print "<TD>Лист А1</TD>\n";
		print "<TD>Факт. время</TD>\n";
		print "<TD>Труд.-ты</TD>\n";
		print "<TD>Лист А3</TD>\n";
		print "<TD>Факт. время</TD>\n";
		print "<TD>Труд.-ты</TD>\n";
		print "<TD>Лист А4</TD>\n";
		print "<TD>Факт. время</TD>\n";
		print "<TD>Труд.-ты</TD>\n";
		print "</TR>\n";
        print "</thead>";
		$Sheet_A1 = 0;
		$Time1 = 0;
		$Sheet_A3 = 0;
		$Time3 = 0;
		$Sheet_A4 = 0;
		$Time4 = 0;
		$Time_Collection = 0;
		$Time_Agreement = 0;
		$prov = 0;
		$Time = 0;
		$ManHourSum = 0;
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			print "<TR align=center>\n";
			print "<TD>".strftime("%d.%m.%y",$row['Date']+ $_SESSION['localTime'] * 3600)."</TD>\n";
			print "<TD nowrap align=left>{$row['Family']} ".mb_substr($row['Name'],0,1,'utf8').".".	mb_substr($row['Patronymic'],0,1,'utf8').".</TD>";
			if ($_SESSION['Name_Post'] == "Администратор" or $_SESSION['Name_Post']=="Экономист")
			{
				printZeroTD($row['Name_Post']);
			}			
			
			printZeroTD($row['Number_Project']);
			printZeroTD($row['Name_Project']);
			printZeroTD($row['num_cher']);
			printZeroTD($row['Name_Mark']);
			printZeroTD($row['Sheet_A1']);
			printZeroTD($row['Time1']);
			printZeroTD($row['Man_Hour1']);
			printZeroTD($row['Sheet_A3']);
			printZeroTD($row['Time3']);
			printZeroTD($row['Man_Hour3']);
			printZeroTD($row['Sheet_A4']);
			printZeroTD($row['Time4']);
			printZeroTD($row['Man_Hour4']);
			printZeroTD($row['k_Sheet']);
			printZeroTD($row['Time_Collection']);
			printZeroTD($row['Time_Agreement']);
			if (($_SESSION['Name_Post'] == "Экономист") OR
			($_SESSION['Name_Post'] == "Начальник управления") OR
			($_SESSION['Name_Post'] == "Начальник отдела") OR
			($_SESSION['Name_Post'] == "Зам.начальника отдела") OR
			($_SESSION['Name_Post'] == "Начальник бюро") OR
			($_SESSION['Name_Post'] == "Администратор"))
			{
				printZeroTD($row['prov']);
			}
			printZeroTD($row['Time']);
			printZeroTD(round($row['Man_Hour_Sum'],2));
			$Sheet_A1 = $Sheet_A1 + $row['Sheet_A1'];
			$Sheet_A3 = $Sheet_A3 + $row['Sheet_A3'];
			$Sheet_A4 = $Sheet_A4 + $row['Sheet_A4'];
			$Time1 = $Time1 + $row['Time1'];
			$Time3 = $Time3 + $row['Time3'];
			$Time4 = $Time4 + $row['Time4'];
			$Time_Collection = $Time_Collection + $row['Time_Collection'];
			$Time_Agreement = $Time_Agreement + $row['Time_Agreement'];
			$prov = $prov + $row['prov'];
			$Time = $Time + $row['Time'];
			$ManHourSum = $ManHourSum + $row['Man_Hour_Sum'];
		}
		print "<TR align=center>\n";
		if ($_SESSION['Name_Post'] == "Администратор" or $_SESSION['Name_Post']=="Экономист")
		{
			print "<TH colspan=7 align=right>Итого:</TH>\n";
		} else
		{
			print "<TH colspan=6 align=right>Итого:</TH>\n";
		}
		printZeroTH(round($Sheet_A1, 1));
		printZeroTH(round($Time1, 1));
		print("<TH>&nbsp</TH>\n");
		printZeroTH(round($Sheet_A3, 1));
		printZeroTH(round($Time3, 1));
		print "<TH>&nbsp</TH>\n";
		printZeroTH(round($Sheet_A4, 1));
		printZeroTH(round($Time4, 1));
		print "<TH>&nbsp</TH>\n";
		print "<TH>&nbsp</TH>\n";
		printZeroTH(round($Time_Collection, 1));
		printZeroTH(round($Time_Agreement, 1));
		if (($_SESSION['Name_Post'] == "Экономист") OR
		($_SESSION['Name_Post'] == "Начальник управления") OR
		($_SESSION['Name_Post'] == "Начальник отдела") OR
		($_SESSION['Name_Post'] == "Зам.начальника отдела") OR
		($_SESSION['Name_Post'] == "Начальник бюро") OR
		($_SESSION['Name_Post'] == "Администратор"))
		{
			printZeroTH(round($prov, 1));
		}
		printZeroTH(round($Time, 1));
		printZeroTH(round($ManHourSum, 1));
		print "</table>\n";
	}
}
//*************************************************************************************************************
// DateAdd($interval, $number, $date)- функция добавления даты
// Входные параметры:
//$interval-интервал
/*
yyyy- год
q- четверть
m- месяц
y-  день года
d- день
w- день недели
ww- неделя года
h- час
n- минута
s- секунда
*/
//$number-добавляемое значение
//$date-заданное время(в сек)
	// Выходные параметры:
//*************************************************************************************************************

function DateAdd($interval, $number, $date)
{
	$date_time_array = getdate($date);
	$hours = $date_time_array['hours'];
	$minutes = $date_time_array['minutes'];
	$seconds = $date_time_array['seconds'];
	$month = $date_time_array['mon'];
	$day = $date_time_array['mday'];
	$year = $date_time_array['year'];
	switch ($interval)
	{
		case 'yyyy':
		$year+=$number;
		break;
		case 'q':
		$year+=($number*3);
		break;
		case 'm':
		$month+=$number;
		break;
		case 'y':
		case 'd':
		case 'w':
		$day+=$number;
		break;
		case 'ww':
		$day+=($number*7);
		break;
		case 'h':
		$hours+=$number;
		break;
		case 'n':
		$minutes+=$number;
		break;
		case 's':
		$seconds+=$number;
		break;
	}
	$timestamp = mktime($hours, $minutes, $seconds, $month, $day, $year);
	return $timestamp;
}
//*************************************************************************************************************
// getArrayTimeYear($lstYear)- функция получения массива значений месяцев в сек заданного года
// Входные параметры: $lstYear-год
// Выходные параметры:
//*************************************************************************************************************

function getArrayTimeYear($lstYear, $period = "26-25")
{
	if ($period == "26-25")
	{
		for ($i = 1; $i < 14; $i++)
		{
			//исправил 1 на 26(начало отчетного периода)  и $i-1
			$a[$i] = mktime(0, 0, 0, $i-1, 26, $lstYear);
		}
		} elseif ($period == "1-31")
	{
		for ($i = 1; $i < 14; $i++)
		{
			//отчетный период с 1 января
			$a[$i] = mktime(0, 0, 0, $i, 1, $lstYear);
		}
	}
	return $a;
}
//******************************************************************************
//startTime()-функция отладки
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function startTime()
{
	return $tstart = microtime(1);
}
//******************************************************************************
//endTime($tstart)-функция отладки
//Входные параметры:
//$tstart-начальное время
//Выходные параметры:
//******************************************************************************

function endTime($tstart)
{
	$tend = microtime(1);
	$total = $tend-$tstart;
	print "На обработку запроса затрачено ".round($total,2)."  секунд! <br />\n";
}
//******************************************************************************
//printLstMonth()-функция печати раскрывающегося списка месяцев
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function printLstMonth()
{
	//global $Month;
	$Month = $_SESSION['Month'];
	$str = "<select class= \"g-2\" name=\"lstMonth\">\n";
	$dateMonth = date("m", time());
	$str .= "<option>Выберите месяц</option>\n";
	//заполнение списка месяцев
	foreach($Month as $key=>$value)
	{
		$str .= "<option value=\"$key\"";
		if ($key==$dateMonth)
		{
			$str .= " selected";
		}
		$str .= ">$value</option>\n";
	}
	$str .= "</select>\n";
	print($str);
}
//******************************************************************************
//printLstYear()-функция печати раскрывающегося списка лет
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function printLstYear()
{
	//global $Year;
	// $_SESSION['Year'] представлен в виде массива ("2008"=>2008, "2009"=>2009 ....)
	$Year =  $_SESSION['Year'];
	$str = "<select class= \"g-2\" name=\"lstYear\">\n";
	$dateYear = date("Y",time());
	$str .= "<option>Выберите год</option>\n";
	//заполнение списка месяцев
	foreach($Year as $key=>$value)
	{
		$str .= "<option value=\"$key\"";
		if ($key==$dateYear)
		{
			$str .= " selected";
		}
		$str .= ">$value</option>\n";
	}
	$str .= "</select>\n";
	print($str);
}
//******************************************************************************
//getYearFromId()-получить год по его ид
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function getYearFromId($id)
{
	global $link;
	$Query = "SELECT Year FROM year WHERE id=".escapeshellarg($id);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		if (mysql_num_rows($dbResult)>0)
		{
			$row = mysql_fetch_array($dbResult, MYSQL_BOTH);
			return $row['Year'];
		}
	}
}
//******************************************************************************
//getMonthFromId()-получить месяц по его ид
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function getMonthFromId($id)
{
	global $link;
	$Query = "SELECT Month FROM month WHERE id=".escapeshellarg($id);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		if (mysql_num_rows($dbResult)>0)
		{
			$row = mysql_fetch_array($dbResult, MYSQL_BOTH);
			return $row['Month'];
		}
	}
}
//******************************************************************************
// printZeroTD($Data)-вывод информации с проверкой на пустоту
//Входные параметры: $Data- входящие данные
//Выходные параметры:
//******************************************************************************

function printZeroTD()
{
	$Data = func_get_args(); //определяем данные
	$i = func_num_args(); //определяем количество параметров
	switch ($i)
	{
		case 0:
		print "<td>&nbsp;</td>\n";
		break;
		case 1:
		if (is_numeric($Data[0]))
		{
			if ($Data[0] == 0)
			{
				print "<td>&nbsp;</td>\n";
			}
			else {
				print "<td>".str_replace('.',',',$Data[0])."</td>\n";
			}
		}
		else {
			if (empty($Data[0]))
			{
				print "<td>&nbsp;</td>\n";
			}
			else {
				print "<td>".$Data[0]."</td>\n";
			}
		}
		break;
		case 2:
		if (is_numeric($Data[0]))
		{
			if ($Data[0] == 0)
			{
				print "<td>&nbsp;</td>\n";
			}
			else {
				print "<td style='color:{$Data[1]}'>".str_replace('.',',',$Data[0])."</td>\n";
			}
		}
		else {
			if (empty($Data[0]))
			{
				print "<td>&nbsp;</td>\n";
			}
			else {
				print "<td style='color:{$Data[1]}'>".str_replace('.',',',$Data[0])."</td>\n";
			}
		}
		break;
		case 3:
		if (is_numeric($Data[0]))
		{
			if ($Data[0] == 0)
			{
				print "<td>&nbsp;</td>\n";
			}
			else {
				print "<td style='color:{$Data[1]}'>".str_replace('.',',',$Data[0])."{$Data[2]}</td>\n";
			}
		}
		else {
			if (empty($Data[0]))
			{
				print "<td>&nbsp;</td>\n";
			}
			else {
				print "<td style='color:{$Data[1]}'>".str_replace('.',',',$Data[0])."{$Data[2]}</td>\n";
			}
		}
		break;
	}
}
//******************************************************************************
// printZeroTH($Data)-вывод информации с проверкой на пустоту
//Входные параметры: $Data- входящие данные
//Выходные параметры:
//******************************************************************************

function printZeroTH()
{
	$Data = func_get_args(); //определяем данные
	$i = func_num_args(); //определяем количество параметров
	switch ($i)
	{
		case 0:
		print "<th>&nbsp;</th>\n";
		break;
		case 1:
		if (is_numeric($Data[0]))
		{
			if ($Data[0] == 0)
			{
				print "<th>&nbsp;</th>\n";
			}
			else {
				print "<th>".str_replace('.',',',$Data[0])."</th>\n";
			}
		}
		else {
			if (empty($Data[0]))
			{
				print "<th>&nbsp;</th>\n";
			}
			else {
				print "<th>".$Data[0]."</th>\n";
			}
		}
		break;
		case 2:
		if (is_numeric($Data[0]))
		{
			if ($Data[0] == 0)
			{
				print "<th>&nbsp;</th>\n";
			}
			else {
				print "<th style='color:{$Data[1]}'>".str_replace('.',',',$Data[0])."</th>\n";
			}
		}
		else {
			if (empty($Data[0]))
			{
				print "<th>&nbsp;</th>\n";
			}
			else {
				print "<th style='color:{$Data[1]}'>".str_replace('.',',',$Data[0])."</th>\n";
			}
		}
		break;
		case 3:
		if (is_numeric($Data[0]))
		{
			if ($Data[0] == 0)
			{
				print "<th>&nbsp;</th>\n";
			}
			else {
				print "<th style='color:{$Data[1]}'>".str_replace('.',',',$Data[0])."{$Data[2]}</th>\n";
			}
		}
		else {
			if (empty($Data[0]))
			{
				print "<th>&nbsp;</th>\n";
			}
			else {
				print "<th style='color:{$Data[1]}'>".str_replace('.',',',$Data[0])."{$Data[2]}</th>\n";
			}
		}
		break;
	}
}
//******************************************************************************
// bodyProjectOutput() -тело отчета ProjectOutput (Утвержденные труд-ты)
	//Входные параметры:
//Выходные параметры:
//******************************************************************************

function bodyProjectOutput($selectStatus)
{
	global $link;
	//инициализация массива времени
	//заполнение массива "отдел-месяц" нулями
	$array_timeReport = InitializationMatrixCard();

	//заполнение массива "месяцы" нулями
	$arraySum = InitializationSumCard(13);
	$array_planTime = InitializationSumCard(13);
	print "<Table  Width=\"100%\" Border=\"1\" CellSpacing=\"0\" CellPadding=\"2\">\n";
	print "<Tr>\n";
	print "<Td>";
	print "Объект:&nbsp;";
	if( $_SESSION['lstObject'] == "Выбор объекта" )
	{
		print "Все объекты";
	}
	else {
		print getNameProject($_SESSION['lstObject']);
	}
	print "<Td>";
	print "Заказчик:&nbsp;";
	if ( $_SESSION['lstCustomer'] == "Выбор заказчика" )
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
	if ( $_SESSION['lstObject'] == "Выбор объекта" )
	{
		print "&nbsp;";
	}
	else {
		print "<U>".getNameExtProject($_SESSION['lstObject'])."</U>";
	}
	print "<Tr>\n";
	print "<Td colspan=2>";
	print "Стадия&nbsp;";
	print "<Td>";
	print "Начало&nbsp;";
	print "<Td>";
	print "Окончание&nbsp;";
	print "</Table>\n";
	print "<br /><br />";
	//составляем объединение результатов работы нескольких команд SELECT в один набор результатов.
	//таблица HTML
	print "<TABLE Width=\"100%\" Border=\"2\" CellSpacing=\"0\" CellPadding=\"2\">\n";
	print "<TR>\n";
	print "<TH colspan=3 rowspan=2>Наименование</TH>".
	"<TH colspan=12>Месяцы</TH>".
	"<TH rowspan=2>Итого,чел./часов</TH>".
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
	for ($i = 1; $i < 13; $i++)
	{
		$Query1 = "SELECT department.Name_Department, design.statusBVP, design.approvalBVP, SUM(design.Man_Hour_Sum) AS Man_Hour_Sum ".
		"FROM design,employee,department,project ".
		"WHERE ".
		"((design.id_Employee = employee.Id) AND ".
		"(department.Id = employee.id_Department) AND ".
		"(design.id_Project = project.Id) AND ".
		"(department.id <> 22) AND (department.id <> 24) AND (department.id <> 25) AND ". //кроме Руководства, ИТ, ТЭОиСДР, ГД
		"(Man_Hour_Sum>".escapeshellarg('0').") AND "; //сумма работ должна быть больше нуля
		if ($selectStatus == "approvalBVP")
		{
			$Query3 = "(design.approvalBVP>=".escapeshellarg($_SESSION['arrayMonth'][$i]).
			") AND (design.approvalBVP<".escapeshellarg($_SESSION['arrayMonth'][$i+1]).")) ".
			" GROUP BY department.id ";
		}
		elseif($selectStatus == "statusBVP")
		{
			$Query3 = "(design.statusBVP>=".escapeshellarg($_SESSION['arrayMonth'][$i]).
			") AND (design.statusBVP<".escapeshellarg($_SESSION['arrayMonth'][$i+1]).")) ".
			" GROUP BY department.id ";
		}
		if ( $_SESSION['lstObject'] != "Выбор объекта" )
		{
			$Query2 = "(project.Id={$_SESSION['lstObject']}) AND ";
		}
		else {
			$Query2 = "";
		}
		if ( $_SESSION['lstCustomer'] != "Выбор заказчика" )
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
			//print "Выборка $Query не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
			{
    				//записываем в массив данные по каждому месяцу- округлим до 2 знака после запятой
    				$array_timeReport[$row['Name_Department']][$i] = round($row['Man_Hour_Sum'],0);

                    if($selectStatus == "statusBVP" AND $_SESSION['lstObject'] == "Выбор объекта")
                    {
                        $arrArchive = getHourFromArchive($_SESSION['lstYear'], $i);
                        foreach($arrArchive as $name=>$value)
                        {
                            $array_timeReport[$name][$i] = $value;
                        }
                    }

			}

		}
	}


	//заполнение таблицы
	//$array_timeReport - массив значений размеров Кол-во отделов х Кол-во месяцев (12)
		// $Name - название отдела
	// $Year - массив месяцев (12)
		// $Month - значение в определенном месяце
	// $arraySum - массив  предназначенный для хранения значений всех отделов  по всем месяцам  (ИТОГО)
		foreach($array_timeReport as $Name=>$Year)
	{
		print "<TR align=center>\n";
		print"<TD colspan=3>$Name</TD>";

        if($selectStatus == "statusBVP")
        {
            for ($key = 1; $key < 13; $key++)
            {
                printZeroTD($Year[$key]);
    		    $arraySum[$key] = $Year[$key] + $arraySum[$key];
            }
    		//суммируем значения по отдельному отделу за весь год 1-12 месяцев
    		$sum = array_sum($Year);
    		printZeroTH($sum);
    		$arraySum[$key] = $sum + $arraySum[$key];
        } elseif($selectStatus == "approvalBVP")
        {
    		foreach($Year as $key=>$Month)
    		{
    			printZeroTD($Month);
    			$arraySum[$key] = $Month + $arraySum[$key];
    		}
		//суммируем значения по отдельному отделу за весь год 1-12 месяцев
		$sum = array_sum($Year);
		printZeroTH($sum);
		$arraySum[$key+1] = $sum + $arraySum[$key+1];
        }

	}
	print "<TR align=center>\n";
	print "<TD colspan=3><b>Итого</b></TD>";
	foreach ( $arraySum as $key=>$SumMonth )
	{
		printZeroTH($SumMonth);
	}
	print "</TABLE>\n";
	$_SESSION['graphData'] = array_slice($arraySum, 0, count($arraySum) - 1);
	//определяем массив из планового времени всех отделов
	$QueryPlanTime = " SELECT SUM(PlanDepartment) AS Plan FROM plan_time ".
	"WHERE ".
	"id_Year = (SELECT id FROM year WHERE Year=".escapeshellarg($_SESSION['lstYear']).")".
	" GROUP BY id_Month";
	if ( !($dbResultPlan = mysql_query($QueryPlanTime, $link)) )
	{
		//print "Выборка $QueryPlanTime не удалась!\n".mysql_error();
		processingError("$QueryPlanTime ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		$i = 1;
		while ( $rowPlan = mysql_fetch_array($dbResultPlan, MYSQL_BOTH) )
		{
			//записываем в массив данные по каждому месяцу- округлим до 2 знака после запятой
			$array_planTime[$i++] = round($rowPlan['Plan'],0);
		}
	}
	$_SESSION['planTime'] = array_slice($array_planTime, 0, count($array_planTime) - 1);
}

//******************************************************************************
// getHourFromArchive($lstYear, $month)   -  находим занесенное время по неосновным отделам, которые присутствуют в плановой табл.
//Входные параметры: $lstYear - год  $month - месяц
//Выходные параметры: $res - массив array("Архив"=> 100, "БВП"=>200)
//******************************************************************************
function getHourFromArchive($lstYear, $month)
{
global $link;
$res = array();
  $query = "select Name_Department, PlanDepartment
            from plan_time, department
            where (id_Department=department.id) and
            id_Year=".escapeshellarg(getIdYear($lstYear)).
            " and id_Month=".escapeshellarg($month).
            " and Primary_Department='FALSE'".  //кроме  основных отделов
            " order by Name_Department";

	if ( !($dbResult = mysql_query($query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
            $res[$row['Name_Department']] = $row['PlanDepartment'];
		}

	}
    return $res;
}
//******************************************************************************
// bodyReportAn() -   тело отчета ReportA1,ReportA4
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function bodyReportAn($Format)
{
	global $link;
	switch ($Format)
	{
		case "ReportA1":
		$QueryFormat = " SUM(design.Sheet_A1) AS sumSheet_A1 ";
		$Sheet =  "sumSheet_A1";
		break;
		case "ReportA4":
		$QueryFormat = " SUM(design.Sheet_A4+2*design.Sheet_A3) AS sumSheet_A4 ";
		$Sheet =  "sumSheet_A4";
		break;
	}
	//инициализация массива времени
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
	print "Заказчик:&nbsp;";
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
	if( $_SESSION['lstObject'] == "Выбор объекта" )
	{
		print "";
	}
	else {
		print "<U>".getNameExtProject($_SESSION['lstObject'])."</U>";
	}
	print "<Tr>\n";
	print "<Td colspan=2>";
	print "Стадия&nbsp;";
	print "<Td>";
	print "Начало&nbsp;";
	print "<Td>";
	print "Окончание&nbsp;";
	print "</Table>\n";
	print "<br /><br />";
	//$Comma_separated = implode(",", $_SESSION['arrayMonth']);
	//print($_SESSION['arrayMonth'][1]);
	//составляем объединение результатов работы нескольких команд SELECT в один набор результатов.
	//таблица HTML
	print "<TABLE Width=\"100%\" Border=\"3\" CellSpacing=\"0\" CellPadding=\"2\">\n";
	print "<TR>\n";
	print "<TH colspan=3 rowspan=2>Наименование</TH>".
	"<TH colspan=12>Месяцы</TH>".
	"<TH rowspan=2>Итого, листов</TH>".
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
	for ($i = 1; $i < 13; $i++)
	{
		$Query1 = "SELECT department.Name_Department, ".$QueryFormat.
		"FROM design,employee,department,project ".
		"WHERE ".
		"((design.id_Employee = employee.Id) AND ".
		"(department.Id = employee.id_Department) AND ".
		"(department.Id <> 22) AND (department.Id <> 24) AND (department.Id <> 25) AND (department.Id <> 26) AND (department.Id <> 19) AND ".   //кроме Руководства, ИТ, ТЭОиСДР, ГД
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
			//print "Выборка не удалась!\n";
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
			{
				$array_timeReport[$row['Name_Department']][$i] = round($row[$Sheet],1);
			}
		}
	}
	//заполнение таблицы
	foreach( $array_timeReport as $Name=>$Year )
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
	foreach( $arraySum as $key=>$SumMonth )
	{
		printZeroTH($SumMonth);
	}
	print "</TABLE>\n";
	$_SESSION['graphData'] = array_slice($arraySum, 0, count($arraySum) - 1);
}
//******************************************************************************
// bodyProjectOutputFormat($Format) -тело отчета ProjectOutputA1, ProjectOutputA4
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function bodyProjectOutputFormat($Format)
{
	global $link;
	switch ($Format)
	{
		case "OutputA1":
		$QueryFormat = " SUM(design.Sheet_A1) AS sumSheet_A1 ";
		$Sheet =  "sumSheet_A1";
		break;
		case "OutputA4":
		$QueryFormat = " SUM(design.Sheet_A4+2*design.Sheet_A3) AS sumSheet_A4 ";
		$Sheet =  "sumSheet_A4";
		break;
	}
	//инициализация массива времени
	$array_timeReport = InitializationMatrixCard();
	$arraySum = InitializationSumCard(13);
	print "<Table  Width=\"100%\" Border=\"1\" CellSpacing=\"0\" CellPadding=\"2\">\n";
	print "<Tr>\n";
	print "<Td>";
	print "Объект:&nbsp;";
	if ( $_SESSION['lstObject'] == "Выбор объекта" )
	{
		print "Все объекты";
	}
	else {
		print getNameProject($_SESSION['lstObject']);
	}
	print "<Td>";
	print "Заказчик:&nbsp;";
	if ( $_SESSION['lstCustomer'] == "Выбор заказчика" )
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
	if ( $_SESSION['lstObject'] == "Выбор объекта" )
	{
		print "";
	}
	else {
		print "<U>".getNameExtProject($_SESSION['lstObject'])."</U>";
	}
	print "<Tr>\n";
	print "<Td colspan=2>";
	print "Стадия&nbsp;";
	print "<Td>";
	print "Начало&nbsp;";
	print "<Td>";
	print "Окончание&nbsp;";
	print "</Table>\n";
	print "<p></p>  ";
	//составляем объединение результатов работы нескольких команд SELECT в один набор результатов.
	//таблица HTML
	print "<TABLE Width=\"100%\" Border=\"3\" CellSpacing=\"0\" CellPadding=\"2\">\n";
	print "<TR>\n";
	print "<TH colspan=3 rowspan=2>Наименование</TH>".
	"<TH colspan=12>Месяцы</TH>".
	"<TH rowspan=2>Итого, листов</TH>".
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
		$Query1 = "SELECT department.Name_Department, design.statusBVP, ".$QueryFormat.
		"FROM design,employee,department,project ".
		"WHERE ".
		"((design.id_Employee = employee.Id) AND ".
		"(department.Id = employee.id_Department) AND ".
		"(department.Id <> 22) AND (department.Id <> 24) AND (department.Id <> 25) AND (department.Id <> 26) AND (department.Id <> 19) AND ".
		"(design.id_Project = project.Id) AND ";
		$Query3 = "(design.statusBVP>=".escapeshellarg($_SESSION['arrayMonth'][$i]).
		") AND (design.statusBVP<".escapeshellarg($_SESSION['arrayMonth'][$i+1]).")) ".
		" GROUP BY department.id ";
		if ( $_SESSION['lstObject'] != "Выбор объекта")
		{
			$Query2 = "(project.Id={$_SESSION['lstObject']}) AND ";
		}
		else {
			$Query2 = "";
		}
		if ( $_SESSION['lstCustomer'] != "Выбор заказчика" )
		{
			$Query4 = "(project.Id_Customer={$_SESSION['lstCustomer']}) AND ";
		}
		else {
			$Query4 = "";
		}
		//результирующий запрос
		$Query = $Query1.$Query2.$Query4.$Query3;
		//file_put_contents("query.txt",$Query);
		//print($Query);
		if ( !($dbResult = mysql_query($Query,$link)) )
		{
			//print "Выборка $Query не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else  {
			while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
			{
				$array_timeReport[$row['Name_Department']][$i] = round($row[$Sheet],1);
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
}
//******************************************************************************
// cells1() -панель ввода данных для отчета Сводные данные за год по отделам
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function cells1()
{
	print "<div class=\"report1\">\n";
	print "<form action=\"selectReport.php\" method=\"post\">\n";
	print "<h4 class=\"legend\">Сводные данные за год по отделам</h4>";
	print "<ul style=\"list-style: none; padding: 0\">\n";
	print "<li><input name=\"rbReport\" type=\"radio\" value=\"time\">Карта затрат фактического времени\n";
	print "<li><input name=\"rbReport\" type=\"radio\" value=\"man_hour\">Общие трудозатраты\n";
	print "<li><input name=\"rbReport\" type=\"radio\" value=\"sheet_a1\">Карта расчета затрат листов А1\n";
	print "<li><input name=\"rbReport\" type=\"radio\" value=\"sheet_a4\">Карта расчета затрат листов А4\n";
	print "<li><input name=\"rbReport\" type=\"radio\" value=\"project_output\" checked>Утвержденные трудозатраты\n";
	print "<li><input name=\"rbReport\" type=\"radio\" value=\"project_outputA1\">Выдача проектов в БВП (листы А1)\n";
	print "<li><input name=\"rbReport\" type=\"radio\" value=\"project_outputA4\">Выдача проектов в БВП (листы А4)\n";
	print "<li><input name=\"rbReport\" type=\"radio\" value=\"project_output_allocation\">Расчет коэф-та распределения по работам\n";
	print "<li><input name=\"rbReport\" type=\"radio\" value=\"project_output_archive\">Архивные трудозатраты\n";
	print "    </ul> ";
	print " <br />\n";
	print "<FIELDSET>\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"1-31\">календарный период\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"26-25\" checked=\"checked\">отчетный период";
	print "</FIELDSET>\n";
	print " <br />\n";
	print "<Table   Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\">\n";
	print "<Tr>\n";
	print "<Td>Введите заказчика\n";
	print "<Td>";
	print "<select size=\"1\" class=\"selectCustomer\" name=\"lstCustomer\">\n";
	print "<option>Выбор заказчика</option>\n";
	//заполнение списка заказчиков
	getArrayNameCustomer();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Введите объект\n";
	print "<Td>";
	print "<select size=\"1\" class=\"size2\" name=\"lstObject\">\n";
	print "<option>Выбор объекта</option>\n";
	//заполнение списка объектов
	getArrayProject();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Выберите год\n";
	print "<Td>";
	//печатаем список лет
	printLstYear();
	print "<Tr>\n";
	print "<Td colspan=2 align=center><input type=\"submit\" name=\"getData1\" value=\"Получить данные\"> \n";
	print "</Table>\n";
	print "</form>";
	print "</div>\n";
}

//******************************************************************************
// cells11() -панель ввода данных для отчета Сравнение производительности по годам
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function cells11()
{
	print "<div class=\"report11\">\n";
	print "<form action=\"selectReport.php\" method=\"post\">\n";
	print "<h4 class=\"legend\">Сравнение производительности по годам</h4>";
	print "<ul style=\"list-style: none; padding: 0\">\n";
	print "<li><input name=\"rbReport\" type=\"radio\" value=\"man_hour\">Общие трудозатраты\n";
	print "<li><input name=\"rbReport\" type=\"radio\" value=\"project_output\" checked>Утвержденные трудозатраты\n";
	print "<li><input name=\"rbReport\" type=\"radio\" value=\"project_outputA1\">Выдача проектов в БВП (листы А1)\n";
	print "<li><input name=\"rbReport\" type=\"radio\" value=\"project_outputA4\">Выдача проектов в БВП (листы А4)\n";
	print "</ul> ";
	print "<br />\n";
	print "<FIELDSET>\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"1-31\">календарный период\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"26-25\" checked=\"checked\">отчетный период";
	print "</FIELDSET>\n";
	print " <br />\n";

	print "<FIELDSET>\n";
	print "<legend>Стиль</legend>";
	print "<input type=\"radio\" name=\"rbShema\" value=\"default\" checked>Обычный\n";
	print "<br/><input type=\"radio\" name=\"rbShema\" value=\"grid\">Сетка\n";
	print "<br/><input type=\"radio\" name=\"rbShema\" value=\"gray\">Серый\n";
	print "<br/><input type=\"radio\" name=\"rbShema\" value=\"dark_blue\" >темно-синий\n";
	print "<br/><input type=\"radio\" name=\"rbShema\" value=\"dark_green\" >темно-зеленый\n";

	print "</FIELDSET>\n";
	print " <br />\n";


	print "<Table   Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\">\n";
	print "<Td>";
  print "<ul style=\"width: 450px; height:30px;\" >";
	//печатаем список лет
	foreach($_SESSION['Year'] as $key=>$value)
	{
		//отмечаем текущий год
		if ($key == $_SESSION['CurrentYear'])
		{
			print "<li style=\"display:inline\"><input type=\"checkbox\" name=\"chkYear[]\" value=\"$value\" checked/>$value</li> ";
		}
		else {
			print "<li style=\"display:inline\"><input type=\"checkbox\" name=\"chkYear[]\" value=\"$value\" />$value</li> ";
		}
	}
 	print "</ul>";

	print "<Tr>\n";
	print "<Td align=center><input type=\"submit\" name=\"getData11\" value=\"Получить данные\"> \n";
	print "</Table>\n";
	print "</form>";
	print "</div>\n";
}

//******************************************************************************
// // cells2() -панель ввода данных для отчета Свод трудозатрат отделов
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function cells2()
{
	print "<div class=\"report2\">\n";
	print "<form action=\"selectReport.php\" method=\"post\">\n";
	print "<H4 class=\"legend\">Свод трудозатрат отделов</H4>";
	print "<Table  Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" >\n";
	print "<Tr>\n";
	print "<Td>Выберите заказчика\n";
	print "<Td>";
	print "<select size=\"1\" class=\"selectCustomer\" name=\"lstCustomer\">\n";
	print "<option>Выбор заказчика</option>\n";
	//заполнение списка заказчиков
	getArrayNameCustomer();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Выберите месяц\n";
	print "<Td>";
	//печатаем список месяцев
	printLstMonth();
	print "<Input Type=\"date\" Name=\"DateBegin\"  class=\"g-2\">\n";
	print "<Tr>\n";
	print "<Td>Выберите год\n";
	print "<Td>";
	//печатаем список лет
	printLstYear();
	print "<Input Type=\"date\" Name=\"DateEnd\" class=\"g-2\">\n";
	print "<Tr>\n";
	print "<Td colspan=2>";
	print "<FIELDSET>\n";
	print "<input name=\"rbCategry\" type=\"radio\" value=\"man_hour\" checked>Общие трудозатраты\n";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td colspan=2>";
	print "<FIELDSET>\n";
	print "<input name=\"rbCategry\" type=\"radio\" value=\"project_output\">Трудозатраты по выдаче в БВП\n";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td colspan=2>";
	print "<FIELDSET>\n";
	print "<input name=\"rbCategry\" type=\"radio\" value=\"project_output_archive\">Архивные трудозатраты\n";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td colspan=2>";
	print "<FIELDSET>\n";
	print "<input name=\"rbCategry\" type=\"radio\" value=\"time\">Фактическое затраченное время\n";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td>Расчет сметы по процентам\n";
	print "<Td>";
	print "<Input Type=\"checkbox\" Name=\"SmetaCalc\" value=\"TRUE\">\n";
	print "<Tr>\n";
	print "<Td>Процент\n";
	print "<Td>";
	print "<Input Type=\"text\" Name=\"Smeta\" class=\"size1\">\n";
	print "<Tr>\n";
	print "<Td colspan=2>\n";
	print "<FIELDSET>\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"1-31\">календарный период\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"26-25\" checked=\"checked\">отчетный период";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td colspan=2 align=center><input type=\"submit\" name=\"getData2\" value=\"Получить данные\"> \n";
	print "</Table>\n";
	print "</form>";
	print "</div>\n";
}
//******************************************************************************
// cells3() -панель ввода данных для отчета Свод трудозатрат ПУ по объектам
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function cells3()
{
	print "<div class=\"report3\">\n";
	print "<form action=\"selectReport.php\" method=\"post\">\n";
	print "<H4 class=\"legend\">Свод трудозатрат УПИРиИ по объектам</H4>";
	print "<Table  Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" >\n";
	print "<Tr>\n";
	print "<Td>Выберите заказчика\n";
	print "<Td>";
	print "<select size=\"1\" class=\"selectCustomer\" name=\"lstCustomer\">\n";
	print "<option>Выбор заказчика</option>\n";
	//заполнение списка заказчиков
	getArrayNameCustomer();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Выберите месяц\n";
	print "<Td>";
	//печатаем список месяцев
	printLstMonth();
	print "<Input Type=\"date\" Name=\"DateBegin\"  class=\"g-2\">\n";
	print "<Tr>\n";
	print "<Td>Выберите год\n";
	print "<Td>";
	//печатаем список лет
	printLstYear();
	print "<Input Type=\"date\" Name=\"DateEnd\" class=\"g-2\">\n";
	print "<Tr>\n";
	print "<Td colspan=2>\n";

  print "<FIELDSET>\n";
  print "<ul class=\"menu_input\" >";
  print "<li><input name=\"rbCategry\" type=\"radio\" value=\"man_hour\" checked>Общие трудозатраты</li>\n";
  print "<li><input name=\"rbCategry\" type=\"radio\" value=\"project_output\">Трудозатраты по выдаче в БВП</li>\n";
  print "<li><input name=\"rbCategry\" type=\"radio\" value=\"project_output_archive\">Архивные трудозатраты</li>\n";
  print "<li><input name=\"rbCategry\" type=\"radio\" value=\"time\">Фактическое затраченное время</li>\n";
  
  print "</ul>";
  print "</FIELDSET>\n";
  print "<Tr>\n";
  print "<Td colspan=2>\n";
  print "<FIELDSET>\n";
  print "<input type=\"radio\" name=\"rbPeriod\" value=\"1-31\">календарный период\n";
  print "<input type=\"radio\" name=\"rbPeriod\" value=\"26-25\" checked=\"checked\">отчетный период";
  print "</FIELDSET>\n";
  print "<Tr>\n";
  print "<Td colspan=2>\n";
  print "<FIELDSET>\n";
  print "<ul class=\"menu_input\" >";
  print "<li><b>Статус проекта</b></li>";
  print "<li><input name=\"chkstatusSearch[]\" type=\"checkbox\" value=\"OKC\" >ОКС</li>\n";
  print "<li><input name=\"chkstatusSearch[]\" type=\"checkbox\" value=\"Plan\" >Плановые</li>\n";
  print "<li><input name=\"chkstatusSearch[]\" type=\"checkbox\" value=\"OverPlan\" >Внеплановые</li>\n";
  print "<li><input name=\"chkstatusSearch[]\" type=\"checkbox\" value=\"AzotServ\" >Азот-Сервис</li>\n";//шифр 24
  print "<li><input name=\"chkstatusSearch[]\" type=\"checkbox\" value=\"Alien\" >Сторонние организации</li>\n";//шифр 100
  print "</ul>";
  print "</FIELDSET>\n";

	print "<Tr>\n";
	print "<Td colspan=2 align=center><input type=\"submit\" name=\"getData5\" value=\"Получить данные\"> \n";
	print "</Table>\n";
	print "</form>";
	print "</div>\n";
}
//******************************************************************************
// cells4() -панель ввода данных для отчета Сводные данные по методике ДКС
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function cells4()
{
	print "<div class=\"report4\">\n";
	print  "<form action=\"selectReport.php\" method=\"post\">\n";
	print "<H4 class=\"legend\">Сводные данные по методике ДКС</H4>";
	print "<Table  Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" >\n";
	print "<Tr>\n";
	print "<Td>Выберите отдел\n";
	print "<Td>";
	print "<select size=\"1\" class=\"selectDO\" name=\"Department\">\n";
	print "<option>Выбор отдела</option>\n";
	//заполнение списка отделов
	getArrayNameDepartment();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Выберите месяц\n";
	print "<Td>";
	//печатаем список месяцев
	printLstMonth();
	print "<Tr>\n";
	print "<Td>Выберите год\n";
	print "<Td>";
	//печатаем список лет
	printLstYear();
	print "<Tr>\n";
	print "<Td colspan=2>";
	print "<FIELDSET>\n";
	print "<input name=\"rbCategry\" type=\"radio\" value=\"time\" >по табельному времени (календарное)\n";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td colspan=2>";
	print "<FIELDSET>\n";
	print "<input name=\"rbCategry\" type=\"radio\" value=\"man_hour\" checked>по трудозатратам (время утверждения)\n";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td colspan=2>";
	print "<FIELDSET>\n";
	print "<input name=\"rbCategry\" type=\"radio\" value=\"time_DKS\">приведение к табельному времени от трудозатрат\n";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td>Коэффициенты\n";
	print "<Td>";
	print "<b>А1</b>&nbsp;<Input Type=\"text\" Name=\"K_Graph\" Value=\"0.9\" class=\"size1\">\n";
	print "<b>А4</b>&nbsp;<Input Type=\"text\" Name=\"K_Text\" Value=\"0.95\" class=\"size1\">\n";
	print "<Tr>\n";
	print "<Td colspan=2>\n";
	print "<FIELDSET>\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"1-31\">календарный период\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"26-25\" checked=\"checked\">отчетный период";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td colspan=2 align=center><input type=\"submit\" name=\"getData6\" value=\"Получить данные\"> \n";
	print "</Table>\n";
	print "</form>";
	print "</div>\n";
}
//******************************************************************************
// cells5() -панель ввода данных для отчета Сводные данные по выработкам в подразделениях
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function cells5()
{
	// нач.отдела имеют доступ
	print "<div class=\"report5\">\n";
	print "<form action=\"selectReport.php\" method=\"post\">\n";
	print "<H4 class=\"legend\">Сводные данные по выработкам в подразделениях</H4>";
	print "<Table  Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" >\n";
	print "<Tr>\n";
	print "<Td>Выберите отдел\n";
	print "<Td>";
	print "<select size=\"1\" class=\"selectDO\" name=\"Department\">\n";
	print "<option>Выбор отдела</option>\n";
	//заполнение списка отделов
	getArrayNameDepartment();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Выберите бюро\n";
	print "<Td>";
	print "<select size=\"1\" class=\"selectDO\" name=\"Office\">\n";
	print "<option>Выбор бюро</option>\n";
	//заполнение списка бюро
	getArrayNameOffice();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Выберите месяц\n";
	print "<Td>";
	//печатаем список месяцев
	printLstMonth();
	print "<Tr>\n";
	print "<Td>Выберите год\n";
	print "<Td>";
	//печатаем список лет
	printLstYear();
	print "<Tr>\n";
	print "<Td>Тип отчета";
	print "<Td>\n";
	print "<FIELDSET>\n";
	print "<input name=\"rbCategry\" type=\"radio\" value=\"Employee\" checked>Отчет по ФИО\n";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td colspan=2>\n";
	print "<FIELDSET>\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"1-31\">календарный период\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"26-25\" checked=\"checked\">отчетный период";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td colspan=2 align=center><input type=\"submit\" name=\"getData3\" value=\"Получить данные\"> \n";
	print "</Table>\n";
	print "</form>";
	print "</div>\n";
}
//******************************************************************************
// cells_performance() -панель для отчета "Производительность подразделения"
//Входные параметры:
//Выходные параметры:
//******************************************************************************
function cells_performance()
{
	// нач.отдела имеют доступ
	print "<div class=\"report_performance\">\n";
	print "<form action=\"selectReport.php\" method=\"post\">\n";
	print "<H4 class=\"legend\">Производительность подразделения</H4>";
	print "<Table  Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" >\n";
	print "<Tr>\n";
	print "<Td>Выберите отдел\n";
	print "<Td>";
	print "<select size=\"1\" class=\"selectDO\" name=\"Department\">\n";
	print "<option>Выбор отдела</option>\n";
	//заполнение списка отделов
	getArrayNameDepartment();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Выберите объект\n";
	print "<Td>";
	print "<select size=\"1\" class=\"selectDO\" name=\"lstObject\">\n";
	print "<option>Выбор объекта</option>\n";
	//заполнение списка объектов
	getArrayProject();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Выберите год\n";
	print "<Td>";
	//печатаем список лет
	printLstYear();
	print "<Tr>\n";
	print "<Td colspan='2'>";
	print "<FIELDSET>\n";
	print "<input name=\"rbCategry\" type=\"radio\" value=\"time\" >по общему времени\n<br/>";
	print "<input name=\"rbCategry\" type=\"radio\" value=\"man_hour\" checked>по утвержденным работам\n";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td colspan=2>\n";
	print "<FIELDSET>\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"1-31\">календарный период\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"26-25\" checked=\"checked\">отчетный период";
	print "</FIELDSET>\n";

	print "<Tr>\n";
	print "<Td colspan=2 align=center><input type=\"submit\" name=\"getDataPerformance\" value=\"Получить данные\"> \n";
	print "</Table>\n";
	print "</form>";
	print "</div>\n";	
	
		
}


/**
* cells_norma() -   панель ввода данных для отчета "Нормирование трудозатрат"
* 
*/
function cells_norma()
{
	print "<div class=\"report_norma\">\n";
	print  "<form action=\"selectReport.php\" method=\"post\">\n";
	print "<H4 class=\"legend\">Нормативы по годам</H4>";
	print "<FIELDSET>\n";
	print "<legend>год</legend>\n";
	print "<ul class=\"menu_input\">";
	foreach($_SESSION['Year'] as $key=>$value)
	{
		//отмечаем текущий год
		if ($key == $_SESSION['CurrentYear'])
		{
			print "<li><input type=\"checkbox\" name=\"chkYearSearch[$key]\" value=\"$value\" checked/>$value</li> ";
		}
		else {
			print "<li><input type=\"checkbox\" name=\"chkYearSearch[$key]\" value=\"$value\" />$value</li> ";
		}
	}
	print "</ul>";	
	print "</FIELDSET><br/>\n";
	print "<FIELDSET>\n";
	print "<legend>период</legend>\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"1-31\">календарный<br/>\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"26-25\" checked=\"checked\">отчетный";
	print "</FIELDSET>\n";
	
	print "<input type=\"submit\" name=\"btnNorma\" value=\"Показать\" style='margin-left:50px;'> \n";
	print "</form>";
	print "</div>\n";
}

//******************************************************************************
// cells6() -панель ввода данных для отчета Свод трудозатрат отдела по месяцам
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function cells6()
{
	// нач.отдела имеют доступ
	print "<div class=\"report6\">\n";
	print  "<form action=\"selectReport.php\" method=\"post\">\n";
	print "<H4 class=\"legend\">Свод трудозатрат отдела по месяцам</H4>";
	print "<Table Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" >\n";
	print "<Tr>\n";
	print "<Td>Выберите отдел\n";
	print "<Td>";
	print "<select size=\"1\" class=\"selectDO\" name=\"Department\">\n";
	print "<option>Выбор отдела</option>\n";
	//заполнение списка отделов
	getArrayNameDepartment();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Введите заказчика\n";
	print "<Td>";
	print "<select size=\"1\" class=\"selectCustomer\" name=\"lstCustomer\">\n";
	print "<option>Выбор заказчика</option>\n";
	//заполнение списка заказчиков
	getArrayNameCustomer();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Выберите объект\n";
	print "<Td>";
	print "<select size=\"1\" class=\"selectDO\" name=\"lstObject\">\n";
	print "<option>Выбор объекта</option>\n";
	//заполнение списка объектов
	getArrayProject();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Выберите месяц\n";
	print "<Td>";
	//печатаем список месяцев
	printLstMonth();
	print "<Input Type=\"date\" Name=\"DateBegin\"  class=\"g-2\">\n";
	print "<Tr>\n";
	print "<Td>Выберите год\n";
	print "<Td>";
	//печатаем список лет
	printLstYear();
	print "<Input Type=\"date\" Name=\"DateEnd\" class=\"g-2\">\n";
	print "<Tr>\n";
	print "<Td>";
	print "<FIELDSET>\n";
	print "<input name=\"rbCategry\" type=\"radio\" value=\"time\" checked>по общему времени\n";
	print "</FIELDSET>\n";
	print "<Td>";
	print "<FIELDSET>\n";
	print "<input name=\"rbCategry\" type=\"radio\" value=\"man_hour\">по утвержденным работам\n";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td>";
	print "<FIELDSET>\n";
	print "<input name=\"rbCategryAdd\" type=\"radio\" value=\"project\" checked>по проектам\n";
	print "</FIELDSET>\n";
	print "<Td>";
	print "<FIELDSET>\n";
	print "<input name=\"rbCategryAdd\" type=\"radio\" value=\"employee\">по Ф.И.О.\n";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td colspan=2>\n";
	print "<FIELDSET>\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"1-31\">календарный период\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"26-25\" checked=\"checked\">отчетный период";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td colspan=2 align=center><input type=\"submit\" name=\"getData4\" value=\"Получить данные\"> \n";
	print "</Table>\n";
	print "</form>";
	print "</div>\n";
}
//******************************************************************************
// cells7() -панель ввода данных для отчета Проверка табельного времени
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function cells7()
{
	// нач.отдела имеют доступ
	print "<div class=\"report7\">\n";
	print  "<form action=\"selectReport.php\" method=\"post\">\n";
	print "<H4 class=\"legend\">Проверка табельного времени</H4>";
	print "<Table Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" >\n";
	print "<Tr>\n";
	print "<Td>Выберите отдел\n";
	print "<Td>";
	print "<select size=\"1\" class=\"selectDO\" name=\"Department\">\n";
	print "<option>Выбор отдела</option>\n";
	//заполнение списка отделов
	getArrayNameDepartment();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Выберите месяц\n";
	print "<Td>";
	//печатаем список месяцев
	printLstMonth();
	print "<Tr>\n";
	print "<Td>Выберите год\n";
	print "<Td>";
	//печатаем список лет
	printLstYear();
	print "<Tr>\n";
	print "<Td colspan=2>\n";
	print "<FIELDSET>\n";
	print "<legend>период</legend>\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"1-31\">календарный\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"26-25\" checked=\"checked\">отчетный";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td colspan=2 align=center><input type=\"submit\" name=\"getData7\" value=\"Получить данные\"> \n";
	print "</Table>\n";
	print "</form>";
	print "</div>\n";
}
//******************************************************************************
// cells10() -панель ввода данных для отчета Табель учета рабочего времени
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function cells10()
{
	// нач.отдела имеют доступ
	print "<div class=\"report10\">\n";
	print  "<form action=\"selectReport.php\" method=\"post\">\n";
	print "<H4 class=\"legend\">Табель учета рабочего времени</H4>";
	print "<Table Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" >\n";
	print "<Tr>\n";
	print "<Td>Выберите отдел\n";
	print "<Td>";
	print "<select size=\"1\" class=\"selectDO\" name=\"Department\">\n";
	print "<option>Выбор отдела</option>\n";
	//заполнение списка отделов
	getArrayNameDepartment();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Выберите месяц\n";
	print "<Td>";
	//печатаем список месяцев
	printLstMonth();
	print "<Tr>\n";
	print "<Td>Выберите год\n";
	print "<Td>";
	//печатаем список лет
	printLstYear();
	print "<Tr>\n";
	print "<Td colspan=2>\n";
	print "<FIELDSET>\n";
	print "<legend>период</legend>\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"1-31\">календарный\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"26-25\" checked=\"checked\">отчетный";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td colspan=2 align=center><input type=\"submit\" name=\"getData10\" value=\"Получить данные\"> \n";
	print "</Table>\n";
	print "</form>";
	print "</div>\n";
}
//******************************************************************************
// cells8() -панель ввода данных для отчета План отдела
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function cells8()
{
	// нач.отдела имеют доступ
	print "<div class=\"report8\">\n";
	print  "<form action=\"selectReport.php\" method=\"post\">\n";
	print "<H4 class=\"legend\">План отдела</H4>";
	print "<Table Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" >\n";
	print "<Tr>\n";
	print "<Td>Выберите отдел\n";
	print "<Td>";
	print "<select size=\"1\" class=\"selectDO\" name=\"Department\">\n";
	print "<option>Выбор отдела</option>\n";
	//заполнение списка отделов
	getArrayNameDepartment();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Выберите месяц\n";
	print "<Td>";
	//печатаем список месяцев
	printLstMonth();
	print "<Tr>\n";
	print "<Td>Выберите год\n";
	print "<Td>";
	//печатаем список лет
	printLstYear();
	print "<Tr>\n";
	print "<Td colspan=2 align=center><input type=\"submit\" name=\"getData8\" value=\"Получить данные\"> \n";
	print "</Table>\n";
	print "</form>";
	print "</div>\n";
}

//******************************************************************************
//cellTabelTime() -панель для ввода табельного времени
//Входные параметры:
//Выходные параметры:
//******************************************************************************
function cellTabelTime()
{
	// нач.отдела имеют доступ
	print "<div class=\"report8\">\n";
	print  "<form action='checkTabelTime.php' method='post'>\n";
	print "<H4 class='legend'>Табельное время</H4>";
	print "<Table Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" >\n";

	print "<Tr>\n";
	print "<Td colspan=2>";
	print "<FIELDSET>\n";
	print "<input name=\"rbCategry\" type=\"radio\" value=\"add\">Добавить\n<br/>";
	print "<input name=\"rbCategry\" type=\"radio\" value=\"del\">Удалить\n<br/>";
	print "<input name=\"rbCategry\" type=\"radio\" value=\"upd\" checked>Редактировать\n";
	print "</FIELDSET>\n";
	
	print "<Tr>\n";
	print "<Td>Выберите год\n";
	print "<Td>";
	//печатаем список лет
	printLstYear();
	print "<Tr>\n";
	print "<Td colspan=2 align=center><input type='submit' name='btnTabelTime' id='btnTabelTime' value='OK'> \n";
	print "</Table>\n";
	print "</form>";
	print "</div>\n";	
}
//******************************************************************************
//cells9() - функция
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function cells9()
{
	// нач.отдела не имеют доступ
	print "<div class=\"report9\">\n";
	print  "<form action=\"selectReport.php\" method=\"post\">\n";
	print "<H4 class=\"legend\">Отчет по выполненным работам</H4>";
	print "<Table Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" >\n";
	//дата завершения работы
	print "<Tr>\n";
	print "<Td>Выберите заказчика\n";
	print "<Td>";
	print "<select size=\"1\" class=\"selectCustomer\" name=\"lstCustomer\">\n";
	print "<option>Выбор заказчика</option>\n";
	//заполнение списка заказчиков
	getArrayNameCustomer();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Месяц завершения\n";
	print "<Td>";
	//печатаем список месяцев
	printLstMonth();
	print "<Input Type=\"date\" Name=\"DateBegin\"  class=\"g-2\">\n";
	print "<Tr>\n";
	print "<Td>Год завершения\n";
	print "<Td>";
	//печатаем список лет
	printLstYear();
	print "<Input Type=\"date\" Name=\"DateEnd\" class=\"g-2\">\n";
	print "<Tr>\n";
	print "<Td>Тип отчета\n";
	print "<td>";
	print "<FIELDSET>\n";
	print "<input name=\"rbTypeReport\" type=\"radio\" value=\"short\" checked>краткий\n<br>";
	print "<input name=\"rbTypeReport\" type=\"radio\" value=\"long\">подробный\n<br>";
	print "<input name=\"rbTypeReport\" type=\"radio\" value=\"month\">месячный план\n<br>";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<td colspan=2>";
	print "<FIELDSET>\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"1-31\">календарный период\n";
	print "<input type=\"radio\" name=\"rbPeriod\" value=\"26-25\" checked=\"checked\">отчетный период";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td colspan=2 align=center><input type=\"submit\" name=\"getData9\" value=\"Получить данные\"> \n";
	print "</Table>\n";
	print "</form>";
	print "</div>\n";
}
//******************************************************************************
//checkField() - проверка входных данных для табл.Plan_Time
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function checkField()
{
	global $errors;
	//выбираем значения месяца и года
	if ( $_REQUEST['lstMonth'] == "Выберите месяц" OR
	$_REQUEST['lstYear'] == "Выберите год" )
	{
		$errors[] = "<H3>Необходимо указать месяц и год</H3>\n";
	}
	if ($_REQUEST['Department'] == "Выбор отдела")
	{
		$errors[] = "<H3>Необходимо указать отдел</H3>\n";
	}
	if (empty($_REQUEST['Tabel_Time']))
	{
		$_REQUEST['Tabel_Time'] = 0;
	}
	if ( empty($_REQUEST['PlanMonthTime']) OR
	empty($_REQUEST['PlanTime']) OR
	empty($_REQUEST['PlanDepartment']) )
	{
		$errors[] = "<H3>Должны быть заполнены данные по выработкам</H3>\n";
	}
	else {
		//в случае ошибки ввода дробной части числа, легко заменяем разделитель на точку
		$symbol = array(",", ":", ";", "-", "|", "\\", "/", "~","?");
		$_REQUEST['PlanMonthTime'] = str_replace($symbol, ".", $_REQUEST['PlanMonthTime']);
		$_REQUEST['PlanTime'] = str_replace($symbol, ".", $_REQUEST['PlanTime']);
		$_REQUEST['PlanDepartment'] = str_replace($symbol, ".", $_REQUEST['PlanDepartment']);
		//проверка на тождественность данных числам, а не строкам
		if ( !is_numeric($_REQUEST['PlanMonthTime']) )
		{
			$errors[] = "<H3>Плановый фонд {$_REQUEST['PlanMonthTime']} - нечисловое значение</H3>";
		}
		if ( !is_numeric($_REQUEST['PlanTime']) )
		{
			$errors[] = "<H3>План на 1 исполнителя {$_REQUEST['PlanTime']} - нечисловое значение</H3>";
		}
		if ( !is_numeric($_REQUEST['PlanDepartment']) )
		{
			$errors[] = "<H3>Плановая выработка на отдел {$_REQUEST['PlanDepartment']} - нечисловое значение</H3>";
		}
	}
}


//******************************************************************************
//checkTabelTime() - ф-ия проверки наличия в табл. tabel_time записей с указанным годом
//Входные параметры: $lstYear - год 2008,,,2013
//Выходные параметры: TRUE, FALSE - 
//******************************************************************************
function checkTabelTime($lstYear)
{
global $link;

$query = "SELECT tabel_time.id  from tabel_time, year where
		(tabel_time.id_Year=year.id) and 
		(year.Year=".escapeshellarg($lstYear).")
		limit 1 ";

	if ( !($dbResult = mysql_query($query, $link)) )
	{
		//print "Выборка $Query_Select не удалась!\n".mysql_error();
		processingError("$query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	} else {

		if(mysql_num_rows($dbResult) > 0) 
		{
			return TRUE;		
		} else	
		{
			return FALSE;
		}
	}
}
//******************************************************************************
//on_line() - вывод количества человек на сайте
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function on_line()
{
	global $link;
	$wine = 300; //время в течение которого мы считаем находящимся на сайте
	$Query_Update = "DELETE FROM online WHERE time+$wine < ".time().
	" OR ip = ".escapeshellarg($_SERVER['REMOTE_ADDR']);
	if ( !($dbResult = mysql_query($Query_Update, $link)) )
	{
		//print "Выборка $Query_Update не удалась!\n".mysql_error();
		processingError("$Query_Update ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	$Query_Insert = "INSERT INTO online SET ip = ".escapeshellarg($_SERVER['REMOTE_ADDR']).
	", time = ".escapeshellarg(time());
	if ( !($dbResult = mysql_query($Query_Insert, $link)) )
	{
		//print "Выборка $Query_Insert не удалась!\n".mysql_error();
		processingError("$Query_Insert ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	$Query_Select = "SELECT id FROM online";
	if ( !($dbResult = mysql_query($Query_Select, $link)) )
	{
		//print "Выборка $Query_Select не удалась!\n".mysql_error();
		processingError("$Query_Select ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	$online_people = mysql_num_rows($dbResult);
	print("На сайте человек: ".$online_people);
}
//******************************************************************************
//getDateTime() - вывод новой формы записи времени
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function getDateTime($param)
{
	global $link;
	$Query = "SELECT FROM_UNIXTIME(".$param.") AS d";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		$row = mysql_fetch_array($dbResult, MYSQL_BOTH);
		return $row['d'];
	}
}
//******************************************************************************
//HeadTable() - таблица заголовка в модуле departmentReport.php
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function HeadTable()
{
	global $link, $Month;
	print "<Table  Border=\"1\" CellSpacing=\"0\" CellPadding=\"2\">\n";
	print "<Tr>\n";
	print "<Td>";
	print "Отдел: ";
	if ( $_SESSION['Department'] != "Выбор отдела" AND
	$_SESSION['Office'] != "Выбор бюро" )
	{
		print getNameDepartment($_SESSION['Department'])."-".getNameOffice($_SESSION['Office']);
	}
	elseif ( $_SESSION['Department'] != "Выбор отдела" )
	{
		print getNameDepartment($_SESSION['Department']);
	}
	//в случае ошибки ввода дробной части числа, легко заменяем разделитель на точку
	//   $symbol = array(",", ":", ";", "-", "|", "\\", "/", "~","?");
	//   $_SESSION['Plan_Time'] = str_replace($symbol, ".", $_SESSION['Plan_Time']);
	//   $_SESSION['Plan_One'] = str_replace($symbol, ".", $_SESSION['Plan_One']);
	//   $_SESSION['Plan_All'] = str_replace($symbol, ".", $_SESSION['Plan_All']);
	//Получаем данные по плановому фонду, плановой выработке и выработке на 1 исполнителя.
	$QueryPlanTime =  " SELECT SUM(Plan) AS Plan, SUM(PlanOneMan) AS PlanOneMan, SUM(PlanDepartment) AS PlanDepartment ".
	" FROM plan_time, month, department, year ".
	" WHERE ".
	" (Plan_Time.id_Month = Month.id) ".
	" AND (Plan_Time.id_Year = Year.id) ".
	" AND (Plan_Time.id_Department = Department.Id) ".
	" AND (id_Department = ".escapeshellarg($_SESSION['Department']).")";
	if ($_SESSION['lstMonth'] == "Выберите месяц")
	{
		$SelectMonth = "";
	}
	else {
		$SelectMonth = " AND (id_Month = ".escapeshellarg($_SESSION['pageMonth']).")";
	}
	if ($_SESSION['lstYear'] == "Выберите год")
	{
		$SelectYear = "";
	}
	else {
		$SelectYear = " AND (id_Year = (SELECT id FROM Year WHERE Year=".escapeshellarg($_SESSION['pageYear'])."))";
	}
	$QueryPlanTime = $QueryPlanTime.$SelectMonth.$SelectYear." GROUP BY department.id";
	//print  $QueryPlanTime;
	//file_put_contents("query.txt",$QueryPlanTime);
	if ( !($dbResult = mysql_query($QueryPlanTime,$link)) )
	{
		//print "Выборка $QueryPlanTime не удалась!\n".mysql_error();
		processingError("$QueryPlanTime ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			$Plan_Time = $row['Plan'];       // плановый фонд времени
			$Plan_One =  $row['PlanOneMan']; //плановая выработка на 1 исполнителя
			$Plan_All =  $row['PlanDepartment'];//плановая выработка отдела
		}
	}
	//начало оформления страницы
	print "<Td>";
	print "Плановый фонд времени:&nbsp;";
	print "<Td>";
	print round($Plan_Time,1);
	print "<Tr>\n";
	print "<Td>";
	print "Месяц&nbsp";
	print $Month[$_SESSION['pageMonth']];
	print "<Td>";
	print "Плановая выработка&nbsp;";
	print "<Td>";
	print round($Plan_All,1);
	print "<Tr>\n";
	print "<Td>";
	print "Численность&nbsp;";
	print "<Td>";
	print "На 1 исполнителя&nbsp;";
	print "<Td>";
	print round($Plan_One,1);
	print "</Table>\n";
	$_SESSION['Plan_Time'] =  $Plan_Time;
	$_SESSION['Plan_One'] =  $Plan_One;
	$_SESSION['Plan_All'] =  $Plan_All;
	return true;
}
//******************************************************************************
//selectDepartment() - функция возвращающая строку в запросе, где требуется ид отдела
//Входные параметры:
//Выходные параметры: $QueryDepartment
//******************************************************************************

function selectDepartment()
{
	//для каждого начаьлника выбирается свой отдел
	if ( $_SESSION['Department'] == "Выбор отдела" )
	{
		if ( ($_SESSION['Name_Post'] == "Начальник отдела") OR
		($_SESSION['Name_Post'] == "Зам.начальника отдела"))
		{
			$QueryDepartment = " AND (department.id=".escapeshellarg(getIdDepartment($_SESSION['Name_Department'])).")";
		}
		else {
			$QueryDepartment = "";
		}
	}
	else {
		$QueryDepartment = " AND (department.Id=".escapeshellarg($_SESSION['Department']).")";
	}
	return $QueryDepartment;
}
//******************************************************************************
//checkRecordPlan_Employee() - функция проверки записей, на тот или иной месяц в табл.Plan_Employee
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function checkRecordPlan_Employee()
{
	global $link;
	$Query1 = "SELECT plan_employee.id FROM plan_employee,employee,department
	WHERE
	(plan_employee.id_Employee=employee.id) AND
	(employee.id_Department=department.id) AND
	id_Year=(SELECT id FROM Year WHERE Year.Year=".escapeshellarg($_SESSION['pageYear']).")
		AND
	id_Month=(SELECT id FROM Month WHERE Month.id=".escapeshellarg($_SESSION['pageMonth']).")";
	//выбираем выбранный отдел
	$QueryDepartment = selectDepartment();
	$Query = $Query1.$QueryDepartment;
	if ( !($dbResult = mysql_query($Query,$link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		$res = mysql_num_rows($dbResult);
		return $res;
	}
	return false;
}
//******************************************************************************
//DrawTable_ReportPlanDepartmentUpdate() - функция отрисовки табл. в отчете ReportPlanDepartment, с обновлением т. Plan_Employee
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function DrawTable_ReportPlanDepartmentUpdate()
{
	global $link;
	//выбрать всех сотрудников, входящих в указанный отдел
	$QueryEmployee = "SELECT plan_employee.id, employee.Family, employee.Name, employee.Patronymic, employee.Status, plan_employee.Correction ".
	"FROM employee, plan_employee,department ".
	"WHERE ".
	"(employee.Id = plan_employee.id_Employee) AND ".
	"(employee.id_Department=department.id) AND ".
	//"(employee.Status = ".escapeshellarg('TRUE').") AND ".  //не уволен, иначе будут отображаться все, кто работал
	"(id_Month = ".escapeshellarg($_SESSION['pageMonth']).") AND ".
	"(id_Year=(SELECT id FROM Year WHERE Year.Year=".escapeshellarg($_SESSION['pageYear']).")) AND ".
	"((employee.StatusMonth >=".escapeshellarg($_SESSION['pageMonth'])." AND ".
	"employee.StatusYear >=(SELECT id FROM Year WHERE Year=".escapeshellarg($_SESSION['pageYear']).")) OR ".
	"(employee.StatusMonth = ".escapeshellarg('0')." AND ".
	"employee.StatusYear = ".escapeshellarg('0').")) ";
	//выбираем выбранный отдел
	$QueryDepartment = selectDepartment();
	$QueryGroupBy = " GROUP BY employee.id";
	$QueryOrderBy = " ORDER BY BINARY employee.Family";
	$QueryMan = $QueryEmployee.$QueryDepartment.$QueryGroupBy.$QueryOrderBy;
	//print $QueryMan;
	//file_put_contents("query.txt",$QueryMan);
	if ( !($dbResultMan = mysql_query($QueryMan, $link)) )
	{
		//print "Выборка $QueryMan не удалась!\n".mysql_error();
		processingError("$QueryMan ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		//таблица HTML
		print "<table  Border=\"2\" CellSpacing=\"0\" CellPadding=\"2\">\n";
		print "<tr bgcolor=#E8E8E8 align=center>\n";
		print "<th>№</TH>";
		print "<th>Ф.И.О.</TH>";
		print "<th>План на 1 <br>исполнителя</th>";
		print "<th>Отношение в %</th>";
		print "</tr>\n";
		$n = 0; //счетчик
		$tmpPlanEmployee = 0;
		while ( $rowMan = mysql_fetch_array($dbResultMan, MYSQL_BOTH) )
		{
			print "<tr align=center>\n";
			print "<td>".($n+1)."</td>";
			print "<td nowrap align=left>{$rowMan['Family']} ".mb_substr($rowMan['Name'],0,1,'utf8').".".mb_substr($rowMan['Patronymic'],0,1,'utf8').".</td>";
			print "<td><input name=\"planEmp[]\" type=\"text\"
			value=".escapeshellarg($rowMan['Correction'])."></td>\n";
			printZeroTD(round(100*$rowMan['Correction']/$_SESSION['Plan_One'],1));
			$n++;
			$array_id[] = $rowMan['id'];  //id записи в таблице Plan_Employee
			$tmpPlanEmployee = $tmpPlanEmployee + $rowMan['Correction'];
		}
		print "<tr align=center>\n";
		print "<td colspan=2 align=right><b>Итого:</b></td>";
		//        $Sum = array_sum($tmpPlanEmployee); //итоговая сумма для проверки плановой выработки на отдел
		printZeroTH($tmpPlanEmployee);
		printZeroTH(round(100*$tmpPlanEmployee/$_SESSION['Plan_All'],1));
		print "<tr align=center>\n";
		print "<td colspan=2 align=right><b>Разница:</b></td>";
		//разность округляем до целых
		printZeroTH(round($tmpPlanEmployee - $_SESSION['Plan_All'],0));
		printZeroTH();
		print "</TABLE>\n";
		print "    <p></p>   ";
		//print "<input type=\"submit\" name=\"SAVE\" value=\"SAVE\">\n  ";
		print "<input type=\"submit\" name=\"OK\" value=\"UPDATE\">\n  ";
		print "<div class=\"hint\"><span>Подсказка! </span>Обновить данные в базе</div>";
	}
	//соединим массивы id сотрудников и планового времени
	if (isset($_REQUEST['OK']))
	{
		$result = array_combine($array_id,$_REQUEST['planEmp']);
		return $result;
	}
	else
	//если никакая кнопка не нажата, нет смысла что-то делать
	if (empty($_REQUEST['OK']))
	{
		//$result = array_combine($arrayEmployee,$tmpPlanEmployee);
		exit;
	}
}
//******************************************************************************
//DrawTable_ReportPlanDepartmentInsert() - функция отрисовки табл. в отчете ReportPlanDepartment, с втавкой новых значений  т. Plan_Employee
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function DrawTable_ReportPlanDepartmentInsert()
{
	global $link;
	//выбрать всех сотрудников, входящих в указанный отдел
	$QueryEmployee = "SELECT employee.id, employee.Family, employee.Name, employee.Patronymic, employee.Status, ".
	"office.Name_Office, department.Name_Department, post.Name_Post ".
	"FROM employee,department,office,post ".
	"WHERE ".
	"(department.Id = employee.id_Department) AND ".
	"(office.Id = employee.id_Office) AND ".
	"(post.Id = employee.id_Post) AND ".
	"((employee.StatusMonth >=".escapeshellarg($_SESSION['pageMonth'])." AND ".
	"employee.StatusYear >=(SELECT id FROM Year WHERE Year=".escapeshellarg($_SESSION['pageYear']).")) OR ".
	"(employee.StatusMonth = ".escapeshellarg('0')." AND ".
	"employee.StatusYear = ".escapeshellarg('0').")) ";
	//        	"(employee.Status = ".escapeshellarg('TRUE').")";  //не уволен, иначе будут отображаться все, кто работал
	//выбираем выбранный отдел
	$QueryDepartment = selectDepartment();
	$QueryGroupBy = " GROUP BY employee.id";
	$QueryOrderBy = " ORDER BY BINARY employee.Family";
	$QueryMan = $QueryEmployee.$QueryDepartment.$QueryGroupBy.$QueryOrderBy;
	//print $QueryMan;
	//file_put_contents("query.txt",$QueryMan);
	if ( !($dbResultMan = mysql_query($QueryMan, $link)) )
	{
		//print "Выборка $QueryMan не удалась!\n".mysql_error();
		processingError("$QueryMan ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		//таблица HTML
		print "<table  Border=\"2\" CellSpacing=\"0\" CellPadding=\"2\">\n";
		print "<tr bgcolor=#E8E8E8 align=center>\n";
		print "<th>№</TH>";
		print "<th>Ф.И.О.</TH>";
		print "<th>План на 1 <br>исполнителя</th>";
		print "<th>Отношение в %</th>";
		print "</tr>\n";
		//если не нажата кнопка подтверждения
		if (empty($_REQUEST['OK']) AND empty($_REQUEST['SAVE']))
		{
			$Plan = round($_SESSION['Plan_One'],1);
			$tmpPlanEmployee = array_fill(0,mysql_num_rows($dbResultMan),$Plan);//занести в массив значения плана на каждого сотрудника
		}
		if (isset($_REQUEST['SAVE']))
		{
			//сохраняем данные во временный массив
			$tmpPlanEmployee = $_REQUEST['planEmp'];
		}
		$n = 0; //счетчик
		while ( $rowMan = mysql_fetch_array($dbResultMan, MYSQL_BOTH) )
		{
			print "<tr align=center>\n";
			print "<td>".($n+1)."</td>";
			print "<td nowrap align=left>{$rowMan['Family']} ".mb_substr($rowMan['Name'],0,1,'utf8').".".mb_substr($rowMan['Patronymic'],0,1,'utf8').".</td>";
			if ($rowMan['Name_Post'] == "Начальник отдела" OR
			$rowMan['Name_Post'] == "Зам.начальника отдела" OR
			$rowMan['Name_Post'] == "Начальник бюро" OR
			$rowMan['Name_Post'] == "Старший специалист" OR
			$rowMan['Name_Post'] == "Старший технолог" OR
			$rowMan['Name_Post'] == "Старший механик" )
			{
				$tmpPlanEmployee[$n] = 0;
			}
			print "<td><input name=\"planEmp[]\" type=\"text\"
			value=".escapeshellarg($tmpPlanEmployee[$n])."></td>\n";
			printZeroTD(round(100*$tmpPlanEmployee[$n]/$_SESSION['Plan_One'],1));
			$n++;
			$arrayEmployee[] = $rowMan['id'];  //id сотрудников
		}
		print "<tr align=center>\n";
		print "<td colspan=2 align=right><b>Итого:</b></td>";
		$Sum = array_sum($tmpPlanEmployee); //итоговая сумма для проверки плановой выработки на отдел
		printZeroTH($Sum);
		printZeroTH(round(100*$Sum/$_SESSION['Plan_All'],1));
		print "<tr align=center>\n";
		print "<td colspan=2 align=right><b>Разница:</b></td>";
		//разность округляем до целых
		printZeroTH(round($Sum - $_SESSION['Plan_All'],0));
		printZeroTH();
		print "</TABLE>\n";
		print "<p></p>";
		print "<div class=\"hint\"><span>Подсказка! </span>План на стажеров должен быть равен нулю.</div>";
		print "    <p></p>   ";
		print "<input type=\"submit\" name=\"SAVE\" value=\"SAVE\">\n  ";
		print "<div class=\"hint\"><span>Подсказка! </span>Сохранение данных, без внесения изменений в базу данных</div>";
		print "<p></p>";
		if (isset($_REQUEST['SAVE']))
		{
			print "<input type=\"submit\" name=\"OK\" value=\"INSERT\">\n  ";
			print "<div class=\"hint\"><span>Подсказка! </span>Записать данные в базу данных</div>";
			print "<p></p>";
		}
	}
	//соединим массивы id сотрудников и планового времени
	if (isset($_REQUEST['OK']))
	{
		$result = array_combine($arrayEmployee,$_REQUEST['planEmp']);
		return $result;
	}
	else
	if(isset($_REQUEST['SAVE']))
	{
		$result = array_combine($arrayEmployee,$_REQUEST['planEmp']);
		return $result;
	}
	else
	//если никакая кнопка не нажата, нет смысла что-то делать
	if (empty($_REQUEST['OK']) AND empty($_REQUEST['SAVE']))
	{
		//$result = array_combine($arrayEmployee,$tmpPlanEmployee);
		exit;
	}
}
//******************************************************************************
//ViewTableTask() - функция отрисовки табл в AdminProject_Task
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function ViewTableTask()
{
	global $link;
	$Query = " SELECT task.id, task.id_Project, task.From_Dep, task.To_Dep, ".
	" task.DateExtradition, task.Comment, department1.Name_Department as Name1, department2.Name_Department as Name2".
	" FROM task, project, department as department1, department as department2 ".
	" WHERE ".
	" (task.From_Dep = department1.id) AND ".
	" (task.To_Dep = department2.id) AND ".
	" (task.id_Project = project.id) AND ".
	" (task.id_Project =".escapeshellarg( $_SESSION['idProject']).")".
	" ORDER BY task.DateExtradition DESC";
	//file_put_contents("query.txt",$Query);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $QueryMan не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		//таблица HTML
		print "<TABLE  Border=\"2\" CellSpacing=\"0\" CellPadding=\"2\">\n";
		print "<Caption><H4>Задания между подразделениями</Caption>\n";
		print "<TR  bgcolor=#E8E8E8 align=center>\n";
		print "<TH></TH>\n";
		print "<TH>От</TH>\n";
		print "<TH>Кому</TH>\n";
		print "<TH>Дата выдачи<br />задания</TH>\n";
		print "<TH>Комментарий</TH>\n";
		print "</TR>\n";
		//проверяем, есть ли результат
		$res = mysql_num_rows($dbResult);
		//если заданий для проекта внесено не было, отображаем пустую строку
		if ($res == 0)
		{
			print "<tr>";
			print "<td>";
			print "<input type=\"checkbox\" name=\"active_str[]\" value=\"".$row['id']."\" /> ";
			print "<input type=\"hidden\" name=\"hidden_task[]\" value=\"0\" />   ";
			print "</td>";
			//от кого задание
			print "<td>";
			print "<select name=\"fromDep[]\" size=\"1\">\n";
			print "<option></option>\n";
			getArrayNameDepartment();
			print "</select></td>";
			//кому задние
			print "<td>";
			print "<select name=\"toDep[]\" size=\"1\">\n";
			print "<option></option>\n";
			getArrayNameDepartment();
			print "</select></td>";
			// дата выдачи задания
			print "<td>";
			print "<Input Type=\"text\" Name=\"DateExtradition[]\" class=\"date_input\"></td>\n";
			// комментарии
			print "<td>";
			print "<Input Type=\"text\" Name=\"Comment[]\"></td>\n";
			print "</tr>";
			print "</table>";
			print "<p></p>";
			print "<input type=\"submit\" name=\"ok\" value=\"updateZero\" class='btnupdate'/>";
		}
		else {
			while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
			{
				print "<tr>";
				print "<td>";
				print "<input type=\"checkbox\" name=\"active_str[]\" value=\"".$row['id']."\" /> ";
				print "<input type=\"hidden\" name=\"hidden_task[]\" value=\"".$row['id']."\" />   ";
				print "</td>";
				//от кого задание
				print "<td>";
				print "<select name=\"fromDep[]\" size=\"1\">\n";
				print "<option>".getNameDepartment($row['From_Dep'])."</option>\n";
				getArrayNameDepartment();
				print "</select></td>";
				//кому задние
				print "<td>";
				print "<select name=\"toDep[]\" size=\"1\">\n";
				print "<option>".getNameDepartment($row['To_Dep'])."</option>\n";
				getArrayNameDepartment();
				print "</select></td>";
				// дата выдачи задания
				print "<td>";
				if ($row['DateExtradition'] == 0)
				{
					print "<Input Type=\"text\" Name=\"DateExtradition[]\" class=\"date_input\"></td>\n";
				}
				else {
					print "<Input Type=\"text\" Name=\"DateExtradition[]\" value=\"".strftime("%d.%m.%Y",$row['DateExtradition'])."\" class=\"date_input\"></td>\n";
				}
				//комментарий
				print "<td>";
				print "<Input Type=\"text\" Name=\"Comment[]\" value=\"".$row['Comment']."\"></td>\n";
				print "</tr>";
			}
			print "</table>";
			print "<p></p>";
			print "<input type=\"submit\" name=\"btnadd\" value=\"add\" class='btnadd'/>";
			print "<input type=\"submit\" name=\"ok\" value=\"update\" class='btnupdate'/>";
			print "<input type=\"submit\" name=\"btncopy\"  value=\"copy\" class='btncopy' />";
			print "<input type=\"submit\" name=\"btndel\"   value=\"delete\" class='btndel' />";
		}
		$result = array(
		"id"				=>	$_REQUEST['hidden_task'],
		"from_dep"			=>	$_REQUEST['fromDep'],
		"to_dep"			=>	$_REQUEST['toDep'],
		"date_extradition"	=>	$_REQUEST['DateExtradition'],
		"comment"			=>	$_REQUEST['Comment']
		);
		//нажата кнопка добавления новой строки
		if (isset($_REQUEST['btnadd']))
		{
			return $result;
		}
		//нажата кнопка копирования
		if (isset($_REQUEST['btncopy']))
		{
			return $result;
		}
		//нажата кнопка копирования
		if (isset($_REQUEST['btndel']))
		{
			return $result;
		}
		//при сохранении результата в базе, создать супер массив, где объединятся все результаты
		if (isset($_REQUEST['ok']))
		{
			return $result;
		}
		//если никакая кнопка не нажата, прекрать выполнения скрипта
		if (empty($_REQUEST['ok']))
		{
			exit;
		}
	}
}
//******************************************************************************
//ViewTableProjectMark() - функция отрисовки табл в AdminProject_Mark
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function ViewTableProjectMark()
{
	global $link;
	$Query = " SELECT markbvp.id, markbvp.id_Project, markbvp.id_Mark, markbvp.NumberChange,
	markbvp.NumberMark, markbvp.DateExtraditionBVP, markbvp.DateCustomer, markbvp.Comment  ".
	" FROM markbvp, project, mark ".
	" WHERE ".
	" (markbvp.id_Mark = mark.id) AND ".
	" (markbvp.id_Project = project.id) AND ".
	" (markbvp.id_Project =".escapeshellarg($_SESSION['idProject']).")".
	" ORDER BY markbvp.id_Mark";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $QueryMan не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		//таблица HTML
		print "<TABLE  Border=\"2\" CellSpacing=\"0\" CellPadding=\"2\">\n";
		print "<Caption><H4>Выданные марки в бюро выпуска проектов</Caption>\n";
		print "<TR bgcolor=#E8E8E8 align=center>\n";
		print "<TH></TH>\n";
		print "<TH>Выданная марка</TH>\n";
		print "<TH>Порядковый<br /> номер</TH>\n";
		print "<TH>№ изменения</TH>\n";
		print "<TH>Дата выдачи в БВП</TH>\n";
		print "<TH>Дата выдачи заказчику</TH>\n";
		print "<TH>Комментарий</TH>\n";
		print "</TR>\n";
		//проверяем, есть ли результат
		$res = mysql_num_rows($dbResult);
		//если заданий для проекта внесено не было, отображаем пустую строку
		if ($res == 0)
		{
			print "<tr>";
			print "<td>";
			print "<input type=\"checkbox\" name=\"active_str[]\" value=\"".$row['id']."\" /> ";
			print "<input type=\"hidden\" name=\"hidden_mark[]\" value=\"0\" />   ";
			print "</td>";
			//марка
			print "<td>";
			print "<select name=\"mark[]\" size=\"1\">\n";
			print "<option></option>\n";
			getArrayNameMark();
			print "</select></td>";
			//порядковый номер марки
			print "<td>";
			print "<Input Type=\"text\" Name=\"NumMark[]\"></td>\n";
			//порядковый номер изменения
			print "<td>";
			print "<Input Type=\"text\" Name=\"NumChange[]\"></td>\n";
			// дата выдачи марки в бвп
			print "<td>";
			print "<Input Type=\"text\" Name=\"DateExtraditionBVP[]\" class=\"date_input\"></td>\n";
			// дата выдачи марки заказчику
			print "<td>";
			print "<Input Type=\"text\" Name=\"DateCustomer[]\" class=\"date_input\"></td>\n";
			// комментарии
			print "<td>";
			print "<Input Type=\"text\" Name=\"Comment[]\"></td>\n";
			print "</tr>";
			print "</table>";
			print "<p></p>";
			print "<input type=\"submit\" name=\"ok\" value=\"updateZero\" class='btnupdate'/>";
		}
		else {
			while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
			{
				print "<tr>";
				print "<td>";
				print "<input type=\"checkbox\" name=\"active_str[]\" value=\"".$row['id']."\" /> ";
				print "<input type=\"hidden\" name=\"hidden_mark[]\" value=\"".$row['id']."\" />   ";
				print "</td>";
				//марка
				print "<td>";
				print "<select name=\"mark[]\" size=\"1\">\n";
				print "<option>".getNameMark($row['id_Mark'])."</option>\n";
				getArrayNameMark();
				print "</select></td>";
				//порядковый номер марки
				print "<td>";
				if ($row['NumberMark'] == 0)
				{
					print "<Input Type=\"text\" Name=\"NumMark[]\"></td>\n";
				}
				else {
					print "<Input Type=\"text\" Name=\"NumMark[]\" value=\"".$row['NumberMark']."\"></td>\n";
				}
				//порядковый номер изменения
				print "<td>";
				if ($row['NumberChange'] == 0)
				{
					print "<Input Type=\"text\" Name=\"NumChange[]\"></td>\n";
				}
				else {
					print "<Input Type=\"text\" Name=\"NumChange[]\" value=\"".$row['NumberChange']."\"></td>\n";
				}
				// дата выдачи задания
				print "<td>";
				if ($row['DateExtraditionBVP'] == 0)
				{
					print "<Input Type=\"text\" Name=\"DateExtraditionBVP[]\"  class=\"date_input\"></td>\n";
				}
				else {
					print "<Input Type=\"text\" Name=\"DateExtraditionBVP[]\" value=\"".strftime("%d.%m.%Y",$row['DateExtraditionBVP'])."\" class=\"date_input\"></td>\n";
				}
				// дата выдачи задания заказчику
				print "<td>";
				if ($row['DateCustomer'] == 0)
				{
					print "<Input Type=\"text\" Name=\"DateCustomer[]\"  class=\"date_input\"></td>\n";
				}
				else {
					print "<Input Type=\"text\" Name=\"DateCustomer[]\" value=\"".strftime("%d.%m.%Y",$row['DateCustomer'])."\" class=\"date_input\"></td>\n";
				}
				//комментарий
				print "<td>";
				print "<Input Type=\"text\" Name=\"Comment[]\" value=\"".$row['Comment']."\"></td>\n";
			}
			print "</table>";
			print "<p></p>";
			print "<input type=\"submit\" name=\"btnadd\" value=\"add\" class='btnadd'/>";
			print "<input type=\"submit\" name=\"ok\" value=\"update\" class='btnupdate'/>";
			print "<input type=\"submit\" name=\"btncopy\"  value=\"copy\" class='btncopy' />";
			print "<input type=\"submit\" name=\"btndel\"   value=\"delete\" class='btndel' />";
		}
		$result = array(
		"id"					=>	$_REQUEST['hidden_mark'],
		"mark"					=>	$_REQUEST['mark'],
		"num_mark"				=>	$_REQUEST['NumMark'],
		"num_change"			=>	$_REQUEST['NumChange'],
		"date_extradition_BVP"	=>	$_REQUEST['DateExtraditionBVP'],
		"date_customer"	        =>	$_REQUEST['DateCustomer'],
		"comment"				=>	$_REQUEST['Comment']
		);
		//нажата кнопка добавления новой строки
		if (isset($_REQUEST['btnadd']))
		{
			return $result;
		}
		//нажата кнопка копирования
		if (isset($_REQUEST['btncopy']))
		{
			return $result;
		}
		//нажата кнопка копирования
		if (isset($_REQUEST['btndel']))
		{
			return $result;
		}
		//при сохранении результата в базе, создать супер массив, где объединятся все результаты
		if (isset($_REQUEST['ok']))
		{
			return $result;
		}
		//если никакая кнопка не нажата, прекрать выполнения скрипта
		if (empty($_REQUEST['ok']))
		{
			exit;
		}
	}
}
//******************************************************************************
//checkCloseProject() - проверка на соответствие вносимой даты по срокам выполнения проекта
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function checkCloseProject() //проверка на внесение работы с законченным проектом
{
	global $link,$errors;
	$Query = "SELECT DateCloseProject, Id FROM project WHERE Id=".escapeshellarg($_REQUEST['Number_Project']);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка $Query не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		$row = mysql_fetch_array($dbResult, MYSQL_BOTH);
		if (($row['DateCloseProject'] +3600 * 24 )< $_REQUEST['Date'] )
		{
			$errors[] = "<H3>Сроки выполнения проекта прошли!Внесение,изменение,удаление данных запрещено</H3>";
			//удаляем сообщение об ошибке для сметчиков (для занесения после срока) или если вносят Заявление
			if ($_SESSION['Name_Department'] == "ПСО" or $row['Id']==913) //id=913 Заявление
			{
				$tmp = array_pop($errors);
			}
		}
	}
}
//************************************************************************************************
// checkDateClose()- утвердить работу нельзя после окончания срока проектирования
// Входные параметры:
// Выходные параметры:
//************************************************************************************************

function checkDateClose()
{
	global $link, $errors;
	$Comma_separated = implode(",", $_REQUEST['design']);
	$Query = "SELECT DateCloseProject  FROM design, project
	WHERE
	design.id_Project = project.id AND
	design.Id IN ($Comma_separated)";
	if ( !($dbResult = mysql_query($Query,$link)) )
	{
		//print "Выборка не удалась!\n".mysql_error();
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		while ($row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			//если вносимая дата больше чем срок окончания работы, то возникает ошибка
			if ( $_REQUEST['Date'] > $row['DateCloseProject'] AND
			$_SESSION['Name_Post'] != "Администратор" AND
			//сметчики имеют право утверждать проекты после его срока
			$_SESSION['Name_Department'] != "ПСО")
			{
				$errors[]="<H3>Ошибка! Утвердить работу нельзя после даты окончания проекта! </H3>";
				break;
			}
		}
	}
}
//******************************************************************************
//processingError($msg, $file, $line, $func)  - выдача диагностического сообщения об ошибке
//Входные параметры:
//$msg - сообщение
//$file- путь к файлу
//$line - строка в которой произошла ошибка
//$func - функция в которой произошла ошибка
//Выходные параметры:
//******************************************************************************

function processingError($msg, $file, $line, $func)
{
	print "<p></p><div class=\"hint\"><span>Ошибка в функции $func:$line строка! </span> в файле $file</div>";
	//print "<br>   ";
	//print "<div class=\"hint\">$msg</div>";
	print "<p></p>";
}
//******************************************************************************
//listSession() - вывод данных о переменных сессии
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function listSession()
{
	print("<br>[Id_Employee]=".$_SESSION['Id_Employee']);
	print("<br>[Family]=".$_SESSION['Family']);
	print("<br>[Name]=".$_SESSION['Name']);
	print("<br>[Patronymic]=".$_SESSION['Patronymic']);
	print("<br>[Sanction]=".$_SESSION['Sanction']);
	print("<br>[Login]=".$_SESSION['Login']);
	print("<br>[Password]=".$_SESSION['Password']);
	print("<br>[Name_Department]=".$_SESSION['Name_Department']);
	print("<br>[Name_Office]=".$_SESSION['Name_Office']);
	print("<br>[Name_Post]=".$_SESSION['Name_Post']);
	print("<br>[LimitDate]=".$_SESSION['LimitDate']);
	print("<br>[CurrentDay]=".$_SESSION['CurrentDay']);
	print("<br>[CurrentMonth]=".$_SESSION['CurrentMonth']);
	print("<br>[CurrentYear]=".$_SESSION['CurrentYear']);
	print("<br>[pageMonth]=".$_SESSION['pageMonth']);
	print("<br>[pageYear]=".$_SESSION['pageYear']);
}
//******************************************************************************
//getSumTabelTime($Month, $Year, $Period) - получаем сумму табельного впремени за указанный период
//Входные параметры:
//$Month - указанный месяц
//$Year - указанный год
//$Period - указанный период
//Выходные параметры:  часы табеля
//******************************************************************************

function getSumTabelTime($Month, $Year, $Period, $flag = "all")
{
	global $link;
	/*$Query1 =   " SELECT SUM(Hour) AS Hour   ".
	" FROM tabel_time, day, month, year ".
	" WHERE ".
	" (tabel_time.id_Month = month.id) ".
	" AND (tabel_time.id_Day = day.id) ".
	" AND (tabel_time.id_Year = year.id) ";
	*/
	$Query1 =   " SELECT SUM(Hour) AS Hour   ".
	" FROM tabel_time ".
	" WHERE 1=1 ";
	if ($flag == "current")
	{
		//определим интервал дат по которым ведется поиск
		$QueryInterval =  getIntervalFromDate($Month, $Year, "tabel_time", "TimeStamp", $Period);
		$DateEnd = mktime(0, 0, 0, $_SESSION['CurrentMonth'], $_SESSION['CurrentDay'], $_SESSION['CurrentYear']);
		$QueryInterval = $QueryInterval." AND (tabel_time.TimeStamp<=".escapeshellarg($DateEnd).")";
	}
	else {
		//определим интервал дат по которым ведется поиск
		$QueryInterval =  getIntervalDate($Month, $Year, "tabel_time", "TimeStamp", $Period);
	}
	$Query = $Query1.$QueryInterval;
	//file_put_contents("query.txt",$Query);
	//результирующая таблица
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "<br />Выборка $Query не удалась!\n".mysql_error();
		//processingError(" ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		mysql_error();
		exit;
	}
	else {
		//результаты запроса заносим в массив, кэширование
		$row = mysql_fetch_array($dbResult, MYSQL_BOTH);
		return $row['Hour'];
	}
}
//******************************************************************************
//getListEmployeeTime() - получение списка сотрудников и табельных часов
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function getListEmployeeTime()
{
	//получение id выбранного отдела
	$QueryDepartment = selectDepartment();
	if ($_SESSION['Department'] == "Выбор отдела")
	{
		$arraytable = array ("design","sapr");
	}
	else {
		if ( getNameDepartment($_SESSION['Department'])  == "ИТ" )
		{
			$arraytable = array ("sapr");
		}
		else {
			$arraytable = array ("design");
		}
	}
	foreach ($arraytable as $index=>$value)
	{
		$Query1 = "SELECT SUM($value.Time) AS Time, ".
		"department.Name_Department, employee.id AS IdEmployee, employee.Family, employee.Name, employee.Patronymic, ".
		"employee.Tabel_Number, post.Name_Post ".
		"FROM $value, employee, department, post ".
		"WHERE ".
		"($value.id_Employee = employee.id) AND ".
		"(employee.id_Department = department.id) AND ".
		"(employee.id_Post = post.id)";
		$Sort = " GROUP BY IdEmployee ORDER BY BINARY employee.Family";
		//соединяем все запросы в один
		$QT[] = $Query1.$QueryDepartment.getIntervalDate(
		$_SESSION['lstMonth'],
		$_SESSION['lstYear'],
		$value,
		"Date",
		$_SESSION['rbPeriod']).
		$Sort;
	}
	if ( count($arraytable) > 1 )
	{
		$QueryTabel = "(".$QT[0].") UNION (".$QT[1].") ";
	}
	else {
		$QueryTabel = $QT[0];
	}
	//file_put_contents("query.txt",$QueryTabel);
	return  $QueryTabel;
}
//******************************************************************************
//getArrWorkTime($employee, $period) - получение  массива отработанного времени в рабочих табл.
//Входные параметры:
//$employee - ид человека
//$period - период календарный или отчетный
//Выходные параметры:  $arrayTime
//******************************************************************************

function getArrWorkTime($employee, $period)
{
	global $link;
	if ($_SESSION['Department'] == "Выбор отдела")
	{
		$arraytable = array ("design","sapr");
	}
	else {
		if ( getNameDepartment($_SESSION['Department'])  == "ИТ")
		{
			$arraytable = array ("sapr");
		}
		else {
			$arraytable = array ("design");
		}
	}
	//обработка исключения  проверка табеля
	if (is_file("checkTabel.php") AND $_SESSION['Name_Department'] == "ИТ")
	{
		$arraytable = array ("sapr");
	}
	foreach ($arraytable as $index=>$value)
	{
		$Query1 = "SELECT FROM_UNIXTIME($value.Date,'%d.%m.%y') AS Date1, SUM($value.Time) AS Time ".
		"FROM $value, employee ".
		"WHERE ".
		"($value.id_Employee = employee.id) AND ".
		"(employee.id = ".escapeshellarg($employee).") ";
		$Query2 = getIntervalDate($_SESSION['pageMonth'], $_SESSION['pageYear'], $value, "Date", $period);
		$Query3 = " GROUP BY Date1 ".
		" ORDER BY $value.Date";
		//соединяем все запросы в один
		$QT[] = $Query1.$Query2.$Query3;
	}
	if ( count($arraytable) > 1 )
	{
		$QueryTabel = "(".$QT[0].") UNION (".$QT[1].") ";
	}
	else {
		$QueryTabel = $QT[0];
	}
	if ( !($dbResult = mysql_query($QueryTabel, $link)) )
	{
		print "Выборка $QueryTabel не удалась!\n".mysql_error();
	}
	else {
		//заносим в массив день-заполненное время (месяц)
			while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			$arrayTime[$row['Date1']] = round($row['Time'],1);
		}
		return  $arrayTime;
	}
}
//******************************************************************************
//getArrDate($period) - получение массива рабочих часов  в периоде в таблице  Tabel_Time
//Входные параметры:    $period - календарный или отчетный период
//Выходные параметры:   $arrayTabelTime[$d.".".$m.".".$y] = отработанные часы в день
//******************************************************************************

function getArrDate($period)
{
	global $link;
	$Query = "SELECT Tabel_Time.Hour, Day.Day, Month.id AS Month,  Year.Year AS Year ".
	"FROM Tabel_Time, Year, Month, Day ".
	"WHERE ".
	"(Tabel_Time.id_Day = Day.id) AND ".
	"(Tabel_Time.id_Month = Month.id) AND ".
	"(Tabel_Time.id_Year = Year.id) ".
	getIntervalDate($_SESSION['pageMonth'], $_SESSION['pageYear'], "Tabel_Time", "TimeStamp", $period);
	//file_put_contents("query.txt",$Query);
	//результирующая таблица
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		print "Выборка $Query не удалась!\n".mysql_error();
	}
	else {
		//результаты запроса заносим в массив
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			if (strlen("{$row['Day']}")<2)
			{
				$d = str_pad("{$row['Day']}",2,"0",STR_PAD_LEFT);
			}
			else {
				$d = "{$row['Day']}";
			}
			if (strlen("{$row['Month']}")<2)
			{
				$m = str_pad("{$row['Month']}",2,"0",STR_PAD_LEFT);
			}
			else {
				$m = "{$row['Month']}";
			}
			$y = mb_substr("{$row['Year']}",2,2,'utf8');
			//день-табельное время (месяц)
				$arrayTabelTime[$d.".".$m.".".$y] = $row['Hour'];
		}
		return  $arrayTabelTime;
	}
}

//******************************************************************************
//check_dep_in_listDep($id_project, $id_department)- проверка на присутствие в списке предполагаемых отделов участв. в проекте  отдела с ид = $id_department
//Входные параметры:    $id_project - идентификатор проекта
//						$id_department - идентификатор отдела
//Выходные параметры:   
//******************************************************************************
function check_dep_in_listDep($id_project, $id_department)
{
	global $link;
	$Query = "select * from project where id=".escapeshellarg($id_project)." and listDep like '%$id_department%'";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		print "Выборка $Query не удалась!\n".mysql_error();
	}
	else 
	{

		$affectedRows = mysql_num_rows($dbResult);

		if($affectedRows > 0)
		{
			return TRUE; 	
		} 
		else
		{
			return FALSE;	
		} 
	}	
	
}
//******************************************************************************
//checkDateCloseProject_in_CurrentInterval($id_project, $IntervalProject)
//- проверка на окончание проекта с $id_project в указанный период
//Входные параметры:    $id_project - идентификатор проекта
//						$IntervalProject - интервал времени в котором проверяется окончание проекта
//Выходные параметры:   
//******************************************************************************
function checkDateCloseProject_in_CurrentInterval($id_project, $IntervalProject)
{
	global $link;	
	$Query = "SELECT id FROM project WHERE id=".$id_project." ".$IntervalProject;
	
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		print "Выборка $Query не удалась!\n".mysql_error();
	}
	else 
	{

		$affectedRows = mysql_num_rows($dbResult);

		if($affectedRows > 0)
		{
			return TRUE; 	
		} 
		else
		{
			return FALSE;	
		} 
	}	
}

//******************************************************************************
//setBorderStyle($aSheet, $cell_x, $cell, $count) - общая ф-ия которая форматирует ячейку Экселя-обрамляет тонкой рамкой 
//Входные параметры:    $aSheet - объект
//						$cell_x - название ячейки (А,В,С)
//						$cell - номер ячейки по у координате (1,2,3.....)
//						$count - наибольшее кол-во заданий или марок 
//Выходные параметры:   
//******************************************************************************
function setBorderStyle($aSheet, $cell_x, $cell, $count = 1)
{
		for($i = $cell; $i < $cell + $count; $i++)
		{
			$aSheet->getStyle($cell_x.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$aSheet->getStyle($cell_x.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$aSheet->getStyle($cell_x.$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$aSheet->getStyle($cell_x.$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);				
		}

}

//******************************************************************************
//checkSee() - проверка на доступ к просмотру
//Входные параметры:
//1-можно просмотреть, но не занести данные
//0-можно занести данные
//Выходные параметры:
//******************************************************************************
function  checkSee()
{
global $errors;
require_once('block.php');

if (defined("SEE"))    //если определена константа
{
 if (constant("SEE") === 1)
 {
   $errors[]="<H3>Возможен только просмотр без занесения и редактирования данных.</H3>";
 }
}
}

//******************************************************************************
//checkApprove() - регулирует возможность утверждения проектов начальниками отделов
//Входные параметры:
//TRUE-можно просмотреть утвердить данные
//FALSE - нельзя утвердить
//Выходные параметры:
//******************************************************************************
function  checkApprove()
{
global $errors, $link;

$query = 'SELECT approve FROM block LIMIT 1';

	if ( !($dbResult = mysql_query($query, $link)) )
	{
		print "Выборка $Query не удалась!\n".mysql_error();
	}
	else 
	{

		$affectedRows = mysql_num_rows($dbResult);

		if($affectedRows > 0)
		{
			$row = mysql_fetch_array($dbResult, MYSQL_BOTH);
			if($row['approve']=='FALSE')
			{
				$errors[]="<H3>Утверждение работ в данный период запрещено.</H3>";		
				return FALSE;		
			}	
			else
			{
				return TRUE;
			}
		} 
	}	

}

//******************************************************************************
//unsetSession() - уничтожение сессионных переменных
//Входные параметры:
//Выходные параметры:
//******************************************************************************

function unsetSession()
{
	unset($_SESSION['DateBegin']);
	unset($_SESSION['DateEnd']);
	unset($_SESSION['arrayMonth']);
	unset($_SESSION['rbPeriod']);
	unset($_SESSION['lstMonth']);
	unset($_SESSION['lstYear']);
	unset($_SESSION['lstObject']);
	unset($_SESSION['lstCustomer']);
	unset($_SESSION['Office']);
	unset($_SESSION['Department']);
	unset($_SESSION['rbCategry']);
	unset($_SESSION['rbCategryAdd']);
	unset($_SESSION['rbTypeReport']);
	unset($_SESSION['Smeta']);
	unset($_SESSION['SmetaCalc']);
	unset($_SESSION['K_Graph']);
	unset($_SESSION['K_Text']);
}

//******************************************************************************
//checkSession() - проверка на наличие в сессии пользователя
//Входные параметры:
//Выходные параметры:
//******************************************************************************
function checkSession()
{
    if(!isset($_SESSION['Id_Employee']))
    {   
//		print_r("<h1>Сессия пользователя = ".$_SESSION['Id_Employee']."</h1>");
		$path = '/PKO-SUBD/autorization1.php';
        header("Location:$path");
		exit;
    }

	
}
//******************************************************************************
//get_manager($id_progect) - получение фио гипа - менеджера по ид проекта, который он ведет
//Входные параметры: $id_progect - ид проекта
//Выходные параметры:
//******************************************************************************
function get_manager($id_progect)
{
	global $link;
	$query = "SELECT Manager FROM project WHERE id=".escapeshellarg($id_progect). " LIMIT 1";
	if ( !($dbResult = mysql_query($query, $link)) )
	{
		print "Выборка $query не удалась!\n".mysql_error();
	}
	else {
 		$row = mysql_fetch_array($dbResult, MYSQL_BOTH);
		$result = getNameEmployee_short($row['Manager']);
		return $result;
	}		
}

//******************************************************************************
// set_time_interval($DateBegin, $DateEnd) - ф-ия установления границ пользовательского интервала времени
//Входные параметры: $DateBegin, $DateEnd -начальная и конечная дата
//Выходные параметры:$arrayInterval массив из 2х элементов - нач.и конеч.даты
//******************************************************************************
function set_time_interval($DateBegin, $DateEnd)
{
		//если хотя бы одна динамическая дата есть, то вторая будет текущей датой
	if ( empty($DateBegin) AND !empty($DateEnd))
	{
		//если поле начальной даты пустое то заполняем его текущей датой
		$arrayInterval[0] = mktime();
		$arrayInterval[1] = notCheckInputDate($DateEnd);
	}
	elseif ( !empty($DateBegin) AND empty($DateEnd) )
	{
		//если поле конечной даты пустое то заполняем его текущей датой
		$arrayInterval[0] = notCheckInputDate($DateBegin);
		$arrayInterval[1] = mktime();
	}
	elseif (!empty($DateBegin) AND !empty($DateEnd))
	{
		$arrayInterval[0] = notCheckInputDate($DateBegin);
		$arrayInterval[1] = notCheckInputDate($DateEnd);
	}
	
	return $arrayInterval;
}

//******************************************************************************
//checkTrueTime($DateBegin, $DateEnd) - ф-ия проверки даты на предмет неопережения конечной даты перед начальной 
//Входные параметры: $DateBegin, $DateEnd -начальная и конечная дата
//Выходные параметры:
//******************************************************************************
function checkTrueTime($DateBegin, $DateEnd)
{
	global $errors;
	if($DateBegin > $DateEnd)
	{
		$errors[]="<H3>Начальная дата больше конечной!</H3>";		
	}
	
}

?>