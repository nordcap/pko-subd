<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей печати геодезических работ за месяц и год
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
include_once("../modules/lib/lib.php");
require_once("begin1.php");
print "<a href=\"sapr.php\" title=\"Поиск\">Назад</a>\n";
print "<br /><br />";
$Query2 =  getIntervalDate($_SESSION['lstMonth'],$_SESSION['lstYear'],"sapr","Date");
$Query1 = "SELECT sapr.Id, sapr.Date, sapr.Name_Work, ".
"tableCustomer.Family AS FamilyCustomer, tableCustomer.Name AS NameCustomer, tableCustomer.Patronymic AS PatronymicCustomer, ".
"tableEmployee.Family AS FamilyEmployee, tableEmployee.Name AS NameEmployee, tableEmployee.Patronymic AS PatronymicEmployee, ".
"sapr.Time, sapr.Comment  ".
"FROM sapr, employee AS tableCustomer, employee AS tableEmployee ".
"WHERE ".
"(sapr.id_Employee = tableEmployee.id) ". //исполнитель
" AND (sapr.id_EmployeeCustomer = tableCustomer.id) "; //заказчик
//file_put_contents("query.txt",$Query1);
//поиск по выбранным сотрудникам бюро САПР
if ( $_SESSION['lstPerformerReport'] != "бюро САПР" )
{
	$Query3 = " AND (sapr.id_Employee = ".escapeshellarg($_SESSION['lstPerformerReport']).") ";
}
else {
	$Query3 = "";
}
$Query4 = " ORDER BY sapr.Date DESC";
//соединяем все запросы в один
$Query = $Query1.$Query2.$Query3.$Query4;

//рисуем таблицу
ViewSapr($Query,"report");
$_SESSION['Query'] = null;
?>
<div class="g-row">
    <div class="g-2">Начальник бюро:</div>
    <div class="g-2">_______________</div>
</div>
<div class="g-row">
    <div class="g-2">Начальник отдела:</div>
    <div class="g-2">________________</div>
</div>
</body>
<script  type="text/javascript">
$(document).ready(function(){
                            $('.f-table-zebra tr>td:nth-child(3)').css('text-align', 'left');
                            });

</script>
</html>