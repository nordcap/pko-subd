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
$st=startTime();
checkSession(); //проверка на присутствие в сессии идентификатора пользователя
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Автоматизированная система учета трудозатрат</title>
    <meta charset="utf-8">
	<link href="../favicon.ico" rel="icon" type="image/x-icon" />
    <link href="../style.css" rel="stylesheet">
	<script src="../js/jquery.js"></script>
  </head>
  <body>
<?php
$errors = array(); //объявляется массив ошибок
//include_once("../js/jscript.js");
//проверка на наличие прав доступа
if (($_SESSION['Name_Post'] == "Администратор") OR
($_SESSION['Name_Post'] == "Экономист"))
{
	print "<H2>Модуль редактирования табельного времени</H2><br />\n";
	print "<a href=\"Admin.php\">Страница администрирования</a><br />\n";
	print "<hr<br />\n";
	//если нажата кнопка Изменить
	if (!empty($_REQUEST['changeTime']))
	{
		checkEmptyTime(); //проверка на пустое значение времени = 0
		if ( count($errors) > 0)
		{
			display_errors();
		}
	}
	//form
	?>
	<form action='<?php echo $_SERVER['PHP_SELF']?>' method='post' class=' f-horizontal' style='margin-top:20px; margin-bottom:20px; margin-left:20px;'>
	<div class="g-2" style="background-color: rgb(226, 209, 180); 
					padding: 10px; 
					border-top-left-radius: 10px; 
					border-top-right-radius: 10px; 
					border-bottom-right-radius: 10px; 
					border-bottom-left-radius: 10px; 
					border: 2px solid rgb(134, 130, 104);
					padding-bottom: 0;">
		<div class='f-row'>
			<label>часы</label>
			<input type="text" name="Time" class="g-2"/>
		</div>
		<div class="f-row">
			<button type="submit" name = "changeTime"  value = "ok" class="f-bu f-bu-success">Изменить</button>
		</div>

	</div>
	


	<?php
	
	//отображение данных
	if ( count($errors) == 0 )
	{
		//отображаем таблицу плановой выработки по отделам
		ViewTableTabelTime();
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
endTime($st);
?>
</body>
</html>