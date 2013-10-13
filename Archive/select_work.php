<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является обработчиком события onchange выпадающего списка
"выбор марки"
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
//session_start();
include_once("../modules/lib/lib.php");


$Query = "SELECT work.Id, mark.Name_Mark, work.Name_Work, work.Type_Work ".
"FROM man_hour, mark, work ".
"WHERE ".
"((mark.id = man_hour.id_Mark) AND ".
"(work.id = man_hour.id_Work) AND ".
"(mark.id =".escapeshellarg($_REQUEST['data']).")) ".
"ORDER BY work.Type_Work";
if ( !($dbResult = mysql_query($Query, $link)) )
{
	print "Выборка $Query не удалась!\n".mysql_error();
}
else	{
	//заполнение списка вида работ
	$str = "<select name=\"Name_Work\" id=\"id_name_work\" class=\"g-6\">\n";
	while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
	{
		$str = $str."<option value=\"{$row['Id']}\">{$row['Name_Work']} - {$row['Type_Work']}</option>\n";
	}
	$str = $str."</select>\n";
}
//запись информации в файл errors.txt
// file_put_contents("query.txt",$Query);
//результат помещаем в переменную RESULT (выходная переменная)
	print $str;
?>
