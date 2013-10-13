<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.3.9
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей администрирования должностей
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
if ($_SESSION['Name_Post'] == "Администратор") {

    //если нажата кнопка Запрос,то
    if ( isset($_REQUEST['hidden']) ) {
        $query = "UPDATE block SET approve=".escapeshellarg($_REQUEST['rbselect']);
        if ( !($dbResult = mysql_query($query, $link)) ) {
            //print "запрос $Query не выполнен\n";
            processingError("$query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
        }
    }

    $flag = checkApprove();
    if ($flag === TRUE) {
        $block_true = 'checked';
        $block_false= '';
    }else {
        $block_true = '';
        $block_false= 'checked';

    }





    print "<H2>Модуль блокировок разрешений</H2><br />\n";
    print "<a href=\"Admin.php\">Страница администрирования</a><br />\n";
    print "<Hr Width=\"100%\" ALIGN=\"left\"><br />\n";

    //Таблица для редактирования
    //---------------------
    //Форма ввода
    print "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\">\n";
    print "<Table  Width=\"250px\" Border=\"0\" CellSpacing=\"0\" CellPadding=\"2\" class=\"tableReport\">\n";
    print "<Tr>\n";
    print "<Td>";
    print "<fieldset>";
    print "<legend>Утверждение работ</legend>";
    print "<input name=\"rbselect\" type=\"radio\" value=\"true\" $block_true>разрешить \n";
    print "<input name=\"rbselect\" type=\"radio\" value=\"false\" $block_false>запретить\n";
    print "</fieldset>\n";

    print "<Tr>\n";
    print "<Td><input type=\"submit\" value=\"    OK    \"> \n";
    print "<input name=\"hidden\" type=\"hidden\" value=\"data\">";
    print "</Table>\n";
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