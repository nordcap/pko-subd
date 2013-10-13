<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей администрирования отдела
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
include_once("../modules/lib/lib.php");
checkSession(); //проверка на присутствие в сессии идентификатора пользователя
require_once("begin1.php");
include_once("../js/jscript.js");
if ($_SESSION['Name_Post'] == "Администратор")
{
	print "<H2>Модуль редактирования отдела</H2><br />\n";
	print "<a href=\"Admin.php\">Страница администрирования</a><br />\n";
	print "<Hr Width=\"100%\" ALIGN=\"left\"><br />\n";
	//если нажата кнопка Запрос,то
	if ( isset($_REQUEST['hidden']) )
	{
		$errors = array(); //объявляется массив ошибок
		switch ( $_REQUEST['rbselect'] )
		{
			case "insert":
			checkEmptyDepartment();
			if ( count($errors) > 0 )
			{
				display_errors();
			}
			break;
			case "update":
			checkEmptyDepartment();
			if ( count($errors) > 0)
			{
				display_errors();
			}
			break;
			case "copy":
			//если выбрана опция копирование данных
			$Comma_separated = implode(",", $_REQUEST['department']);
			$Query = "SELECT * from department ".
			"WHERE ".
			"department.Id IN ($Comma_separated) ".
			"LIMIT 1";   //выборка из таблицы
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				print "Выборка не удалась!\n";
			}
			else  {
				while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
				{
					$_REQUEST['Name_Department'] = $row['Name_Department'];
				}
				$tmpNameDepartment = $_REQUEST['Name_Department'];
			}
			break;
		}
		if( count($errors) > 0 )
		{
			//если есть ошибки при вводе или обновлении, то заносим их во временные переменные переменные
			$tmpNameDepartment = $_REQUEST['Name_Department'];
		}
	}
	//Таблица для редактирования
	//---------------------
	//Форма ввода
	print "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\">\n";
	print "<Table  Width=\"450px\" Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" class=\"tableReport\">\n";
	print "<!--[if IE]>\n";
	print "<Table  Width=\"450px\" Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" bgcolor=\"#C0E4FF\">\n";
	print "<![endif]-->";
	print "<Caption><H3>Ввод данных</Caption>\n";
	print "<Tr>\n";
	print "<Td>Наименование отдела \n";
	print "<Td><Input Type=\"text\" Name=\"Name_Department\" Value=\"$tmpNameDepartment\" class=\"size2\">\n";
	print "<Tr>\n";
	print "<Td>Условие выбора\n";
	print "<Td>\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"select\" >Общая выборка данных<br />\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"insert\" checked>Вставка нового значения<br />\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"update\">Изменение данных<br />\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"delete\">Удаление данных<br />\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"copy\">Копирование данных<br />\n";
	print "<Tr>\n";
	print "<Td>\n";
	print "<Td><input type=\"submit\" name=\"AddDepartment\" value=\"    OK    \"> \n";
	print("<input name=\"hidden\" type=\"hidden\" value=\"data\">");
	print "</Table><br /><br />\n";
	//отображение данных
	if ( count($errors) == 0 )
	{
		//определяем замкнутую область
		//print "<div class=\"layer\">";
		ViewTableDepartment();
		//print "</div>";
	}
	print "</form>";
	//---------------------
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