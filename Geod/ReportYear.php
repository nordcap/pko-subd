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
print "<table class=\"f-table-zebra\">\n";
print "<caption>Ведомость расчета стоимости выполнения работ за год.<br />Бюро генплана и геодезии</caption>\n";
print "<thread>\n";
print "<TR>\n";
print "<TH colspan=12>Месяцы</TH>".
"<TH rowspan=2>Итого,руб</TH>".
"<TR>\n".
"<TH>январь</TH>".
"<TH>февраль</TH>".
"<TH>март</TH>".
"<TH>апрель</TH>".
"<TH>май</TH>".
"<TH>июнь</TH>".
"<TH>июль</TH>".
"<TH>август</TH>".
"<TH>сентябрь</TH>".
"<TH>октябрь</TH>".
"<TH>ноябрь</TH>".
"<TH>декабрь</TH>";
print "</TR>\n"; 
print "</thread>\n";

for ($i = 1; $i < 13; $i++)
{
	$Query1 = "SELECT SUM(geod.Total) AS TotalMonth ".
	"FROM geod ".
	"WHERE ";
	$Query2 = "(geod.Date>=".escapeshellarg($_SESSION['arrayMonth'][$i]).
	") AND (geod.Date<".escapeshellarg($_SESSION['arrayMonth'][$i+1]).") ";
	//результирующий запрос
	$Query = $Query1.$Query2;
	//file_put_contents("query.txt",$Query);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		print "Выборка $Query не удалась!\n";
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
			//записываем в массив данные по каждому месяцу- округлим до 2 знака после запятой
			$array_Year[$i] = round($row['TotalMonth'],2);
		}
	}
}
print "<TR align=center>\n";
foreach($array_Year as $key=>$Month)
{
    printZeroTD($Month);
}
//суммируем значения за весь год 1-12 месяцев
$sum = array_sum($array_Year);
printZeroTD($sum);
$_SESSION['Query'] = null;
?>
</table>
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
     $('f-table-zebra tr>td:nth-child(5)').css('text-align', 'left'); //наименование

  });
</script>
</html>