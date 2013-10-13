<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей администрирования названий выполняемых работ
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
	print "<H2>Модуль редактирования видов работ</H2><br />\n";
	print "<a href=\"Admin.php\">Страница администрирования</a><br />\n";
	print "<Hr Width=\"100%\" ALIGN=\"left\"><br />\n";
	//если нажата кнопка Запрос,то
	if ( isset($_REQUEST['hidden']) )
	{
		$errors=array(); //объявляется массив ошибок
		switch ( $_REQUEST['rbselect'] )
		{
			case "insert":
			checkEmptyWork();
			checkMatchWork();
			if ( count($errors) > 0 )
			{
				display_errors();
			}
			break;
			case "update":
			checkEmptyWork();
			if ( count($errors) > 0 )
			{
				display_errors();
			}
			break;
			case "copy":
			//если выбрана опция копирование данных
			$Comma_separated = implode(",", $_REQUEST['work']);
			$Query = "SELECT * from work ".
			"WHERE ".
			"work.Id IN ($Comma_separated) ".
			"LIMIT 1";   //выборка из таблицы
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				print "Выборка не удалась!\n";
			}
			else  {
				while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH))
				{
					$_REQUEST['Name_Work'] = $row['Name_Work'];
					$_REQUEST['Type_Work'] = $row['Type_Work'];
					$_REQUEST['Comment'] = $row['Comment'];
				}
				$tmpNameWork = $_REQUEST['Name_Work'];
				$tmpTypeWork = $_REQUEST['Type_Work'];
				$tmpComment = $_REQUEST['Comment'];
				break;
			}
		}
		if( count($errors) > 0 )
		{
			//если есть ошибки, заносим их во временные переменные переменные
			$tmpNameWork = $_REQUEST['Name_Work'];
			$tmpTypeWork = $_REQUEST['Type_Work'];
			$tmpComment = $_REQUEST['Comment'];
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
	print " <Tr>\n";
	print "<Td>Наименование работы \n";
	print "<Td><Input Type=\"text\" Name=\"Name_Work\" Value=\"$tmpNameWork\" class=\"size2\">\n";
	print "<Tr>\n";
	print "<Td>Вид работы\n";
	print "<Td><Input Type=\"text\" Name=\"Type_Work\" Value=\"$tmpTypeWork\" class=\"size2\">\n";
	print "<Tr>\n";
	print "<Td>Комментарий\n";
	print "<Td><Input Type=\"text\" Name=\"Comment\" Value=\"$tmpComment\" class=\"size2\">\n";
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
	print "<Td><input type=\"submit\" value=\"    OK    \"> \n";
	print "<input name=\"hidden\" type=\"hidden\" value=\"data\">";
	print "</Table><br /><br />\n";
	//отображение данных
	if ( count($errors) == 0 )
	{
		//определяем замкнутую область
		ViewTableWork();
	}
	print "</form>";
	//----------
	} else {
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