<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.3.9
+-----------------------------------------------------------------------+
| Copyright (c) 2013 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей отчета "определение норматива трудоемкости"
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
ob_start();
include_once("../modules/lib/lib.php");
$st=startTime();
$count = count($_SESSION['chkYearSearch']);

$query = "
SELECT
design.id_Work AS id_work,
mark.Name_Mark,
`work`.Name_Work, 
`work`.Type_Work,
`work`.`Comment`,
man_hour.Man_Hour 
FROM design
INNER JOIN `work` ON design.id_Work=`work`.Id
INNER JOIN mark ON design.id_Mark=mark.Id
INNER JOIN man_hour ON man_hour.id_Mark=mark.Id AND man_hour.id_Work=`work`.Id
WHERE `work`.`Comment` LIKE '%А_' AND design.statusBVP>0
GROUP BY design.id_Mark, design.id_Work
ORDER BY mark.Name_Mark, `work`.`Comment`
";
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Автоматизированная система учета трудозатрат</title>
<META charset="utf8">
<link  rel="stylesheet" type="text/css" href="../css/framework.css">
<link  rel="stylesheet" type="text/css" href="../css/user_1.css">
<link href="../favicon.ico" rel="icon" type="image/x-icon" />
<!--<script language="JavaScript" src="../js/jquery.js" type="text/javascript"></script>
<script language="JavaScript" src="../js/jquery.date_input.js" type="text/javascript"></script>
<script language="JavaScript" src="../js/jscript.js" type="text/javascript"></script>
<script type="text/javascript">$($.date_input.initialize);</script>-->
</head>
<body>
<a href="object.php">Назад</a>
<table class="f-table-zebra">
	<caption>Определение нормативов трудоемкости по годам</caption>
	<thead>
		<tr>
			<th rowspan="2" class="g-2">
				Вид документации
			</th>
			<th rowspan="2" class="g-2">
				Тип
			</th>			
			<th rowspan="2" class="g-1">
				Марка чертежа(по видам документации)
			</th>
			<th rowspan="2">
				Формат листа
			</th>
			<th colspan="<?php print($count+1)?>">
				Кол-во выполненных листов
			</th>
			<th colspan="<?php print($count+1)?>">
				Фактически затраченное время, час
			</th>
			<th colspan="<?php print($count+1)?>">
				Утвержденные трудозатраты, ч./час
			</th>
			<th rowspan="2" class="g-1">
				Норма времени на разработку ед.продукции, чел./час
			</th>
			<th rowspan="2" class="g-1">
				Фактически затраченное время на ед.вида документации (кол. 6/5)
			</th>
			<th rowspan="2" class="g-1">
				Затраченное время на ед.вида документации (кол. 7/5)
			</th>
			<th rowspan="2" class="g-1">
				% превышения (кол. 8/9*100%)
			</th>
		</tr>
		<tr>
		<?php
		//вывод годов по которым нужно проводить сравнение
		for($i = 0; $i < 3; $i++)
		{
			foreach($_SESSION['chkYearSearch'] as $year)
			{
				print('<th>'.$year.'</th>');
			}
			print('<th>Всего</th>');
			
		}
		?>
		</tr>
		<tr>
			<th>1</th>
			<th>2</th>
			<th>3</th>
			<th>4</th>
			<th colspan="<?php print($count+1)?>">5</th>
			<th colspan="<?php print($count+1)?>">6</th>
			<th colspan="<?php print($count+1)?>">7</th>
			<th>8</th>
			<th>9</th>
			<th>10</th>
			<th>11</th>
		</tr>
	</thead>
	<tbody>
		<?php
		
		if ( !($dbResult = mysql_query($query, $link)) )
		{
			//print "Выборка $Query не удалась!\n".mysql_error();
			processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
		else {
			while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
			{	
				$data_year = array();
				foreach($_SESSION['chkYearSearch'] as $year)
				{
					$data_year[$year] = getDataByMark($row['id_work'],  $year, $_SESSION['rbPeriod']);
				}	
		
				
				print "<tr>";
				printZeroTD($row['Type_Work']); // 1 столбец
				printZeroTD($row['Name_Work']); // 2 столбец
				printZeroTD($row['Name_Mark']); // 3 столбец
				
				$format = explode(' ', $row['Comment']);
				
				printZeroTD($format[1]); //4 столбец
				//вывод данных по годам по которым нужно проводить сравнение
				
				
				$sum_list = array();
				//5 столбец
				foreach($_SESSION['chkYearSearch'] as $year)
				{
					//A1 or A3 or A4
					switch($format[1])
					{
						case 'А1':
						$list = $data_year[$year]['A1'] +  $data_year[$year]['A3']/4 + $data_year[$year]['A4']/8;
						break;

						case 'А3':
						$list = $data_year[$year]['A3'] + 2*$data_year[$year]['A4'] + $data_year[$year]['A1']/4;
						break;

						case 'А4':
						$list = $data_year[$year]['A4'] + 2*$data_year[$year]['A3'] + 8*$data_year[$year]['A1'];
						break;
					}
					printZeroTD(round($list, 0));
					$sum_list[] = $list;
				}

				$sum_list_value = round(array_sum($sum_list), 0); 
				printZeroTD($sum_list_value);//конец 5 столбца
				
				//6 столбец
				$sum_time = array();
				foreach($_SESSION['chkYearSearch'] as $year)
				{
					$fact_time = round($data_year[$year]['Time'], 0);//фактическое потраченное время
					$sum_time[] = $fact_time;
					printZeroTD($fact_time);
				}				
				$sum_time_value =  round(array_sum($sum_time), 0);
				printZeroTD($sum_time_value);//конец 6 столбца
				
				//7 столбец
				$sum_man_hour = array();
				foreach($_SESSION['chkYearSearch'] as $year)
				{
					$man_hour = round($data_year[$year]['Man_Hour_Sum'], 0);//фактическое потраченное время
					$sum_man_hour[] = $man_hour;
					printZeroTD($man_hour);
				}				
				$sum_man_hour_value = round(array_sum($sum_man_hour), 0);
				printZeroTD($sum_man_hour_value);//конец 7 столбца
				
				printZeroTD($row['Man_Hour']);//8 столбец
				
				
				$time_simple = round($sum_time_value/$sum_list_value, 2);//фактически затраченное время на ед вида документации
				printZeroTD($time_simple); //9 столбец 6/5
				$time_mh = round($sum_man_hour_value/$sum_list_value, 2);
				printZeroTD($time_mh);//10 столбец
				
				
				printZeroTD(round(100*$row['Man_Hour']/$time_mh,0)); //11 столбец 8/10
				print "</tr>";
			}
		}
		
		?>
	</tbody>
</table>

<?php 	endTime($st);?>
</body>
</html>
<?php ob_end_flush();?>