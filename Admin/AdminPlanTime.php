<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей администрирования плана выработки отделов по месяцам
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
	print "<H2>Модуль редактирования плана выработки для отделов по месяцам</H2><br />\n";
	print "<a href=\"Admin.php\">Страница администрирования</a><br />\n";
	print "<Hr Width=\"100%\" ALIGN=\"left\"><br />\n";
	//если есть данные то
	if (!empty($_POST))
	{
		$errors = array(); //объявляется массив ошибок
		//проверка ошибок
		switch ( $_REQUEST['rbselect'] )
		{
			case "insert":
			checkField();//проверка правильности данных
			checkStatementTime();//нельзя добавить уже внесенные значения, только их изменить
			break;
			case "update":
			checkField();//проверка правильности данных
			break;
			case "copy":
			//если выбрана опция копирование данных
			$Comma_separated = implode(",", $_REQUEST['design']);
			$Query = "SELECT * from plan_time ".
			"WHERE ".
			"plan_time.Id IN ($Comma_separated) ".
			"LIMIT 1";   //выборка из таблицы
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				print "Выборка не удалась!\n";
			}
			else {
				while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
				{
					$_REQUEST['Plan'] = $row['Plan'];
					$_REQUEST['PlanOneMan'] = $row['PlanOneMan'];
					$_REQUEST['PlanDepartment'] = $row['PlanDepartment'];
				}
				$tmpPlan = $_REQUEST['Plan'];
				$tmpPlanOneMan = $_REQUEST['PlanOneMan'];
				$tmpPlanDepartment = $_REQUEST['PlanDepartment'];
			}
			break;
		}
		if ( count($errors) > 0 )
		{
			display_errors();
			$tmpPlan = $_REQUEST['Plan'];
			$tmpPlanOneMan = $_REQUEST['PlanOneMan'];
			$tmpPlanDepartment = $_REQUEST['PlanDepartment'];
		}
	}
	print "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\">\n";
	print "<Table  width=\"400px\" Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" class=\"tableReport\">\n";
	print "<!--[if IE]>\n";
	print "<Table  Width=\"400px\" Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" bgcolor=\"#C0E4FF\">\n";
	print "<![endif]-->";
	print "<Tr>\n";
	print "<Td>Выберите отдел\n";
	print "<Td width=\"40%\">";
	print "<select size=\"1\" class=\"selectDO g-2\" name=\"Department\">\n";
	print "<option>Выбор отдела</option>\n";
	//заполнение списка отделов
	getArrayNameDepartment('plan');
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Выберите месяц\n";
	print "<Td>";
	//печатаем список месяцев
	printLstMonth();
	print "<Tr>\n";
	print "<Td>Выберите год\n";
	print "<Td>";
	//печатаем список лет
	printLstYear();
	print "<Tr>\n";
	print "<Td>\n";
	print "Плановый фонд времени <br />(час в мес.)\n";
	print "<Td>\n";
	print "<Input Type=\"text\" Name=\"PlanMonthTime\" Value = \"$tmpPlan\" class=\"g-2\">\n";
	print "<Tr>\n";
	print "<Td>\n";
	print "План на 1 исполнителя\n";
	print "<Td>\n";
	print "<Input Type=\"text\" Name=\"PlanTime\" Value=\"$tmpPlanOneMan\" class=\"g-2\">\n";
	print "<Tr>\n";
	print "<Td>\n";
	print "Расчет плановой выработки пропорц.факт.числ.\n";
	print "<Td>\n";
	print "<Input Type=\"text\" Name=\"PlanDepartment\" Value= \"$tmpPlanDepartment\" class=\"g-2\">\n";
	print "<Tr>\n";
	print "<Td>Условие выбора\n";
	print "<Td>\n";
	print "<FIELDSET>\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"select\">Выборка<br />\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"insert\" checked>Вставка<br />\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"update\">Изменение<br />\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"delete\">Удаление<br />\n";
	print "<input name=\"rbselect\" type=\"radio\" value=\"copy\">Копирование<br />\n";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td>&nbsp;\n";
	print "<Td>\n";
	print "<input type=\"submit\" value=\"&nbsp&nbsp&nbsp&nbspОК&nbsp&nbsp&nbsp&nbsp\">\n";
	print "</Table>\n";
	//отображение данных
	if ( count($errors) == 0 )
	{
		//отображаем таблицу плановой выработки по отделам
		ViewTablePlanTime();
	}
	print "</form>\n";
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
