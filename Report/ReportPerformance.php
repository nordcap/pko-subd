<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.3.9
+-----------------------------------------------------------------------+
| Copyright (c) 2013 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей отчета "Производительность подразделения"
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
include_once("../modules/lib/lib.php");
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Автоматизированная система учета трудозатрат</title>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; CHARSET=utf-8">
<link  rel="stylesheet" type="text/css" href="../css/framework.css">
<link  rel="stylesheet" type="text/css" href="../css/user_1.css">
<link href="../favicon.ico" rel="icon" type="image/x-icon" />
<!--<script language="JavaScript" src="../js/jquery.js" type="text/javascript"></script>-->
<!--<script language="JavaScript" src="../js/jquery.date_input.js" type="text/javascript"></script>-->
<!--<script language="JavaScript" src="../js/jscript.js" type="text/javascript"></script>-->
<!--<script type="text/javascript">$($.date_input.initialize);</script>-->
</head>
<body>
<?php
//include_once("../js/jscript.js");
print "<a href=\"object.php\">Назад</a><br />\n";


print "<p><b>Отдел:</b>&nbsp;";
if ( $_SESSION['Department'] != "Выбор отдела" )
{
	print (getNameDepartment($_SESSION['Department'])."<br />");
}
print "</p>";
print "<p>";

if ( $_SESSION['lstYear'] != "Выберите год" )
{
	print "<b>Год:</b>&nbsp;";
	print ($_SESSION['lstYear']."<br />");
}
print "</p>";

print "<p>";
	if( $_SESSION['lstObject'] == "Выбор объекта" )
	{
		print "Все объекты";
	}
	else {
		print "<b>".getNameProject($_SESSION['lstObject'])."</b>";
        print "     <U>".getNameExtProject($_SESSION['lstObject'])."</U>";
	}
print "</p>";

switch ($_SESSION['rbCategry'])
{
	case "time":
		$QueryDate = getIntervalDate(
		'Выберите месяц',
		$_SESSION['lstYear'],
		"design",
		"Date",
		$_SESSION['rbPeriod']);
		$mon = "Date";

	break;
	case "man_hour":
		$QueryDate = getIntervalDate(
		'Выберите месяц',
		$_SESSION['lstYear'],
		"design",
		"statusBVP",
		$_SESSION['rbPeriod']);
		$mon = "statusBVP";
	break;
}

if ( $_SESSION['lstObject'] == "Выбор объекта" )
{
	$QueryObject = "";
}
else {
	$QueryObject = " AND (project.Id=".escapeshellarg($_SESSION['lstObject']).")";
}


//выбрать всех сотрудников, входящих в указанный отдел
$QueryEmployee = "select employee.Id AS FIO, employee.Family, employee.Name, employee.Patronymic, sum(design.Man_Hour_Sum) 
from  design, employee, department, project
where (department.Id = employee.id_Department) AND
(design.id_employee=employee.Id) AND
(design.id_Project=project.Id) AND
(design.statusBVP>0) AND 
(department.Id<>19) AND 
(project.Number_Project <> 'Заявление')";


//запрос отдела
$QueryDepartment = selectDepartment();


$GroupBy = " GROUP BY employee.id ORDER BY BINARY employee.Family";

//объединение запроса в 1 запрос
$Query = $QueryEmployee.$QueryObject.$QueryDate.$QueryDepartment.$GroupBy;


	print "<table class='f-table-zebra'>\n";
    print "<caption>Производительность сотрудников</caption>";
	print "<thead>\n";
	
	print "<TR>\n";
	print "<TH rowspan=2>№№</TH>".
	"<TH rowspan=2>Сотрудник</TH>".
	"<TH colspan=12>Месяцы</TH>".
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
	print "</thead>";
	print "<tbody>\n";

if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//print "Выборка $Query не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			$count = 1;
			while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
			{
				print "<tr>";
				print "<td>".$count++."</td>";
				print "<td>".getNameEmployee_short($row['FIO'])."</td>";
				
				for ($month = 1; $month < 13; $month++)
				{	
					$value = round(getManHourByProjectEmployee($row['FIO'], 
															$QueryObject,
															getIntervalDate(
																			$month,
																			$_SESSION['lstYear'],
																			"design",
																			$mon,
																			$_SESSION['rbPeriod']
																			)),0);
					printZeroTD($value);	
					$sum_value[$month][$row['FIO']] = $value;
					
				}
				print "</tr>";

			}
			print "<tr>";
			print "<td colspan='2'>Итого";
			foreach($sum_value as $k=>$v)
			{
				printZeroTD(array_sum($v));
			}
			print "</tr>";
		}
			
	print "</tbody>\n";
	print "</table>\n";

	?>
</body>
</html>