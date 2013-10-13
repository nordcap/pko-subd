<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей печати геодезических работ за месяц и год
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
include_once("../modules/lib/lib.php");
require_once("begin1.php");
//include_once("../js/jscript.js");
print "<a href=\"geod.php\" title=\"Поиск\">Назад</a>\n";
print "<br /><br />";
$Query2 =  getIntervalDate($_SESSION['lstMonth'],$_SESSION['lstYear'],"geod","Date",$_SESSION['rbPeriod']);

$Query1 = "SELECT  geod.Id, geod.Date, geod.Name_Work, geod.Unit_Measurement, ".
"geod.Category, geod.Price, geod.Number, geod.Amount, geod.Coefficient, geod.Comment, geod.Time, ".
"geod.Total, geod.id_Employee, geod.DateSubmit, ".
"employee.Family, employee.Name, employee.Patronymic, project.Number_Project, customer.Name_Customer, tender_geod.Number_Tender ".
"FROM geod, employee, project, customer, tender_geod ".
"WHERE ".
"(geod.id_Employee = employee.id) AND ".
"(geod.id_Project = project.id) AND ".
"(geod.id_Tender = tender_geod.id) AND ".
"(project.id_Customer = customer.id)";
$Query3 = " ORDER BY geod.Date DESC";
//результирующий запрос
$Query = $Query1.$Query2.$Query3;
//file_put_contents("query.txt",$Query);  
//рисуем таблицу
//результирующая таблица
if ( !($dbResult = mysql_query($Query, $link)) )
{
	//print "Выборка $Query не удалась!\n";
	processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	exit;
}
else {
	print "<table class=\"f-table-zebra\">\n";
	print "<caption>Отчет о выполнении работ за ".getMonthFromId($_SESSION['lstMonth'])." ".$_SESSION['lstYear']."г.</caption>\n";
    print "<thread>\n";
	print "<TR>\n";
	print "<TH>Дата</TH>\n";
	print "<TH>№ проекта</TH>\n";
	print "<TH>№ заявки</TH>\n";
	print "<TH>Заказчик (цех)</TH>\n";
	print "<TH>Наименование выполненных работ</TH>\n";
	print "<TH>Трудозатраты <br>чел./час</TH>\n";
	print "<TH>Стоимость<br>(без НДС),руб.</TH>\n";
	print "</TR>\n";
    print "</thread>\n";

	$TotalMoney = 0;
	$TotalTime = 0;
	while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
	{
		print "<TR align=center>\n";
		print "<TD>".strftime("%d.%m.%y",$row['Date'] + $_SESSION['localTime'] * 3600)."</TD>\n";
		printZeroTD($row['Number_Project']);
		printZeroTD($row['Number_Tender']);
		print "<TD nowrap align=left>".$row['Name_Customer']."</TD>";
		print "<TD nowrap align=left>".$row['Name_Work']."</TD>";
		printZeroTD(round($row['Time'],1));
		printZeroTD($row['Time']*287); //стоимость работ
		$TotalMoney = $TotalMoney + $row['Time']*287;
		$TotalTime = $TotalTime + $row['Time'];
	}
	print "<TR>\n";
	print "<TH colspan=5>Итого:</TH>";
	printZeroTH(round($TotalTime,0));
	printZeroTH($TotalMoney);
	print "</table>\n";
}
?>
</body>
<script  type="text/javascript">
$(document).ready(function(){
     $('.f-table-zebra tr>td:nth-child(5)').css('text-align', 'left');
  });
</script>
</html>