<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей администрирования таблицы трудозатрат
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();     //инициализирум механизм сесссий
include_once("../modules/lib/lib.php");
checkSession(); //проверка на присутствие в сессии идентификатора пользователя
require_once("begin1.php");
include_once("../js/jscript.js");
//проверка на наличие прав доступа
if (($_SESSION['Name_Post'] == "Администратор") OR
($_SESSION['Name_Post'] == "Экономист"))
{
	print "<H2>Модуль редактирования трудозатрат</H2><br />\n";
	print "<a href=\"Admin.php\">Страница администрирования</a><br />\n";
	print "<Hr Width=\"100%\" ALIGN=\"left\"><br />\n";
	//если нажата кнопка Запрос,то
	if ( isset($_REQUEST['hidden']) )
	{
		$errors = array(); //объявляется массив ошибок
		switch ( $_REQUEST['rbselect'] )
		{
			case "insert":
			checkEmptyManHour();
			if ( count($errors) > 0 )
			{
				display_errors();
			}
			break;
			case "update":
			checkEmptyManHour();
			if ( count($errors) > 0 )
			{
				display_errors();
			}
			break;
			case "copy":
			//если выбрана опция копирование данных
			$Comma_separated = implode(",", $_REQUEST['manhour']);
			$Query = "SELECT man_hour.id, man_hour.id_Mark, man_hour.id_Work, man_hour.Man_Hour from man_hour ".
			"WHERE ".
			"man_hour.Id IN ($Comma_separated) ".
			"LIMIT 1";   //выборка из таблицы
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				print "Выборка не удалась!\n";
			}
			else	{
				while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
				{
					$_REQUEST['Name_Mark'] = $row['id_Mark'];
					$_REQUEST['Name_Work'] = $row['id_Work'];
					$_REQUEST['Man_Hour'] = $row['Man_Hour'];
				}
				$tmpNameMark = $_REQUEST['Name_Mark'];
				$tmpNameWork = $_REQUEST['Name_Work'];
				$tmpManHour = $_REQUEST['Man_Hour'];
			}
			break;
		}
		if( count($errors) > 0 )
		{
			//если есть ошибки, заносим их во временные переменные переменные
			$tmpNameMark = $_REQUEST['Name_Mark'];
			$tmpNameWork = $_REQUEST['Name_Work'];
			$tmpManHour = $_REQUEST['Man_Hour'];
		}
	}
	//Таблица для редактирования
	//---------------------
	//Форма ввода
	print "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\">\n";
	print "<Table  Width=\"400px\" Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" class=\"tableReport\">\n";
	print "<!--[if IE]>\n";
	print "<Table  Width=\"400px\" Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" bgcolor=\"#C0E4FF\">\n";
	print "<![endif]-->";
	print "<Caption><H3>Ввод данных</Caption>\n";
	print "<Tr>\n";
	print "<Td>Марка\n";
	print "<Td><select size=\"1\" name=\"Name_Mark\" class=\"size2\">\n";
	print "<option>".getNameMark($tmpNameMark)."</option>\n";
	//заполнение списка марок
	getArrayNameMark();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Наименование работ\n";
	print "<Td><select size=\"1\" name=\"Name_Work\" class=\"size2\">\n";
	print "<option>".getNameWork($tmpNameWork)."</option>\n";
	//заполнение списка видов работ
	getArrayWorkName();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Трудозатраты\n";
	print "<Td><Input Type=\"text\" Name=\"Man_Hour\" Value=\"$tmpManHour\" class=\"size2\">\n";
	print "<Tr>\n";
	print "<Td>Условие выбора\n";
	print "<Td>\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"select\">Общая выборка данных<br />\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"insert\" checked>Вставка нового значения<br />\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"update\">Изменение данных<br />\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"delete\">Удаление данных<br />\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"copy\">Копирование данных<br />\n";
	print "<Tr>\n";
	print "<Td>\n";
	print " <Td><input type=\"submit\" value=\"    OK    \"> \n";
	print "<input name=\"hidden\" type=\"hidden\" value=\"data\">";
	print "</Table>\n";
	//отображение данных
	if ( count($errors) == 0 )
	{
		//определяем замкнутую область
		ViewTableManHour();
	}
	print "</form>";
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