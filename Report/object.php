<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей диспечеризации отчетов
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();     //инициализирум механизм сесссий
include_once("../modules/lib/lib.php");
checkSession(); //проверка на присутствие в сессии идентификатора пользователя
require_once("begin1.php");
include_once("../js/jscript.js");
//проверяем права доступа
//данный модуль доступен только для лиц обладающим правом создания различных отчетов, как для всего ПУ
//так и для отдельных отделов
if ( ($_SESSION['Name_Post'] == "Экономист") OR
($_SESSION['Name_Post'] == "Главный инженер") OR
($_SESSION['Name_Post'] == "Начальник управления") OR
($_SESSION['Name_Post'] == "Зам.начальника отдела") OR
($_SESSION['Name_Post'] == "Начальник отдела") OR
($_SESSION['Name_Post'] == "ГИП") OR
($_SESSION['Name_Post'] == "Администратор") OR
($_SESSION['Name_Post'] == "Работник архива"))
{
	include_once("../modules/lib/lib.php");
	print "<H2>Модуль выбора отчетов</H2><br />\n";
	print "<a href=\"../index1.php\">Назад</a><br />\n";
	print "<Hr Width=\"100%\" ALIGN=\"left\"><br />\n";
	//для всех кроме начальника отдела доступны следующие отчеты
	print "<ul class=\"menu_object\">";
	print "<li id=\"object1\"><a href=\"#\" class = \"beginMenu\" title=\"Сводные данные за год по отделам\">Сводные данные за год по отделам</a>";
	if ( $_SESSION['Name_Post'] != "Начальник отдела" OR $_SESSION['Name_Post'] != "Зам.начальника отдела")
	{
		cells1();
	}
	print "</li>";


	print "<li id=\"object11\"><a href=\"#\" title=\"Сравнение производительности по годам\">Сравнение производительности по годам</a>";
	if ( $_SESSION['Name_Post'] != "Начальник отдела" OR $_SESSION['Name_Post'] != "Зам.начальника отдела")
	{
		cells11();
	}
	print "</li>";

	print "<li id=\"object9\"><a href=\"#\" title=\"Отчет по выполненным работам\">Отчет по выполненным работам</a>";
	if ( $_SESSION['Name_Post'] != "Начальник отдела" OR $_SESSION['Name_Post'] != "Зам.начальника отдела")
	{
		cells9();
	}
	print "</li>";
	print "<li id=\"object2\"><a href=\"#\" title=\"Свод трудозатрат отделов\">Свод трудозатрат отделов</a>";
	if ( $_SESSION['Name_Post'] != "Начальник отдела" OR $_SESSION['Name_Post'] != "Зам.начальника отдела")
	{
		cells2();
	}
	print "</li>";
	print "<li id=\"object3\"><a href=\"#\" title=\"Свод трудозатрат УПИРиИ по объектам\">Свод трудозатрат УПИРиИ по объектам</a>";
	if ( $_SESSION['Name_Post'] != "Начальник отдела" OR $_SESSION['Name_Post'] != "Зам.начальника отдела")
	{
		cells3();
	}
	print "</li>";
	print "<li id=\"object4\"><a href=\"#\" title=\"Сводные данные по методике ДКС\">Сводные данные по методике ДКС</a>";
	if ( $_SESSION['Name_Post'] != "Начальник отдела" )
	{
		cells4();
	}
	print "<li id=\"norma\"><a href=\"#\" title=\"Определение норматива трудоемкости\">Определение норматива трудоемкости</a>";
	if ( $_SESSION['Name_Post'] != "Начальник отдела" )
	{
		cells_norma();
	}
	print "</li>";
	print "<li id=\"object5\"><a href=\"#\" title=\"Сводные данные по выработкам в подразделениях\">Сводные данные по выработкам в подразделениях</a>";
	cells5();
	print "</li>";
	
	print "<li id=\"object_performance\"><a href=\"#\" title=\"Производительность подразделения\">Производительность подразделения</a>";
	cells_performance();
	print "</li>";
	
	
	print "<li id=\"object6\"><a href=\"#\" title=\"Свод трудозатрат отдела по месяцам\">Свод трудозатрат отдела по месяцам</a>";
	cells6();
	print "</li>";
	print "<li id=\"object7\"><a href=\"#\" title=\"Проверка табельного времени\">Проверка табельного времени</a>";
	cells7();
	print "</li>";
	print "<li id=\"object10\"><a href=\"#\" title=\"Табель учета рабочего времени\">Табель учета рабочего времени</a>";
	cells10();
	print "</li>";
	print "<li id=\"object8\" ><a href=\"#\" class=\"endMenu\" title=\"План отдела\">План отдела</a>";
	cells8();
	print "</li>";
	print "</ul>";
	print "<p></p>";
	print "<div class=\"hint\"><span>Подсказка! </span>Для закрытия отчета, кликнуть 2 раза на строке меню или на самом отчете</div>";
}
else {
	print "<div class=\"validation\">";
	print "<H3>Отсутствуют права на просмотр информации!</H3>\n";
	print "</div>";
	print "<div class=\"info\">";
	print "<H4>Перейдите по ссылке на главную страницу</H4>\n";
	print "</div>";
    print "<a href=\"../index1.php\">Главная страница</a><br />\n";
}
require_once("../end1.php");
?>