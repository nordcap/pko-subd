<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей администрирования сотрудника
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
include_once("../modules/lib/lib.php");
checkSession(); //проверка на присутствие в сессии идентификатора пользователя
require_once("begin1.php");
include_once("../js/jscript.js");
//проверка на наличие прав доступа
if (($_SESSION['Name_Post'] == "Администратор") OR
($_SESSION['Name_Post'] == "Экономист"))
{
	print "<H2>Модуль редактирования сотрудника</H2><br />\n";
	print "<a href=\"Admin.php\">Страница администрирования</a><br />\n";
	print "<Hr Width=\"100%\" ALIGN=\"left\"><br />\n";
	//если нажата кнопка Запрос,то
	if ( isset($_REQUEST['hidden']) )
	{
		$errors = array(); //объявляется массив ошибок
		switch ( $_REQUEST['rbselect'] )
		{
			case "insert":
			checkEmptyEmployee();
			break;
			case "update":
			checkEmptyEmployee();
			break;
			case "updateNotParol":
			checkEmptyEmployee();
			break;
			case "copy":
			//если выбрана опция копирование данных
			$Comma_separated = implode(",", $_REQUEST['employee']);
			$Query = "SELECT * from employee ".
			"WHERE ".
			"employee.Id IN ($Comma_separated) ".
			"LIMIT 1";   //выборка из таблицы
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				print "Выборка не удалась!\n";
			}
			else {
				while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
				{
					$_REQUEST['Family'] 		= $row['Family'];
					$_REQUEST['Name'] 			= $row['Name'];
					$_REQUEST['Patronymic'] 	= $row['Patronymic'];
					$_REQUEST['Login'] 			= $row['Login'];
					$_REQUEST['Department'] 	= $row['id_Department'];
					$_REQUEST['Office']			= $row['id_Office'];
					$_REQUEST['Post'] 			= $row['id_Post'];
					$_REQUEST['Status'] 		= $row['Status'];
					$_REQUEST['Sanction'] 		= $row['Sanction'];
					$_REQUEST['Tabel_Number'] 	= $row['Tabel_Number'];
				}
				$tmpFamily 			= $_REQUEST['Family'];
				$tmpName 			= $_REQUEST['Name'];
				$tmpPatronymic 		= $_REQUEST['Patronymic'];
				$tmpLogin 			= $_REQUEST['Login'];
				$tmpDepartment 		= $_REQUEST['Department'];
				$tmpOffice 			= $_REQUEST['Office'];
				$tmpPost 			= $_REQUEST['Post'];
				$tmpStatus 			= $_REQUEST['Status'];
				$tmpSanction 		= $_REQUEST['Sanction'];
				$tmpTabelNumber 	= $_REQUEST['Tabel_Number'];
			}
			break;
		}
		if( count($errors) > 0 )
		{
			display_errors();
			//если есть ошибки, заносим их во временные переменные переменные
			$tmpFamily 			= $_REQUEST['Family'];
			$tmpName 			= $_REQUEST['Name'];
			$tmpPatronymic 		= $_REQUEST['Patronymic'];
			$tmpLogin 			= $_REQUEST['Login'];
			$tmpDepartment		= $_REQUEST['Department'];
			$tmpOffice 			= $_REQUEST['Office'];
			$tmpPost 			= $_REQUEST['Post'];
			$tmpStatus 			= $_REQUEST['Status'];
			$tmpSanction 		= $_REQUEST['Sanction'];
			$tmpTabelNumber 	= $_REQUEST['Tabel_Number'];
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
	print "<Td>Фамилия \n";
	print "<Td><Input Type=\"text\" Name=\"Family\" Value=\"$tmpFamily\" class=\"size2\">\n";
	print "<Tr>\n";
	print "<Td>Имя\n";
	print "<Td><Input Type=\"text\" Name=\"Name\" Value=\"$tmpName\" class=\"size2\">\n";
	print "<Tr>\n";
	print "<Td>Отчество\n";
	print "<Td><Input Type=\"text\" Name=\"Patronymic\" Value=\"$tmpPatronymic\" class=\"size2\">\n";
	print "<Tr>\n";
	print "<Td>Табельный номер\n";
	print "<Td><Input Type=\"text\" Name=\"Tabel_Number\" Value=\"$tmpTabelNumber\" class=\"size2\">\n";
	print "<Tr>\n";
	print "<Td>Отдел\n";
	print "<Td><select size=\"1\" name=\"Department\" class=\"size2\">\n";
	print "<option>".getNameDepartment($tmpDepartment)."</option>\n";
	//заполнение списка отделов
	getArrayNameDepartment();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Бюро\n";
	print "<Td><select size=\"1\" name=\"Office\" class=\"size2\">\n";
	print "<option>".getNameOffice($tmpOffice)."</option>\n";
	//заполнение списка бюро
	getArrayNameOffice();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Должность\n";
	print "<Td><select size=\"1\" name=\"Post\" class=\"size2\">\n";
	print "<option>".getNamePost($tmpPost)."</option>\n";
	//заполнение списка должностей
	getArrayNamePost();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Логин\n";
	print "<Td><Input Type=\"text\" Name=\"Login\" Value=\"$tmpLogin\" class=\"size2\">\n";
	print "<Tr>\n";
	print "<Td>Пароль\n";
	print "<Td><Input Type=\"password\" Name=\"Password\" class=\"size2\">\n";
	print "<Tr>\n";
	print "<Td>Право утверждения работ\n";
	if ( $tmpSanction == 'TRUE')
	{
		print "<Td><input name=\"Sanction\" type=\"checkbox\" value=\"TRUE\" checked>";
	}
	else {
		print "<Td><input name=\"Sanction\" type=\"checkbox\" value=\"TRUE\">";
	}
	print "<Tr>\n";
	print "<Td>Уволен\n";
	if ( $tmpStatus == "FALSE" )
	{
		print "<Td><input name=\"Status\" type=\"checkbox\" value=\"FALSE\" checked>";
	}
	else {
		print "<Td><input name=\"Status\" type=\"checkbox\" value=\"FALSE\">";
	}
	print "<Tr>\n";
	print "<Td>Месяц увольнения\n";
	print "<Td>";
	//печатаем список месяцев
	printLstMonth();
	print "<Tr>\n";
	print "<Td>Год увольнения\n";
	print "<Td>";
	//печатаем список лет
	printLstYear();
	print "<Tr>\n";
	print "<Td>Условие выбора\n";
	print "<Td>\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"select\" >Общая выборка данных<br />\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"insert\">Вставка нового значения<br />\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"update\">Изменение всех данных<br />\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"updateNotParol\">Изменение данных без пароля<br />\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"delete\">Удаление данных<br />\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"copy\" checked>Копирование данных<br />\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"status\">Уволен<br />\n";
	print "<Tr>\n";
	print "<Td>\n";
	print " <Td><input type=\"submit\" value=\"    OK    \"> \n";
	print "<input name=\"hidden\" type=\"hidden\" value=\"data\">";
	print "</Table>\n";
	//отображение данных
	if ( count($errors) == 0 )
	{
		//определяем замкнутую область
		ViewTableEmployee();
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