<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является диспечерской страницей администрирования
в модуле index.php
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();     //инициализирум механизм сесссий
include_once("../modules/lib/lib.php");
checkSession(); //проверка на присутствие в сессии идентификатора пользователя
unset($_SESSION['saveQuery']);
include_once("begin1.php");
include_once("../js/jscript.js");


if (($_SESSION['Name_Post'] == "Администратор") OR
($_SESSION['Name_Post'] == "Начальник управления") OR
($_SESSION['Name_Post'] == "Работник архива") OR
($_SESSION['Name_Post'] == "Экономист"))
{
	print "<CENTER><H2>Модуль администрирования</H2></CENTER><br />\n";
	//ссылка на главную страницу
	print "<a href=\"../index1.php\">Главная страница</a><br />\n";
	print "<Hr Width=\"30%\" ALIGN=\"left\"><br />\n";
	//ссылки на отдельные модули администрирования
	//print "<div>";
	print "<ul class=\"menu_object\">";
	print "<li><a href=\"AdminEmployee.php\" class = \"beginMenu\">Редактирование сотрудника</a></li>";
	print "<li><a href=\"AdminBlock.php\">Управление блокировками</a></li>";
	print "<li><a href=\"AdminDepartment.php\">Редактирование отдела</a></li>";
	print "<li><a href=\"AdminOffice.php\">Редактирование бюро</a></li>";
	print "<li><a href=\"AdminPost.php\">Редактирование должности</a></li>";
	print "<li><a href=\"AdminWork.php\">Редактирование видов работ</a></li>";
	print "<li><a href=\"AdminProject.php\">Редактирование проектов</a></li>";
	print "<li><a href=\"AdminMark.php\">Редактирование марок</a></li>";
	print "<li><a href=\"AdminManHour.php\">Редактирование трудозатрат</a></li>";
	print "<li><a href=\"AdminCustomer.php\">Редактирование заказчиков</a></li>";
	print "<li><a href=\"AdminYear.php\">Редактирование лет</a></li>";
	print "<li id='admin_tabel'><a href='#'>Редактирование табельного времени</a>";
	cellTabelTime();
	print "</li>";
	print "<li><a href=\"AdminPlanTime.php\" class=\"endMenu\">Редактирование плана выработки по отделам</a></li>";
	//	 print "<li><a href=\"AdminQuery.php\">Выполнение запроса</a></li><br />\n";
	print "<ul>";
	//print "</div>";
}
else {
	print "<a href=\"../index1.php\">Назад</a><br />\n";
	print "<div class=\"validation\">";
	print "<H3>Отсутствуют права на просмотр информации!</H3>\n";
	print "</div>";
	print "<div class=\"info\">";
	print "<H4>Перейдите по ссылке на главную страницу</H4>\n";
	print "</div>";
	exit;
}
unsetSession();

require_once("../end1.php");
?>