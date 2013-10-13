<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей изменения пароля сотрудника
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();     //инициализирум механизм сесссий
//начать буферизацию вывода
ob_start();
include_once("js/jscript.js");
require_once("begin1.php");
print "<a href=\"index1.php\">Назад</a><br />\n";

?>
<div class="g-5" style="margin: 30px auto;">
    <form action="<?php print $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="g-5">
            <div class="g-2" style="margin: 0 auto;">
                <h3>Смена пароля</h3>
            </div>

            <div class="f-row">
                <label>Введите старый пароль</label>
                    <div class="f-input">
                        <input Type="password" Name="OldPassword">
                    </div>
            </div>
            <div class="f-row">
                <label>Введите новый пароль</label>
                    <div class="f-input">
                        <input Type="password" Name="NewPassword">
                    </div>
            </div>
            <div class="f-row">
                    <button type="submit" name="Вход"  value="ОК" class="f-bu" style="margin-left: 220px; width: 100px;">OK</button>
            </div>
        </div>
    </form>
</div>
<?php

//---------------------
//сделать проверка на корректность ввода
if ( isset($_REQUEST['Вход']) )
{
	include_once("modules/lib/connect.php");
	include_once("modules/lib/lib.php");
	if ( $_SESSION['Password'] == cryptParol(trim(strtolower($_REQUEST['OldPassword']))) )
	{
		//проверка  cryptParol(trim(strtolower($_REQUEST['Password'])))
		$Query = "UPDATE employee SET Password= ".escapeshellarg(cryptParol(trim(strtolower( $_REQUEST['NewPassword'] )))).
		"  WHERE Id=".escapeshellarg( $_SESSION['Id_Employee'] );
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			print "Запрос не выполнен!<br />\n";
		}
		else {
            print "<div class=\"g success\" style=\"margin:auto; position:relative; top:150px; border-radius:10px;\">\n";
			print "<H3>Вы успешно авторизовались! Перейдите по ссылке на главную страницу</H3>\n";
            print "</div>\n";
            header("location:index1.php");
		}
	}
	else {
      print "<div class=\"g error\" style=\"margin:auto; position:relative; top:150px; border-radius:10px;\">\n";
      print "<H3>Не верные имя или пароль</H3>\n";
      print "</div>\n";
	}
}
require_once("end1.php");
//сбросить содержимое буфера
ob_end_flush();
?>