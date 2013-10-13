<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2011 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей отчета "Табель учета рабочего времени"
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
include_once("../modules/lib/lib.php");
require_once("begin1.php");
include_once("../js/jscript.js");
print "<a href=\"object.php\">Назад</a><br />\n";
print "<H2>Табель учета рабочего времени</H2><br />\n";
print "<Hr Width=\"100%\" ALIGN=\"left\"><br />\n";
if ( $_SESSION['Department'] != "Выбор отдела" )
{
	print "<b>Отдел:</b>&nbsp;";
	print (getNameDepartment($_SESSION['Department'])."<br />");
}
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
print "<b>Период:</b>&nbsp;";
print ($_SESSION['rbPeriod']."<br />");
print "<b>Плановое время:</b>&nbsp;";
$SumTabelTime = getSumTabelTime($_SESSION['lstMonth'],$_SESSION['lstYear'],$_SESSION['rbPeriod']);
print round($SumTabelTime, 1)." ч.<br>";
//получаем перечень дней в отчетном периоде в массив $arrListDay
/*  $QueryListDay = "SELECT id_Day, Hour FROM tabel_time WHERE 1=1 ".
getIntervalDate($_SESSION['lstMonth'],$_SESSION['lstYear'],"tabel_time","TimeStamp",$_SESSION['rbPeriod']);
if ( !($dbResult = mysql_query($QueryListDay, $link)) )
{
	//print "<br />Выборка $QueryTabel не удалась!\n".mysql_error();
	processingError("$QueryListDay ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
}
else {
	while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
	{
		$arrListDay[$row["id_Day"]] = $row["Hour"];
	}
	}*/
//получаем массив [день]=>[час]
$arrListDay = getArrDate($_SESSION['rbPeriod']);
//создаем результирующий запрос
$QueryTabel = getListEmployeeTime();
//file_put_contents("query.txt",$QueryTabel);
if ( !($dbResult = mysql_query($QueryTabel, $link)) )
{
	//print "<br />Выборка $QueryTabel не удалась!\n".mysql_error();
	processingError("$QueryTabel ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
}
else {
	//таблица HTML
	print "<TABLE  Border=\"2\" CellSpacing=\"0\" CellPadding=\"2\">\n";
	print "<Caption><h4>Табель учета рабочего времени</h4></Caption>\n";
	print "<TR class=\"header\" bgcolor=#E8E8E8 align=center>\n";
	print "<TH rowspan=2>Таб.№</TH>\n";
	print "<TH rowspan=2>Ф.И.О.</TH>\n";
	print "<TH colspan=".count($arrListDay).">{$_SESSION['Month'][$_SESSION['lstMonth']]} {$_SESSION['lstYear']} г.</TH>\n";
	print "<TH rowspan=2>Итого</TH>\n";
	print "</TR>\n";
	print "<TR class=\"header\" bgcolor=#E8E8E8 align=center>\n";
	foreach($arrListDay as $day=>$hour)
	{
		if ($hour == 7.2)
		{
			print "<TH bgcolor=\"#ffe400\">".substr($day,0,2)."</TH>";
		}
		elseif ($hour == 0)
		{
			print "<TH bgcolor=\"#ff0000\">".substr($day,0,2)."</TH>";
		}
		else{
			printZeroTH(substr($day,0,2));
		}
	}
	print "</TR>\n";
	while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
	{
		print "<TR align=center>\n";
		printZeroTD($row['Tabel_Number']);
		print "<TD nowrap align=left>{$row['Family']} ".
		mb_substr($row['Name'],0,1,'utf8').".".
		mb_substr($row['Patronymic'],0,1,'utf8').
		".</TD>";
		$arrHour = getArrWorkTime($row['IdEmployee'], $_SESSION['rbPeriod']);
		foreach($arrListDay as $day=>$hour)
		{
			$flag = FALSE;
			foreach( $arrHour as $date=>$work_time)
			{
				//если дни совпадают, то отображаем суммарное время дня
				if ( $day == $date )
				{
					printZeroTD($work_time);
					$flag = TRUE;
					break;
				}
			}
			if ($flag == FALSE)
			{
				printZeroTD();
			}
		}
		$sum = array_sum($arrHour);
		//если разница меньше 1,&nbsp;то прощаем косяки
		if (abs($sum - round($SumTabelTime, 1)) < 1)
		{
			print("<TD bgcolor=\"#99FF99\">".$sum."</TD>"); //зеленый-совпадает с плановым
			} else {
			print("<TD bgcolor=\"#FF7F50\">".$sum."</TD>"); //красный- не совпадает с плановым
		}
	}
}
require_once("../end1.php");
?>
