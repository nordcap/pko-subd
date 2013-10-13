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
//include_once("../js/jscript.js");
print "<a href=\"archive.php\" title=\"Поиск\">Назад</a>\n";
print "<p></p>";
$Query1 = "SELECT archive.Id,
archive.Date, archive.Sheet_A1, archive.Sheet_A4, archive.NumberDraw, archive.Comment,
mark.Name_Mark, project.Number_Project, project.Name_Project,
employee.Name, employee.Family, employee.Patronymic ".
" FROM archive, employee, project, mark ".
" WHERE ".
"(archive.id_Mark = mark.id) AND ".
"(archive.id_Project = project.id) AND ".
"(archive.id_Employee = employee.id) ";
//выбираем значения месяца и года
$Query2 = getIntervalDate($_SESSION['lstMonth'],$_SESSION['lstYear'],"archive","Date",$_SESSION['rbPeriod']);

if (isset($_SESSION['NumberProjectReport']) AND $_SESSION['NumberProjectReport'] != "Выберите номер")
{
	$QueryProject = " AND (archive.id_Project =".escapeshellarg($_SESSION['NumberProjectReport']).")";
}
else {
	$QueryProject = "";
}
if (isset($_SESSION['NameMarkReport']) AND $_SESSION['NameMarkReport'] != "Выберите марку")
{
	$QueryMark = " AND (archive.id_Mark =".escapeshellarg($_SESSION['NameMarkReport']).")";
}
else {
	$QueryMark = "";
}
if (isset($_SESSION['lstPerformerReport']) AND $_SESSION['lstPerformerReport'] != "Выберите сотрудника")
{
	$QueryEmployee = " AND (archive.id_Employee =".escapeshellarg($_SESSION['lstPerformerReport']).")";
}
else {
	$QueryEmployee = "";
}
$Query3 = " ORDER BY archive.Date DESC";
//соединяем все запросы в один
$Query = $Query1.$Query2.$QueryProject.$QueryMark.$QueryEmployee.$Query3;
//file_put_contents("query.txt", $Query);
//рисуем таблицу
ViewArchive($Query,"report");
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
     $('.f-table-zebra tr>td:nth-child(5)').css('text-align', 'left');
     $('.f-table-zebra tr>td:nth-child(8)').css('text-align', 'left');
  });
</script>
</html>