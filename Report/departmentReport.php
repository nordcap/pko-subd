<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей отчета "свод выработки по подразделениям"
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
error_reporting(E_ERROR & ~E_NOTICE );
session_start();
include_once("../modules/lib/lib.php");
require_once("begin1.php");
include_once("../js/jscript.js");
//инициализация массива времени
print "<a href=\"object.php\">Назад</a><br />\n";
print "<H2>Свод выработки подразделений по картам учета рабочего времени</H2><br />\n";
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
HeadTable();
print "<p></p>";

//выбрать всех сотрудников, входящих в указанный отдел
$QueryEmployee = "SELECT employee.id AS FIO, employee.Family, employee.Name, employee.Patronymic, employee.Status, ".
"employee.StatusMonth, employee.StatusYear, ".
"office.Name_Office, department.Name_Department, post.Name_Post ".
"FROM employee,department,office,post ".
"WHERE ".
"(department.Id = employee.id_Department) AND ".
"(office.Id = employee.id_Office) AND ".
"(employee.id_Post = post.id) AND ".
"((employee.StatusMonth >=".escapeshellarg($_SESSION['pageMonth'])." AND ".
"employee.StatusYear >=(SELECT id FROM Year WHERE Year=".escapeshellarg($_SESSION['pageYear']).")) OR ".
"(employee.StatusMonth = ".escapeshellarg('0')." AND ".
"employee.StatusYear = ".escapeshellarg('0').")) ";
//        	"(employee.Status = ".escapeshellarg('TRUE').")";  //не уволен, иначе будут отображаться все, кто работал

$Query1 = "SELECT employee.id, employee.Family, employee.Name, employee.Patronymic, ".
"design.statusBVP, design.offPlan, design.Date, ".
"office.Name_Office, department.Name_Department, ".
"SUM(design.Man_Hour_Sum) AS sumManHourSum ".
"FROM design,employee,department,office ".
"WHERE ".
"((design.id_Employee = employee.Id) AND ".
"(department.Id = employee.id_Department) AND ".
"(office.Id = employee.id_Office)) ";
if ( $_SESSION['Office'] == "Выбор бюро" )
{
	$QueryOffice = "";
}
else {
	$QueryOffice = " AND (office.Id=".escapeshellarg($_SESSION['Office']).")";
}
//запрос отдела
$QueryDepartment = selectDepartment();
//в зависимости от выбора группирования меняем запрос
switch ($_SESSION['rbCategry'])
{
	case "Employee":
	$QueryGroupBy = " GROUP BY employee.id";
	$case = "Employee";
	break;
	case "Office":
	$QueryGroupBy = " GROUP BY office.id";
	$case = "Office";
	break;
	case "Department":
	$QueryGroupBy = " GROUP BY department.id";
	$case = "Department";
	break;
}
$QueryOrderBy = " ORDER BY BINARY employee.Family";
$QueryMan = $QueryEmployee.$QueryDepartment.$QueryOffice.$QueryGroupBy.$QueryOrderBy;
//file_put_contents("query.txt",$QueryMan);
if ( !($dbResultMan = mysql_query($QueryMan, $link)) )
{
	//print "Выборка $QueryMan не удалась!\n".mysql_error();
	processingError("$QueryMan ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
}
else {
	//таблица HTML
	print "<TABLE  Border=\"2\" CellSpacing=\"0\" CellPadding=\"2\">\n";
	print "<TR bgcolor=#E8E8E8 align=center>\n";
	print "<TH>№</TH>";
	//в зависимости от выбранного переключателя таблица также будет изменяться
	switch ($case)
	{
		case  "Employee":
		print "<TH>Ф.И.О.</TH>";
		break;
		case "Office":
		print "<TH>бюро</TH>";
		break;
		case "Department":
		print "<TH>Отдел</TH>";
		break;
	}
	print "<TH>Общие трудозатраты<br />чел/час</TH>";
	print "<TH>Сдано в БВП<br />чел/час</TH>";
    if($_SESSION['Department'] == "19"){
      print "<TH>Вне плана<br />чел/час</TH>";
    }
	print "<TH>Плановое время<br />чел/час</TH>";
	print "<TH>% выполнения<br>плана</TH>";
	print "</TR>";
	$n = 0; //счетчик
	$sum = 0;
	$sumBVP = 0;
	$sumHour = 0;
    $sumOffPlan = 0;
	while ( $rowMan = mysql_fetch_array($dbResultMan, MYSQL_BOTH) )
	{
		$n++;
		print "<TR align=center>\n";
		print "<TD>".$n."</TD>";
		switch ($case)
		{
			case  "Employee":
			print "<TD nowrap align=left>{$rowMan['Family']} ".mb_substr($rowMan['Name'],0,1,'utf8').".".mb_substr($rowMan['Patronymic'],0,1,'utf8').".</TD>";
			$Family = " AND (employee.id=".escapeshellarg($rowMan['FIO']).")";

			$department = "";
			break;
			case "Office":
			print "<TD>{$rowMan['Name_Office']}</TD>";
			$department =  " AND (office.Name_Office=".escapeshellarg($rowMan['Name_Office']).")";
			$Family = "";
			break;
			case "Department":
			print "<TD>{$rowMan['Name_Department']}</TD>";
			$department = " AND (department.Name_Department=".escapeshellarg($rowMan['Name_Department']).")";
			$Family = "";
			break;
		}

		//ищем трудозатраты конкретного работника
		$Query = $Query1.$department.$Family.getIntervalDate(
		$_SESSION['pageMonth'],
		$_SESSION['pageYear'],
		"design",
		"Date",
		$_SESSION['rbPeriod']).
		$QueryGroupBy;
		//print ($Query."<br />");
		//file_put_contents("query.txt",$Query);
		if ( !($dbResultHour = mysql_query($Query, $link)) )
		{
			//print "Выборка $Query не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			$Hour = 0;
			/*			  	   	while ( $rowHour = mysql_fetch_array($dbResultHour, MYSQL_BOTH) )
			{
				$Hour = $rowHour['sumManHourSum'];
			}
			*/
			$rowHour = mysql_fetch_array($dbResultHour, MYSQL_BOTH);
			$Hour = $rowHour['sumManHourSum'];
			printZeroTD(round($Hour,1));
		}
		//ищем утвержденные трудозатраты работника
		$QueryBVP = $Query1.$department.$Family.getIntervalDate(
		$_SESSION['pageMonth'],
		$_SESSION['pageYear'],
		"design",
		"statusBVP",
		$_SESSION['rbPeriod']).
		$QueryGroupBy;
		//				print ($QueryBVP."<br />");
		//file_put_contents("query.txt",$QueryBVP);
		if ( !($dbResultBVP = mysql_query($QueryBVP, $link)) )
		{
			//print "Выборка $QueryBVP не удалась!\n".mysql_error();
			processingError("$QueryBVP ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			$HourBVP = 0;
			$rowBVP = mysql_fetch_array($dbResultBVP, MYSQL_BOTH);
			$HourBVP = $rowBVP['sumManHourSum'];
			printZeroTD(round($HourBVP,1));
		}


        //только для сметного отдела
            $HourOffPlan = 0;
            if($_SESSION['Department'] == "19"){

            		$QueryOffPlan = $Query1.$department.$Family.getIntervalDate(
            		$_SESSION['pageMonth'],
            		$_SESSION['pageYear'],
            		"design",
            		"offPlan",
            		$_SESSION['rbPeriod']).
            		$QueryGroupBy;

            		//file_put_contents("query.txt",$QueryBVP);
            		if ( !($dbResultPlan = mysql_query($QueryOffPlan, $link)) )
            		{
            			//print "Выборка $QueryBVP не удалась!\n".mysql_error();
            			processingError("$QueryOffPlan ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
            		}
            		else {
            		    $rowOffPlan = mysql_fetch_array($dbResultPlan, MYSQL_BOTH);
            			$HourOffPlan = $rowOffPlan['sumManHourSum'];
            			printZeroTD(round($HourOffPlan,1));
                        }


            }


		//найдем откорректированный план для человека
		$QueryCorrection = "SELECT Correction FROM plan_employee  WHERE ".
		"id_Employee = ".escapeshellarg($rowMan['FIO'])." AND ".
		"id_Year=(SELECT id FROM Year WHERE Year.Year=".escapeshellarg($_SESSION['pageYear']).") AND ".
		"id_Month = ".escapeshellarg($_SESSION['pageMonth']);
		

		//file_put_contents("query.txt",$QueryCorrection);
		if ( !($dbResultCorrection = mysql_query($QueryCorrection, $link)) )
		{
			//print "Выборка $QueryCorrection не удалась!\n".mysql_error();
			processingError("$QueryCorrection ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			//если нач.отдела заполнил plan_employee, то ищем корректировку плана в ней
			if (mysql_num_rows($dbResultCorrection) > 0)
			{
				$rowCorrection = mysql_fetch_array($dbResultCorrection, MYSQL_BOTH);
				printZeroTD($rowCorrection['Correction']);
				//Столбец процентов от месячного плана
				@printZeroTD(round(100*($HourBVP+ $HourOffPlan)/$rowCorrection['Correction'],1));
			}
			else {
				//если не найдены записи в табл. plan_employee план берется по умолчанию
				//плановое время для начальства = 0
				if ($rowMan['Name_Post'] == "Начальник отдела" OR
				$rowMan['Name_Post'] == "Зам.начальника отдела" OR
				$rowMan['Name_Post'] == "Начальник бюро" OR
				$rowMan['Name_Post'] == "Старший специалист" OR
				$rowMan['Name_Post'] == "Старший технолог" OR
				$rowMan['Name_Post'] == "Старший механик" )
				{
					printZeroTD();
					printZeroTD();
				}
				else {
					printZeroTD(round($_SESSION['Plan_One'],1));
					@printZeroTD(round(100*($HourBVP + $HourOffPlan)/$_SESSION['Plan_One'],1));
				}
			}
		}
		$sumBVP = $sumBVP + $HourBVP;
        $sumOffPlan = $sumOffPlan + $HourOffPlan;
		$sum = $sum + $Hour;
	}
	print "<TR align=center>\n";
	print "<TD colspan=2 align=right><b>Итого:</b></TD>";
	printZeroTH(round($sum,1));
	printZeroTH(round($sumBVP,1));
    if($_SESSION['Department'] == "19"){
	        printZeroTH(round($sumOffPlan, 1));
    }
	printZeroTH();
	printZeroTH();
	print "<TR align=center>\n";
	print "<TD colspan=2 align=right><b>Коэффициент:</b></TD>";
	printZeroTH(@round($sum/$_SESSION['Plan_All'],2));
	printZeroTH(@round($sumBVP/$_SESSION['Plan_All'],2));
    if($_SESSION['Department'] == "19"){
	    printZeroTH(@round($sumOffPlan/$_SESSION['Plan_All'],2));
    }
	printZeroTH();
	printZeroTH();
	print "</TABLE>\n";
}
print "<br /><br />";
print "<Table   Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\">\n";
print "<Tr>\n";
print "<Td>";
print "Начальник бюро: ";
print "<Td>";
print "<br />";
print "<Hr Width=\"250\" ALIGN=left>\n";
print "<br />";
print "<Tr>\n";
print "<Td>";
print "Начальник отдела: ";
print "<Td>";
print "<br />";
print "<Hr Width=\"250\" ALIGN=left>\n";
print "<br />";
print "</Table>\n";
require_once("../end1.php");