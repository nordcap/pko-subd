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
include_once("../modules/lib/lib.php");
checkSession(); //проверка на присутствие в сессии идентификатора пользователя
//начать буферизацию вывода
ob_start();
require_once("begin1.php");
//include_once("../js/jscript.js");
//проверка на наличие прав доступа
if (($_SESSION['Name_Post'] == "Администратор") OR
($_SESSION['Name_Post'] == "Экономист") OR
($_SESSION['Name_Department'] == "ГД"))
{
	include_once("../modules/lib/lib.php");
	print "<H2>Модуль редактирования проектов</H2><br />\n";
	print "<a href=\"geod.php\">Панель ввода данных</a><br />\n";
	print "<Hr Width=\"100%\" ALIGN=\"left\"><br />\n";
	if (!empty($_POST))
	{
		//если нажата кнопка Запрос,то
		$errors = array(); //объявляется массив ошибок и успешных сообщений
		$successes = array();
		//проверка нажатия кнопки проверки на совпадение номера заявки
		if (isset($_REQUEST['checkTender']))
		{
			checkMismatchTender();
		}
		//при нажатия кнопки поиска, долен быть введен номер заявки
		if (isset($_REQUEST['findTender']))
		{
			if ( empty($_REQUEST['Number_Tender']) )
			{
				$errors[] = "<H3>Введите номер заявки</H3>";
			}
		}
		//обработка нажатия кнопки ОК
		if (isset($_REQUEST['rbselect']))
		{
			switch ( $_REQUEST['rbselect'] )
			{
				case "insert":
				checkEmptyTender();
				checkMatchTender();
				break;
				case "update":
				checkEmptyTender();
				break;
				case "customer":
				checkEmptyTender();
				break;
				case "copy":
				//если выбрана опция копирование данных
				$Comma_separated = implode(",", $_REQUEST['project']);
				$Query = "SELECT  Id, Number_Tender, Name_Tender, Number_UTR, id_Customer ".
				" FROM tender_geod ".
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
						$_POST['Customer'] = $row['id_Customer'];
						$_POST['Number_Tender'] = $row['Number_Tender'];
						$_POST['Number_UTR'] = $row['Number_UTR'];
						$_POST['Name_Tender'] = $row['Name_Tender'];
					}
					/* print phpinfo(32);   */
				}
				break;
			}
		}
		if ( count($successes) > 0 OR count($errors) > 0)
		{
			display_errors();
		}
	}
	//Таблица для редактирования
	//---------------------
    ?>
    <form action="<?php print $_SERVER['PHP_SELF']; ?>" method="post">
    <div class="g-8">
        <div class="f-row">
            <label>Номер заявки</label>
            <div class="f-input g-3">
                <input type="text" name="Number_Tender" value="<?php print $_POST['Number_Tender']; ?>" class="g-3"/>
                <button type="submit" name="checkTender"><img src="../img/check.png" style="vertical-align: bottom">Проверить</button>
                <button type="submit" name="findTender"><img src="../img/check.png" style="vertical-align: bottom">Найти</button>
            </div>
         </div>
        <div class="f-row">
            <label>Название заявки</label>
            <div class="f-input">
                <input type="text" name="Name_Tender" value="<?php print $_POST['Name_Tender']; ?>" class="g-3"/>
            </div>
        </div>
        <div class="f-row">
            <label>Номер УТР</label>
            <div class="f-input">
                <input type="text" name="Number_UTR" value="<?php print $_POST['Number_UTR']; ?>" class="g-3"/>
            </div>
        </div>
        <div class="f-row">
            <label>Заказчик</label>
            <div class="f-input">
                <select name="Customer" class="g-3">
                    <option><?php print getNameCustomer($_POST['Customer']); ?></option>
                    <?php
                    	//заполнение списка заказчиков
                        getArrayNameCustomer();
                    ?>
                </select>
            </div>
        </div>
        <div class="f-actions">
        <button type="submit" name="rbselect" value="select" class="f-bu">Общая</button>
        <button type="submit" name="rbselect" value="insert" class="f-bu f-bu-success">Вставка</button>
        <button type="submit" name="rbselect" value="update" class="f-bu f-bu-default">Изменить</button>
        <button type="submit" name="rbselect" value="customer" class="f-bu f-bu-default">Изменить заказчика</button>
        <button type="submit" name="rbselect" value="delete" class="f-bu f-bu-warning">Удалить</button>
        <button type="submit" name="rbselect" value="copy" class="f-bu f-bu-default">Копировать</button>
        </div>
   </div>
    <?php
	//вызов отображения таблицы
	if ( count($errors) == 0 )
	{
		ViewTableTender();
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
print "</body>\n";
print "</html>\n";
//если нажата кнопка Найти, то выход из скрипта
if (isset($_REQUEST['findTender']))
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
?>
<script  type="text/javascript">
$(document).ready(function(){
     $('.f-table-zebra tr>td:nth-child(3)').css('text-align', 'left');
  });
</script>
<?php
//сбросить содержимое буфера
ob_end_flush();
?>