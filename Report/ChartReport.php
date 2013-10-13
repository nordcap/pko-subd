<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.3.9
+-----------------------------------------------------------------------+
| Copyright (c) 2013 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей отчета трудоемкости работ за год по отделам
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
//начать буферизацию вывода
ob_start();
include_once("../modules/lib/lib.php");
require_once("begin1.php");
include_once("../js/jscript.js");
print "<a href=\"object.php\">Назад</a><br />\n";
//инициализация массива времени

$arraySum = InitializationSumCard(13);
$array_timeReport = array();

foreach($_SESSION['chkYear'] as $key=>$valueYear)
{
for ($i = 1; $i < 13; $i++)
{
switch ($_SESSION['rbReport'])
{
  case "man_hour":
  $QueryDate = getIntervalDate($i,$valueYear,"design","Date",$_SESSION['rbPeriod']);

  $Query =" SELECT  SUM(Man_Hour_Sum) AS Man_Hour_Sum ".
          " FROM design,employee,department WHERE ".
      		"(design.id_Employee = employee.Id) AND ".
      		"(department.Id = employee.id_Department) AND ".
      		"(department.Id <> 22) AND (department.Id <> 24) AND ".
          "(department.Id <> 25) ".$QueryDate;
  break;

	case "project_output":
  $QueryDate = getIntervalDate($i,$valueYear,"design","statusBVP",$_SESSION['rbPeriod']);

  $Query =" SELECT  SUM(Man_Hour_Sum) AS Man_Hour_Sum ".
          " FROM design,employee,department WHERE ".
      		"(design.id_Employee = employee.Id) AND ".
      		"(department.Id = employee.id_Department) AND ".
      		"(department.Id <> 22) AND (department.Id <> 24) AND ".
          "(department.Id <> 25) ".$QueryDate;
	break;

	case "project_outputA1":
  $QueryDate = getIntervalDate($i,$valueYear,"design","statusBVP",$_SESSION['rbPeriod']);

  $Query =" SELECT  SUM(design.Sheet_A1) AS sumSheet_A1 ".
          " FROM design,employee,department WHERE ".
      		"(design.id_Employee = employee.Id) AND ".
      		"(department.Id = employee.id_Department) AND ".
      		"(department.Id <> 22) AND (department.Id <> 24) AND ".
          "(department.Id <> 25) AND (department.Id <> 19) ".$QueryDate;
	$Sheet =  "sumSheet_A1";
	break;

	case "project_outputA4":
  $QueryDate = getIntervalDate($i,$valueYear,"design","statusBVP",$_SESSION['rbPeriod']);

  $Query =" SELECT  SUM(design.Sheet_A4+2*design.Sheet_A3) AS sumSheet_A4 ".
          " FROM design,employee,department WHERE ".
      		"(design.id_Employee = employee.Id) AND ".
      		"(department.Id = employee.id_Department) AND ".
      		"(department.Id <> 22) AND (department.Id <> 24) AND ".
          "(department.Id <> 25) AND (department.Id <> 19) ".$QueryDate;

	$Sheet =  "sumSheet_A4";
	break;
}

	//file_put_contents("query.txt",$Query);
	//print($Query."<br />");
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n";
		processingError("$Query ", __FILE__, __LINE__, __FUNCTION__);
    //print mysql_error();
	}
	else {
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
		{
      switch ($_SESSION['rbReport'])
      {
        case "man_hour":   //общие трудозатраты
        if ($row['Man_Hour_Sum'] > 0)
    		  {
      			$array_timeReport[$valueYear][$i] = round($row['Man_Hour_Sum'],0);
    		  } else {
            continue;
    		  }
        break;

      	case "project_output":  //утвержденные трудозатраты
        if ($row['Man_Hour_Sum'] > 0)
    		  {
      			$array_timeReport[$valueYear][$i] = round($row['Man_Hour_Sum'],0);
    		  } else {
            continue;
    		  }
      	break;

      	case "project_outputA1":
        if ($row[$Sheet] > 0)
    		  {
      			$array_timeReport[$valueYear][$i] = round($row[$Sheet],1);
    		  } else {
            continue;
    		  }
      	break;

      	case "project_outputA4":
        if ($row[$Sheet] > 0)
    		  {
      			$array_timeReport[$valueYear][$i] = round($row[$Sheet],1);
    		  } else {
            continue;
    		  }
      	break;
      }
		}
	}
}
}
?>
<script language="JavaScript" type="text/javascript">
var chart;
$(document).ready(function() {
	chart = new Highcharts.Chart({
		chart: {
			renderTo: 'container',
			type: 'spline'
		},
		title: {
			text: 'график сравнения производительности'
		},
		subtitle: {
		  <?php
      switch ($_SESSION['rbReport'])
      {
        case "man_hour":
        ?>
        text: 'по обшим трудозатратам'
        <?php
        break;

        case "project_output":
        ?>
        text: 'по утверждённым трудозатратам'
        <?php
        break;

        case "project_outputA1":
        ?>
        text: 'по листажу А1'
        <?php
        break;

        case "project_outputA4":
        ?>
        text: 'по листажу А4'
        <?php
        break;
      }
		  ?>

		},
		xAxis: {
			categories: ['январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь']
		},
		yAxis: {
			title: {
		  <?php
      switch ($_SESSION['rbReport'])
      {
        case "man_hour":
        ?>
				text: 'чел./ч.'
        <?php
        break;

        case "project_output":
        ?>
				text: 'чел./ч.'
        <?php
        break;

        case "project_outputA1":
        ?>
				text: 'листы'
        <?php
        break;

        case "project_outputA4":
        ?>
				text: 'листы'
        <?php
        break;
      }
		  ?>

			}
		},
		tooltip: {
			enabled: false,
			formatter: function() {
				return '<b>'+ this.series.name +'</b><br/>'+
					this.x +': '+ this.y +'°C';
			}
		},
		plotOptions: {
			spline: {
				dataLabels: {
					enabled: true
				},
				enableMouseTracking: false
			}
		},
		series: [
		  <?php foreach($array_timeReport as $year=>$month) { ?>
		      {
		      name: <?php print("'".$year."'"); ?>,
			  data: [
		      <?php foreach($month as $key=>$value)
		      {
		        print $value;
		        print ",";
		      }
		      ?>    ]
				},
    <?php } ?>
            ]



	});
});
 </script>
<script language="JavaScript" src="../js/Highcharts-2.2.5/js/highcharts.js" type="text/javascript"></script>
<script language="JavaScript" src="../js/Highcharts-2.2.5/js/modules/exporting.js" type="text/javascript"></script>
<?php
switch ($_SESSION['rbShema'])
{
  case "default":

  break;

  case "grid":
  print "<script language=\"JavaScript\" src=\"../js/Highcharts-2.2.5/js/themes/grid.js\" type=\"text/javascript\"></script>";
  break;

  case "gray":
  print "<script language=\"JavaScript\" src=\"../js/Highcharts-2.2.5/js/themes/gray.js\" type=\"text/javascript\"></script>";
  break;

  case "dark_blue":
  print "<script language=\"JavaScript\" src=\"../js/Highcharts-2.2.5/js/themes/dark-blue.js\" type=\"text/javascript\"></script>";
  break;

  case "dark_green":
  print "<script language=\"JavaScript\" src=\"../js/Highcharts-2.2.5/js/themes/dark-green.js\" type=\"text/javascript\"></script>";
  break;


}
?>
<div id="container" style="min-width: 400px; height: 500px; margin: 0 auto"></div>
<?php
require_once("../end1.php");
//сбросить содержимое буфера
ob_end_flush();
