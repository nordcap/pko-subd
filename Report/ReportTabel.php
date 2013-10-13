<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей отчета "Табельное время"
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
include_once("../modules/lib/lib.php");
require_once("begin1.php");
include_once("../js/jscript.js");
print "<a href=\"object.php\">Назад</a><br />\n";
print "<H2>Сравнение табеля и фактического времени</H2><br />\n";
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
//находим табельное время
$SumTabelTime = round(getSumTabelTime($_SESSION['lstMonth'],
$_SESSION['lstYear'],
$_SESSION['rbPeriod']), 1);
print "<b>Месяц:</b>&nbsp;".$SumTabelTime;
//находим табельное время от начала периода
$SumCurrentTabelTime = round(getSumTabelTime($_SESSION['lstMonth'],
$_SESSION['lstYear'],
$_SESSION['rbPeriod'],
"current"), 1);
//результирующий запрос
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
	print "<Caption><H4>Проверка табельного времени</Caption>\n";
	print "<TR class=\"header\" bgcolor=#E8E8E8 align=center>\n";
	print "<TH>№</TH>\n";
	print "<TH>ФИО</TH>\n";
	print "<TH>Отдел</TH>\n";
	print "<TH>Табельное время<br /> от начала периода</TH>\n";
	print "<TH>Заполненное <br /> время</TH>\n";
	print "<TH>Разность</TH>\n";
	print "</TR>\n";
	$SumTabel = 0;
	$SumTime = 0;
	$n = 1;
	while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
	{
		print "<TR align=center>\n";
		print "<TD>".$n++."</TD>";
		print "<TD nowrap align=left>{$row['Family']} ".
		mb_substr($row['Name'],0,1,'utf8').".".
		mb_substr($row['Patronymic'],0,1,'utf8').
		".</TD>";
		//отдел
		printZeroTD($row['Name_Department']);
		//табельное время от начала периода
		if ($SumCurrentTabelTime > $SumTabelTime)
		{
			printZeroTD($SumTabelTime);
			$Tabel = $SumTabelTime;
		}
		else {
			printZeroTD($SumCurrentTabelTime);
			$Tabel = $SumCurrentTabelTime;
		}
		//заполненное время
		printZeroTD(round($row['Time'],1));
		//разница между табельным временем и заполненным
		$Raznost = round($Tabel - $row['Time'],1);
		//если разность табельного времени и заполненного положительна, значит табельное время нужно распределить между работами
		if ($Raznost > 0)
		{
			//выделяем ошибку красным
			printZeroTD($Raznost, "red");
		}
		else {
			//иначе все нормально (затратить время можно больше табкльного-напр. домашняя работа)
				printZeroTD($Raznost, "black");
		}
		//суммация табельного времени
		$SumTabel = $SumTabel + $Tabel;
		//суммация заполненного времени
		$SumTime =  $SumTime +  $row['Time'];
	}
	print "<TR align=center>\n";
	print "<TH colspan=3>Итого:</TH>";
	printZeroTH(round($SumTabel,1));
	printZeroTH(round($SumTime,1));
	//разность между табельным и заполненным временем
	$SumRaznost =  round($SumTabel -  $SumTime,1);
	if ($SumRaznost > 0)
	{
		//выделяем ошибку красным
		printZeroTH($SumRaznost, "red");
	}
	else {
		//иначе все нормально (затратить время можно больше табкльного-напр. домашняя работа)
			printZeroTH($SumRaznost, "black");
	}
}
require_once("../end1.php");
?>
