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
print "<a href=\"geod.php\" title=\"Поиск\">Назад</a>\n";
print "<br /><br />";
$Query2 =  getIntervalDate($_SESSION['lstMonth'],$_SESSION['lstYear'],"geod","Date",$_SESSION['rbPeriod']);

$Query1 = "SELECT geod.Id, geod.Date, geod.Name_Work, geod.Unit_Measurement, ".
"geod.Category, geod.Price, geod.Number, geod.Amount, geod.Coefficient, geod.Comment, geod.Time, ".
"geod.Total, geod.id_Employee, geod.DateSubmit, tender_geod.Number_Tender, ".
"employee.Family, employee.Name, employee.Patronymic, project.Number_Project ".
"FROM geod, employee, project, tender_geod ".
"WHERE ".
"(geod.id_Employee = employee.id) AND ".
"(geod.id_Project = project.id) AND ".
"(geod.id_Tender = tender_geod.id) ";
$Query3 = " ORDER BY geod.Date DESC";
//результирующий запрос
$Query = $Query1.$Query2.$Query3;
//file_put_contents("query.txt",$Query);
//рисуем таблицу
$result = ViewGeod($Query,"report");
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
<p></p>
<?php 
		$s = serialize($result);    //происходит сериализация массива для передачи по скрытому полю ввода
		print "<form action=\"../Report/report_excel/saveExcel_Geod.php\" method=\"POST\">\n";
		print "<input type=\"image\" name=\"btnSaveExcel\" src=\"../img/file_xls.png\" width=\"48\" height=\"48\" border=\"0\" alt=\"сохранение в Экселе\" />  ";
		print "<div class=\"hint\"><span>Подсказка! </span>Сохранение в Excel</div>";
		print "<input type=\"hidden\" name=\"dataToExcel\" value=\"".htmlspecialchars($s, ENT_QUOTES)."\" />  ";
		print "</form>";
?>
</body>
<script  type="text/javascript">
$(document).ready(function(){
     $('.f-table-zebra tr>td:nth-child(5)').css('text-align', 'left');
     $('.f-table-zebra tr>td:nth-child(11)').css('text-align', 'left');
     $('.f-table-zebra tr>td:nth-child(11)').css('width', '200px');
  });
</script>
</html>