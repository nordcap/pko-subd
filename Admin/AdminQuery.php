<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей запроса к БД, с фиксированным выводом
на экран данных по работам сотрудников
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
include_once("../modules/lib/lib.php");
checkSession(); //проверка на присутствие в сессии идентификатора пользователя
require_once("begin1.php");
include_once("../js/jscript.js");
if ($_SESSION['Name_Post']=="Администратор")
{
	print "<H2>Модуль выполнения запроса к Базе данных</H2><br />\n";
	print "<a href=\"Admin.php\">Страница администрирования</a><br />\n";
	print ("<Hr Width=\"100%\" ALIGN=\"left\"><br />\n");
	//если нажата кнопка Запрос,то
	if (isset($_REQUEST['hidden']))
	{
		$errors=array(); //объявляется массив ошибок
		if(count($errors)>0)
		{
			//если есть ошибки, заносим их во временные переменные переменные
			$tmpAdminQuery=$_REQUEST['AdminQuery'];
		}
	}
	print ("<form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\">\n");
	print "<Input Type=\"text\" Name=\"AdminQuery\" Value=\"$tmpAdminQuery\" Size=200>\n";
	print("<br />");
	print "Послать запрос\n";
	print ("<input type=\"submit\" value=\"ОК\"> \n<br />");
	print("<input name=\"hidden\" type=\"hidden\" value=\"data\"><br />");
	print("</form>");
	if (isset($_REQUEST['AdminQuery']))
	{
		if (!($dbResult=mysql_query($_REQUEST['AdminQuery'],$link)))
		{
			print("<H3><br />Запрос не удался!\n</H3>");
		}
		else
		{
			print("<H3><br />Запрос удался!\n</H3>");
		}
		print("<br />Query=".$_REQUEST['AdminQuery']);
		print("<pre>");
		print("<br />Число записей=".mysql_affected_rows());
		print("</pre>");
		//отображения запроса на экране
		//таблица HTML
		print("<TABLE Width=\"100%\" Border=\"3\" CellSpacing=\"0\" CellPadding=\"2\">\n");
		print " <Caption><H4>Объектная карта</Caption>\n";
		//переделал таблицу
		print ("<TR align=center>\n");
		print ("<TH rowspan=2>Id</TH>\n");
		print ("<TH rowspan=2>Дата</TH>\n");
		print ("<TH rowspan=2>Сотрудник</TH>\n");
		print ("<TH rowspan=2>Номер объекта</TH>\n");
		print ("<TH rowspan=2>Номер чертежа</TH>\n");
		print ("<TH rowspan=2>Марка</TH>\n");
		print ("<TH colspan = 3>Графическая часть</TH>\n");
		print ("<TH colspan = 6>Текстовая часть</TH>\n");
		print ("<TH rowspan = 2>Сбор данных</TH>\n");
		print ("<TH rowspan=2>Согл.-ие</TH>\n");
		print ("<TH rowspan=2>Проверка</TH>\n");
		print ("<TH rowspan=2>Итого часов</TH>\n");
		print ("<TH rowspan=2>Итого чел.-час</TH>\n");
		print ("<TH rowspan=2>Сдача в БВП</TH>\n");
		print("</TR>\n");
		print ("<TR align=center>\n");
		print("<TD>Лист А1</TD>\n");
		print("<TD>Факт. время</TD>\n");
		print("<TD>Труд.-ты</TD>\n");
		print("<TD>Лист А3</TD>\n");
		print("<TD>Факт. время</TD>\n");
		print("<TD>Труд.-ты</TD>\n");
		print("<TD>Лист А4</TD>\n");
		print("<TD>Факт. время</TD>\n");
		print("<TD>Труд.-ты</TD>\n");
		print("</TR>\n");
		//переделал таблицу
		$Sheet_A1=0;
		$Time1=0;
		$Sheet_A3=0;
		$Time3=0;
		$Sheet_A4=0;
		$Time4=0;
		$Time_Collection=0;
		$Time_Agreement=0;
		$prov=0;
		$Time=0;
		$ManHourSum=0;
		while ($row=mysql_fetch_array($dbResult,MYSQL_BOTH))
		{
			print("<TR align=center>\n");
			print("<TD>{$row['0']}</TD>".
			"<TD>".strftime("%d.%m.%y",$row['Date'] + $_SESSION['localTime'] * 3600)."</TD>".
			"<TD nowrap align=left>{$row['Family']} ".substr($row['Name'],0,1).".".substr($row['Patronymic'],0,1).".</TD>".
			"<TD align=left>{$row['Number_Project']}</TD>".
			"<TD nowrap>{$row['num_cher']}</TD>".//Добавил Ден
			"<TD>{$row['Name_Mark']}</TD>".
			"<TD>{$row['Sheet_A1']}</TD>".
			"<TD>{$row['Time1']}</TD>".
			"<TD>{$row['Man_Hour1']}</TD>".
			"<TD>{$row['Sheet_A3']}</TD>".
			"<TD>{$row['Time3']}</TD>".
			"<TD>{$row['Man_Hour3']}</TD>".
			"<TD>{$row['Sheet_A4']}</TD>".
			"<TD>{$row['Time4']}</TD>".
			"<TD>{$row['Man_Hour4']}</TD>".
			"<TD>{$row['Time_Collection']}</TD>".
			"<TD>{$row['Time_Agreement']}</TD>".
			"<TD>{$row['prov']}</TD>".//Добавил Ден
			"<TD>{$row['Time']}</TD>".
			"<TD>{$row['Man_Hour_Sum']}</TD>\n");
			if ($row['checkBVP']=="TRUE")
			{
				print ("<TD> сдано </TD>\n");
			}
			else {
				print ("<TD> нет </TD> \n");
			}
			$Sheet_A1=$Sheet_A1+round($row['Sheet_A1'],3);
			$Sheet_A3=$Sheet_A3+round($row['Sheet_A3'],3);
			$Sheet_A4=$Sheet_A4+round($row['Sheet_A4'],3);
			$Time1=$Time1+round($row['Time1'],3);
			$Time3=$Time3+round($row['Time3'],3);
			$Time4=$Time4+round($row['Time4'],3);
			$Time_Collection=$Time_Collection+round($row['Time_Collection'],3);
			$Time_Agreement=$Time_Agreement+round($row['Time_Agreement'],3);
			$prov=$prov+round($row['prov'],3);
			$Time=$Time+round($row['Time'],3);
			$ManHourSum=$ManHourSum+round($row['Man_Hour_Sum'],3);
		}
		print("<TR align=center>\n");
		print("<TH colspan=6 align=right>Итого:</TH>");
		print("<TH>$Sheet_A1</TH>\n");
		print("<TH>$Time1</TH>\n");
		print("<TH>-</TH>\n");
		print("<TH>$Sheet_A3</TH>\n");
		print("<TH>$Time3</TH>\n");
		print("<TH>-</TH>\n");
		print("<TH>$Sheet_A4</TH>\n");
		print("<TH>$Time4</TH>\n");
		print("<TH>-</TH>\n");
		print("<TH>$Time_Collection</TH>\n");
		print("<TH>$Time_Agreement</TH>\n");
		print("<TH>$prov</TH>\n");
		print("<TH>$Time</TH>\n");
		print("<TH>$ManHourSum</TH>\n");
		print("<TH>-</TH>\n");
	}
}
else {
	print "<a href=\"admin.php\">Назад</a><br />\n";
	print "<div class=\"validation\">";
	print "<H3>Отсутствуют права на просмотр информации!<H3>\n";
	print "</div>";
	print "<div class=\"info\">";
	print "<H4>Перейдите по ссылке на страницу администрирования</H4>\n";
	print "</div>";
	exit;
}
require_once("../end1.php");
?>
