<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей администрирования проектов
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();     //инициализирум механизм сесссий
//начать буферизацию вывода
ob_start();
include_once("../modules/lib/lib.php");
checkSession(); //проверка на присутствие в сессии идентификатора пользователя
require_once("begin1.php");
include_once("../js/jscript.js");
//проверка на наличие прав доступа
if (($_SESSION['Name_Post'] == "Администратор") OR
($_SESSION['Name_Post'] == "Главный инженер") OR
($_SESSION['Name_Post'] == "Начальник управления") OR
($_SESSION['Name_Post'] == "ГИП") OR
($_SESSION['Name_Post'] == "Работник архива") OR  
($_SESSION['Name_Post'] == "Экономист"))
{
	print "<H2>Модуль редактирования проектов</H2><br />\n";
	print "<a href=\"Admin.php\">Страница администрирования</a><br />\n";
	print "<Hr Width=\"100%\" ALIGN=\"left\"><br />\n";
	if (!empty($_POST))
	{
		//если нажата кнопка Запрос,то
		$errors = array(); //объявляется массив ошибок и успешных сообщений
		$successes = array();
		//проверка нажатия кнопки проверки на совпадение номера проекта
		if (isset($_REQUEST['checkProject']))
		{
			checkMismatchProject();
		}
		//при нажатия кнопки поиска, долен быть введен номер проекта
		if (isset($_REQUEST['findProject']))
		{
			if ( empty($_REQUEST['Number_Project']) )
			{
				$errors[] = "<H3>Введите номер проекта</H3>";
			}
		}
		//обработка нажатия кнопки ОК
		if (isset($_REQUEST['btnSubmit']))
		{
			switch ( $_REQUEST['rbselect'] )
			{
				case "insert":
				//проверка правильности даты
				$_REQUEST['DateOpen'] = checkEmptyDateUniversal($_REQUEST['btnSubmit'],$_REQUEST['DateOpen']);
				$_REQUEST['DateClose'] = checkEmptyDateUniversal($_REQUEST['btnSubmit'],$_REQUEST['DateClose']);
				checkDateBE(); // проверка на опережение даты начала
				checkEmptyProject();
				checkMatchProject();
				break;
				
				case "update":
				//проверка правильности даты
				$_REQUEST['DateOpen'] = checkEmptyDateUniversal($_REQUEST['btnSubmit'],$_REQUEST['DateOpen']);
				$_REQUEST['DateClose'] = checkEmptyDateUniversal($_REQUEST['btnSubmit'],$_REQUEST['DateClose']);
				checkDateBE();       // проверка на опережение даты начала
				checkEmptyProject();
				//checkMatchProject();
				break;
				
				case "customer":
				checkEmptyProject();
				break;
				
				case "close":
				//проверка правильности даты
				$_REQUEST['DateOpen'] = checkEmptyDateUniversal($_REQUEST['btnSubmit'],$_REQUEST['DateOpen']);
				$_REQUEST['DateClose'] = checkEmptyDateUniversal($_REQUEST['btnSubmit'],$_REQUEST['DateClose']);
				checkDateBE();       // проверка на опережение даты начала
				break;
				
				case "status":
				//проверка правильности даты
				$_REQUEST['DateOpen'] = checkEmptyDateUniversal($_REQUEST['btnSubmit'],$_REQUEST['DateOpen']);
				$_REQUEST['DateClose'] = checkEmptyDateUniversal($_REQUEST['btnSubmit'],$_REQUEST['DateClose']);
				checkDateBE();      // проверка на опережение даты начала
				break;
				
				case "copy":
				//если выбрана опция копирование данных
				$Comma_separated = implode(",", $_REQUEST['project']);
				$Query = "SELECT  Id, Number_Project, Name_Project, Number_UTR, Number_TORO, id_Customer,
				StatusProject, DateOpenProject, DateCloseProject, Manager, Customer_Service, Plant_Destination, ".
				"Report, Report_ManHour ".
				" FROM project ".
				" WHERE ".
				" Id IN ($Comma_separated) ".
				" LIMIT 1";   //выборка из таблицы
				if ( !($dbResult = mysql_query($Query, $link)) )
				{
					print "Выборка не удалась!\n";
				}
				else	{
					while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
					{
						$_POST['Customer']          = $row['id_Customer'];
						$_POST['Manager']           = $row['Manager'];
						$_POST['Number_Project']    = $row['Number_Project'];
						$_POST['Number_UTR']        = $row['Number_UTR'];
						$_POST['Number_TORO']       = $row['Number_TORO'];
						$_POST['Name_Project']      = $row['Name_Project'];
						$_POST['Report']            = $row['Report'];
						$_POST['Report_ManHour']    = $row['Report_ManHour'];
                        $_POST['Customer_Service']  = $row['Customer_Service'];
                        $_POST['Plant_Destination'] = $row['Plant_Destination'];


						$tmpStatusProject           = $row['StatusProject'];
						$_POST['DateOpen']          = date("d.m.y", $row['DateOpenProject']);
						$_POST['DateClose']         = date("d.m.y", $row['DateCloseProject']);
					}
					/* print phpinfo(32);   */
				}
				break;
			}
		}
		if (isset($_REQUEST['btnSearch']))
		{
			if (!empty($_REQUEST['DateBegin']))
			{
				$_REQUEST['DateBegin'] = inputDate($_REQUEST['DateBegin']);
			}
			if (!empty($_REQUEST['DateEnd']))
			{
				$_REQUEST['DateEnd'] = inputDate($_REQUEST['DateEnd']);
			}
		}
		if ( count($successes) > 0 OR count($errors) > 0)
		{
			display_errors();
		}
	}
	//Таблица для редактирования
	//---------------------
	//Форма ввода
	print "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\" enctype=\"multipart/form-data\">\n";
	print "<Table  Width=\"100%\" Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" class=\"tableReport\">\n";
	print "<Caption><H3>Ввод данных</Caption>\n";
	print "<Tr>\n";
	print "<Td>Номер проекта \n";
	print "<Td><Input Type=\"text\" Name=\"Number_Project\" Value=\"".$_POST['Number_Project']."\" class=\"size2\"><br>\n";
	print "<button type=\"submit\" name=\"checkProject\"><img src=\"../img/check.png\" style=\"vertical-align: bottom\">&nbsp;Проверить</button>";
	print "<button type=\"submit\" name=\"findProject\"><img src=\"../img/check.png\" style=\"vertical-align: bottom\">&nbsp;Найти</button>";
	print "<Td rowspan=7 style='vertical-align:top'>\n";
	print "<FIELDSET>\n";
	print "<LEGEND><b>Условие выбора</b></LEGEND>";
	print "<ul class=\"menu_input\">";
	print "<li><input name=\"rbselect\" type=\"radio\" value=\"select\" >Общая выборка данных</li>\n";
	print "<li><input name=\"rbselect\" type=\"radio\" value=\"insert\" checked >Вставка нового значения</li>\n";
	print "<li><input name=\"rbselect\" type=\"radio\" value=\"update\">Изменение всех данных</li>\n";
	print "<li><input name=\"rbselect\" type=\"radio\" value=\"customer\">Изменение заказчика</li>\n";
	print "<li><input name=\"rbselect\" type=\"radio\" value=\"status\">Изменение статуса и даты</li>\n";
	print "<li><input name=\"rbselect\" type=\"radio\" value=\"close\">Изменение даты</li>\n";
	print "<li><input name=\"rbselect\" type=\"radio\" value=\"delete\">Удаление данных</li>\n";
	print "<li><input name=\"rbselect\" type=\"radio\" value=\"copy\">Копирование данных</li>\n";
	print "</ul>";
	print "</FIELDSET>\n";
	print "<Td rowspan=12 style='vertical-align:top'>\n";
	print "<FIELDSET>\n";
	print "<LEGEND><b>Статус проекта</b></LEGEND>";
	print "<ul class=\"menu_input\">";
	if (isset($tmpStatusProject))
	{
		if ($tmpStatusProject == 'OKC')
		{
			print "<li><input name=\"rbstatus\" type=\"radio\" value=\"OKC\" checked>Капитальное строительство</li>\n";
		}
		else {
			print "<li><input name=\"rbstatus\" type=\"radio\" value=\"OKC\" >Капитальное строительство</li>\n";
		}
		if ($tmpStatusProject == 'Plan')
		{
			print "<li><input name=\"rbstatus\" type=\"radio\" value=\"Plan\" checked>Плановый проект</li>\n";
		}
		else {
			print "<li><input name=\"rbstatus\" type=\"radio\" value=\"Plan\" >Плановый проект</li>\n";
		}
		if ($tmpStatusProject == 'OverPlan')
		{
			print "<li><input name=\"rbstatus\" type=\"radio\" value=\"OverPlan\" checked>Внеплановый проект</li>\n";
		}
		else {
			print "<li><input name=\"rbstatus\" type=\"radio\" value=\"OverPlan\">Внеплановый проект</li>\n";
		}
		} else {
		print "<li><input name=\"rbstatus\" type=\"radio\" value=\"OKC\" >Капитальное строительство</li>\n";
		print "<li><input name=\"rbstatus\" type=\"radio\" value=\"Plan\" >Плановый проект</li>\n";
		print "<li><input name=\"rbstatus\" type=\"radio\" value=\"OverPlan\" checked>Внеплановый проект</li>\n";
	}
	print "</ul>";
	print "</FIELDSET>\n";
	print "<p></p>";
	print "<FIELDSET>\n";
	print "<LEGEND><b>Изменение даты</b></LEGEND>";
	print "<ul class=\"menu_input\">";
	/*print "<li><input name=\"rbdate\" type=\"radio\" value=\"date_all\" checked>Изменение всех дат</li>\n";
	print "<li><input name=\"rbdate\" type=\"radio\" value=\"date_open\" >Принятие заявки</li>\n";
	print "<li><input name=\"rbdate\" type=\"radio\" value=\"date_close\" >Завершение проекта</li>\n";
	print "<li><input name=\"rbdate\" type=\"radio\" value=\"date_output\" >Выдача проекта заказчику</li>\n";*/
	print "<li><input type=\"checkbox\" name=\"chkdate['DateOpen']\" value=\"date_open\" />Принятие заявки</li> ";
	print "<li><input type=\"checkbox\" name=\"chkdate['DateClose']\" value=\"date_close\" />Завершение проекта</li> ";
	print "</ul>";
	print "</FIELDSET>\n";
	print "<p></p>";
	print "<FIELDSET>\n";
	print "<LEGEND><b>Способ сортировки</b></LEGEND>";
	print "<ul class=\"menu_input\">";
	print "<li><input name=\"rbchksort\" type=\"radio\" value=\"sort_customer\" checked>по заказчикам</li>\n";
	print "<li><input name=\"rbchksort\" type=\"radio\" value=\"sort_gip\">по гипам</li>\n";
	print "</ul>";
	print "</FIELDSET>\n";
	//вставить кнопки
	print "<p></p>";
	print "<input type=\"submit\" name=\"btnSubmit\" value='OK' style='width: 100px; margin-left: 10px;'> \n";
	print "<input type=\"submit\" name=\"btnDoc\" value='ВРД' style='width: 100px; margin-left: 10px;'> \n";
	print "<p></p>";
	print "<input type=\"checkbox\" name=\"hide\" value=\"TRUE\" />Отобразить все  столбцы ";
	//========    поиск   ==================================
	print "<Td width=350px rowspan=12 style='vertical-align:top'>\n";
	print "<FIELDSET  style='padding: 3px;'>\n";
	print "<LEGEND><b>Поиск по дате</b></LEGEND>";
	//----------- month -----------------
	print "<div style=\" width: 80px; float:left;\">";
	print "<ul class=\"menu_input\" >";
	print "<li><b>Месяц</b></li>";
	foreach($_SESSION['Month'] as $key=>$value)
	{
		//отмечаем текущий месяц
		if ($key == $_SESSION['CurrentMonth'])
		{
			print "<li><input type=\"checkbox\" name=\"chkMonthSearch[$key]\" value=\"$value\" checked/>$value</li> ";
		}
		else {
			print "<li><input type=\"checkbox\" name=\"chkMonthSearch[$key]\" value=\"$value\" />$value</li> ";
		}
	}
	print "</ul>";
	print "</div>";
	//----------- year -----------------
	print "<div style = \"width:70px;float:left;\">";
	print "<ul class=\"menu_input\">";
	print "<li><b>Год</b></li>";
	foreach($_SESSION['Year'] as $key=>$value)
	{
		//отмечаем текущий год
		if ($key == $_SESSION['CurrentYear'])
		{
			print "<li><input type=\"radio\" name=\"rbYearSearch\" value=\"$value\" checked/>$value</li> ";
		}
		else {
			print "<li><input type=\"radio\" name=\"rbYearSearch\" value=\"$value\" />$value</li> ";
		}
	}
	print "</ul>";
	print "</div>";

  //----------- регистрация или окончание -----------------
	print "<div>";
	print "<input type=\"radio\" name=\"rbOpenClose\" value=\"open\"/>по дате регистрации<br />";
	print "<input type=\"radio\" name=\"rbOpenClose\" value=\"close\" checked/>по дате окончания";
	print "</div>";
  //--------------------------------------------------------

	print "<FIELDSET>\n";
	print "<div class='width: 250px;'>";
	print "<i><u>Динамический диапазон</u></i><br />";
	print "<Input Type=\"text\" Name=\"DateBegin\"   class=\"date_input size1\"><br />\n";
	print "<Input Type=\"text\" Name=\"DateEnd\"   class=\"date_input size1\">\n";
	print "</div>";
	print "</FIELDSET>\n";
	print "<FIELDSET>\n";
	print "<ul class=\"menu_input\" >";
	print "<li><b>Статус</b></li>";
	print "<li><input name=\"rbstatusSearch\" type=\"radio\" value=\"All\" checked>Все</li>\n";
	print "<li><input name=\"rbstatusSearch\" type=\"radio\" value=\"OKC\" >ОКС</li>\n";
	print "<li><input name=\"rbstatusSearch\" type=\"radio\" value=\"Plan\" >План</li>\n";
	print "<li><input name=\"rbstatusSearch\" type=\"radio\" value=\"OverPlan\" >Внеплан</li>\n";
	print "</ul>";
	print "</FIELDSET>\n";
	print "<select size=\"1\" name=\"CustomerSearch\" class=\"size2\">\n";
	print "<option>Выбор заказчика</option>\n";
	//заполнение списка заказчиков
	getArrayNameCustomer();
	print "</select>\n";
	print "<input type=\"submit\" name=\"btnSearch\" value='Поиск' style='width: 100px; position: relative; left: 20px; top: 0px;'> \n";
	print "</FIELDSET>\n";
	//======================================================
	print "<Tr>\n";
	print "<Td>Название проекта\n";
	print "<Td><Input Type=\"text\" Name=\"Name_Project\" Value=\"".$_POST['Name_Project']."\" class=\"size2\">\n";
	print "<Tr>\n";
	print "<Td>Номер УТР\n";
	print "<Td><Input Type=\"text\" Name=\"Number_UTR\" Value=\"".$_POST['Number_UTR']."\" class=\"size1\">\n";
	print "<button type=\"submit\" name=\"findProjectUTR\"><img src=\"../img/check.png\" style=\"vertical-align: bottom\">&nbsp;Найти</button>";

	print "<Tr>\n";
	print "<Td>Номер заявки на ремонт/Номер объекта\n";
	print "<Td><Input Type=\"text\" Name=\"Number_TORO\" Value=\"".$_POST['Number_TORO']."\" class=\"size2\">\n";
	
	print "<Tr>\n";
	print "<Td>Менеджер проекта (ГИП)\n";
	print "<Td><select size=\"1\" name=\"Manager\" class=\"size2\">\n";
	print "<option>".getNameEmployee($_POST['Manager'])."</option>\n";
	//заполнение списка сотрудников
	getArrayNameEmployeeGIP();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Выберите заказчика\n";
	print "<Td><select size=\"1\" name=\"Customer\" class=\"size2\">\n";
	print "<option>".getNameCustomer($_POST['Customer'])."</option>\n";
	//заполнение списка заказчиков
	getArrayNameCustomer();
	print "</select>\n";
	print "<Tr>\n";
	print "<Td>Дата принятия заявки\n";
	print "<Td>";
	print "<Input Type=\"text\" Name=\"DateOpen\" Value=\"".$_POST['DateOpen']."\" class=\"date_input size2\">\n";
	print "<Tr>\n";
	print "<Td>Дата завершения проекта\n";
	print "<Td>";
	print "<Input Type=\"text\" Name=\"DateClose\" Value=\"".$_POST['DateClose']."\" class=\"date_input size2\">\n";

    print "<Tr>\n";
    print "<Td>Служба заказчика\n";
	print "<Td>";
	print "<Input Type=\"text\" Name=\"Customer_Service\" Value=\"".$_POST['Customer_Service']."\" class=\"size2\">\n";
	print "<Tr>\n";
	print "<Td>Цеха\n";
	print "<Td>";
	print "<Input Type=\"text\" Name=\"Plant_Destination\" Value=\"".$_POST['Plant_Destination']."\" class=\"size2\">\n";

	print "<Tr>\n";
	print "<Td>Номер акта\n";
	print "<Td><Input Type=\"text\" Name=\"Report\" Value=\"".$_POST['Report']."\" class=\"size2\">\n";
	print "<Td rowspan=3>";
	print "<FIELDSET>\n";
	print "<LEGEND><b>Предполагаемые отделы</b></LEGEND>";
	//добавление списка планируемых отделов
	print "<select name=\"Depart[]\"  multiple style='float:left'>\n";
	getArrayNameDepartment();
	print "</select>";
	print "<input type=\"submit\" name=\"btnDep\" value='Внести' style='width: 80px; margin-left: 10px;'> \n";
	print "<input type=\"submit\" name=\"btnDepDel\" value='Удалить' style='width: 80px; margin-left: 10px;'> \n";
	print "</FIELDSET>\n";
	print "<Tr>\n";
	print "<Td>Актованные трудозатраты\n";
	print "<Td><Input Type=\"text\" Name=\"Report_ManHour\" Value=\"".$_POST['Report_ManHour']."\" class=\"size2\">\n";
	//-------------загрузка файла
	print "<Tr>\n";
	print "<Td colspan='2'>\n";
	print "<div>";
	print "<input name=\"upload\" type=\"file\" /> ";
	print "<input type=\"submit\" name=\"btnScan\" value='Добавить' style='width: 70px; margin-left: 0px;'> \n";
	print "<input type=\"submit\" name=\"btnScanDel\" value='Удалить' style='width: 70px; margin-left: 5px;'> \n";
	print "</div>";
	//print "<input name=\"hidden\" type=\"hidden\" value=\"data\">";
	print "</Table><br /><br />\n";
	//вызов отображения таблицы
	if ( count($errors) == 0 )
	{
		$result = ViewTableProject();
	}
	print "</form>";
		$s = serialize($result);    //происходит сериализация массива для передачи по скрытому полю ввода
		print "<form action=\"../Report/report_excel/saveExcel_Project.php\" method=\"POST\">\n";
		print "<input type=\"image\" name=\"btnSaveExcel\" src=\"../img/file_xls.png\" width=\"48\" height=\"48\" border=\"0\" alt=\"сохранение в Экселе\" />  ";
		print "<div class=\"hint\"><span>Подсказка! </span>Сохранение в Excel</div>";
		print "<input type=\"hidden\" name=\"dataToExcel\" value=\"".htmlspecialchars($s, ENT_QUOTES)."\" />  ";
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
//если нажата кнопка Найти, то выход из скрипта
if (isset($_REQUEST['findProject']))
{
	exit;
}
//если нажата кнопка поиск, то выход из скрипта
if (isset($_REQUEST['btnSearch']))
{
	exit;
}
//если есть данные и нет ошибок и сообщений, перегружаем страницу
if (!empty($_POST) AND
$_REQUEST['rbselect']!="copy" AND
count($errors) == 0 AND
count($successes) == 0 )
{
	header("Location: $_SERVER[PHP_SELF]");
	exit();
}
//сбросить содержимое буфера
ob_end_flush();
?>