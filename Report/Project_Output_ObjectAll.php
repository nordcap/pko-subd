<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей отчета трудозатрат работ по объектам
по всем подразделениям
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
//error_reporting(1); //подавление сообщение о сессиях
session_start();

include_once("../modules/lib/lib.php");
require_once("begin1.php");
include_once("../js/jscript.js");
print "<a href=\"object.php\">Назад</a><br /><br />\n";
//print "<a href=\"../Graph/graphReport.php?Type=Project_Output_Object\">График загрузки подразделений</a><br />\n";
print "<CENTER><H2>Свод трудозатрат УПИРиИ по объектам</H2></CENTER><hr/>\n";


	print "<b>Месяц:</b>&nbsp;";
	print ($_SESSION['Month'][$_SESSION['lstMonth']]."<br />");


	print "<b>Год:</b>&nbsp;";
	print ($_SESSION['lstYear']."<br />");

	print "<b>Период:</b>&nbsp;";
	print ($_SESSION['rbPeriod']."<br />");
	print "<p></p>";

//проверка времени
checkTrueTime($_SESSION['DateBegin'], $_SESSION['DateEnd']);

//если есть ошибки- выводим их на экран
if ( count($errors) > 0 )
{
	display_errors();
}
if ( count($errors) == 0 )
{
		//определяем запрос поиска проектов, которые заканчиваются в указанном отчетном периоде
		if (!empty($_SESSION['DateBegin']) OR !empty($_SESSION['DateEnd']))
		{
			$IntervalProject = " AND ((project.DateCloseProject>=".escapeshellarg($_SESSION['DateBegin']).
			") AND (project.DateCloseProject<".escapeshellarg($_SESSION['DateEnd']+3600*24)."))";
		}
		else {
			$IntervalProject =  getIntervalDate(
				$_SESSION['lstMonth'],
				$_SESSION['lstYear'],
				"project",
				"DateCloseProject",
				$_SESSION['rbPeriod']);
		}
	//------------------------------------------------------------------------------------

	switch ($_SESSION['rbCategry'])
	{
		case "man_hour":
		$sum = " SUM(design.Man_Hour_Sum) AS Man_Hour_Sum ";
			if (!empty($_SESSION['DateBegin']) OR !empty($_SESSION['DateEnd']))
		{
			$Query2 = " AND ((design.Date>=".escapeshellarg($_SESSION['DateBegin']).
			") AND (design.Date<".escapeshellarg($_SESSION['DateEnd']+3600*24)."))";
		}
		else {
			$Query2 = getIntervalDate(
			$_SESSION['lstMonth'],
			$_SESSION['lstYear'],
			"design",
			"Date",
			$_SESSION['rbPeriod']);
		}
		break;
		
		case "project_output":
		$sum = " SUM(design.Man_Hour_Sum) AS Man_Hour_Sum ";
		if (!empty($_SESSION['DateBegin']) OR !empty($_SESSION['DateEnd']))
		{
			$Query2 = " AND ((design.statusBVP>=".escapeshellarg($_SESSION['DateBegin']).
			") AND (design.statusBVP<".escapeshellarg($_SESSION['DateEnd']+3600*24)."))";
		}
		else {
			$Query2 = getIntervalDate(
			$_SESSION['lstMonth'],
			$_SESSION['lstYear'],
			"design",
			"statusBVP",
			$_SESSION['rbPeriod']);
		}
		break;
		
		case "project_output_archive":
		$sum = " SUM(design.Man_Hour_Sum) AS Man_Hour_Sum ";
		if (!empty($_SESSION['DateBegin']) OR !empty($_SESSION['DateEnd']))
		{
			$Query2 = " AND ((design.approvalBVP>=".escapeshellarg($_SESSION['DateBegin']).
			") AND (design.approvalBVP<".escapeshellarg($_SESSION['DateEnd']+3600*24)."))";
		}
		else {
			$Query2 = getIntervalDate(
			$_SESSION['lstMonth'],
			$_SESSION['lstYear'],
			"design",
			"approvalBVP",
			$_SESSION['rbPeriod']);
		}
		break;
		
		
		case "time":
		$sum = " SUM(design.Time) AS Man_Hour_Sum ";
		 if (!empty($_SESSION['DateBegin']) OR !empty($_SESSION['DateEnd']))
		{
			$Query2 = " AND ((design.statusBVP>=".escapeshellarg($_SESSION['DateBegin']).
			") AND (design.statusBVP<".escapeshellarg($_SESSION['DateEnd']+3600*24)."))";
		}
		else {
			$Query2 = getIntervalDate(
			$_SESSION['lstMonth'],
			$_SESSION['lstYear'],
			"design",
			"statusBVP",
			$_SESSION['rbPeriod']);
		}		
		break;		
	}

		if (!empty($_SESSION['DateBegin']) OR !empty($_SESSION['DateEnd']))
		{
			$QueryOffPlan = " AND ((design.offPlan>=".escapeshellarg($_SESSION['DateBegin']).
			") AND (design.offPlan<".escapeshellarg($_SESSION['DateEnd']+3600*24)."))";
		}
		else {
			$QueryOffPlan = getIntervalDate($_SESSION['lstMonth'],$_SESSION['lstYear'],"design","offPlan",$_SESSION['rbPeriod']);
		}

	$Query1 = "SELECT project.Id, project.Number_Project, project.Number_UTR, project.Name_Project,
	customer.Number_Customer, customer.Name_Customer,project.Customer_Service, project.Plant_Destination, ".
	$sum.
	" FROM design,project,customer ".
	" WHERE ".
	" (design.id_Project = project.Id) AND ".
	" (project.id_Customer = Customer.Id) ";
	$Query3 = " GROUP BY project.id ";
	$Query5 = "	ORDER BY Number_Customer, Number_Project ";
	if ( $_SESSION['lstCustomer'] != "Выбор заказчика" )
	{
		$Query4 = " AND (project.Id_Customer=".escapeshellarg($_SESSION['lstCustomer']).")";
	}
	else {
		$Query4 = "";
	}

		$QueryDateBVP = getIntervalFromDate(1,
		2008,
		"design",
		"statusBVP",
		"1-31");

	//результирующий запрос
	$Query = $Query1.$Query2.$Query4;
	//file_put_contents("query.txt",$Query);
	//print($Query);
	//если выбран статус проекта - окс, план, внеплан, азот-сервис, сторонние
	if($_SESSION['chkstatusSearch'])
	{
	    foreach($_SESSION['chkstatusSearch'] as $key=>$value)
	    {
	       if ($value == "OKC" OR $value == "Plan")
	       {
	          $Status = " AND  project.StatusProject = ".escapeshellarg($value).
	                    " AND  customer.Number_Customer <> '24'".
	                    " AND  customer.Number_Customer <> '100'";
	       }
	       elseif ($value == "OverPlan")
	       {
	          $Status = " AND  project.StatusProject = ".escapeshellarg($value).
	                    " AND  customer.Number_Customer <> '24'".
	                    " AND  customer.Number_Customer <> '100'";
	       }
	       elseif ($value == "AzotServ")
	       {
	          $Status = " AND  customer.Number_Customer = '24'";

	       }
	       elseif ($value == "Alien")
	       {
	          $Status = " AND  customer.Number_Customer = '100'";

	       }

	       //формируем запрос по статусу
	       $QT[] = $Query.$Status.$Query3;

	    }


	    	 $Count = count($QT);
	         //если выбраны несколько статусов, то объединяем запросы
	         if($Count > 1 )
	         {
	         $Query = "";
	    		foreach($QT as $Key=>$Value)
	    		{
	    			if (($Count - 1) == $Key)
	    			{
	    				$Query = $Query."(".$Value.")";
	    				break;
	    			}
	    			else {
	    				$Query = $Query."(".$Value.") UNION ";
	    			}
	    		}
	         } else
	         {
	             $Query = $QT[0];
	         }
	$Query = $Query.$Query5;
	}
	else
	{
	$Query = $Query.$Query3.$Query5;
	}

	//file_put_contents("query.txt",$Query);
	//print_r($Query);
	//составляем объединение результатов работы нескольких команд SELECT в один набор результатов.
	//таблица HTML

	print "<TABLE Width=\"100%\" Border=\"2\" CellSpacing=\"0\" CellPadding=\"2\">\n";
	print "<TR bgcolor = #E8E8E8>\n";
	print "<TH width=5%>№ п/п</TH>".
	"<TH width=10%>Регистрационный<br /> номер в УПИРиИ</TH>".
	"<TH width=5%>Регистрационный<br /> номер в УТР</TH>".
	"<TH width=10%>Служба заказчика</TH>".
	"<TH width=10%>Цех, в котором планируется<br /> осуществить проект</TH>".
	"<TH width=50%>Наименование работы</TH>";
	if($_SESSION['rbCategry'] == "time"){
		print "<TH width=5%>Фактическое<br/>затр.время,<br />час</TH>";
	} else {
		print "<TH width=5%>Трудозатраты за мес,<br /> чел.-час</TH>";	
	}
	
	print "<TH width=5%>Внеплановые трудозатраты за период,<br /> чел.-час</TH>";
	print "<TH width=5%>Фактические трудозатраты<br /> всего по объекту, чел-час</TH>";
	print "<TR>\n";

	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		//print "Выборка не удалась!\n";
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	else {
		$ManHourSum = 0;
		$ManHourSumOffPlan = 0;
	    $ManHourSumAll = 0;
		$man_hour_smeta = 0;
		$man_hour_offPlan = 0 ;
		$man_hour_all = 0;
		$n=0;
		$result = array();
		while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH))
		{
			//расчетный блок для вычисления общих трудозатрат, внеплановых и сметных затрат как 5% от общих тр-т
	        $man_hour_offPlan 	= round(getManHourByProject_id($row['Id'], $QueryOffPlan), 0);//внеплановые труд-ты
			$man_hour_all 		= round(getManHourByProject_id($row['Id'], $QueryDateBVP), 0);//фактич.трудозатраты всего по объекту
			//если в списке ожидаемых отделов есть ПСО и дата окончания проекта приходится на выбранный период, то вычисляем сметные
			//трудозатраты, в размере 5% и соответственно увеличиваем общие тр-ты на 5%
			if(check_dep_in_listDep($row['Id'], 19) && checkDateCloseProject_in_CurrentInterval($row['Id'], $IntervalProject))
			{
				$man_hour_smeta	= round($man_hour_all * 0.05, 0); //тр-ты сметного отдела= 5% от общих тр-т
				$man_hour_all	= round($man_hour_all * 1.05, 0); //общие тр-ты увеличиваем на 5%
				
				print "<TR align=left style=\"background-color:#D8FFC0\">\n";	//выделяем зеленым цветом, чтобы можно было вычленить работы, которые 
																				//оканчиваются в отчетный период и где есть сметы		
			} else
			{
				print "<TR align=left>\n";
				$man_hour_smeta = 0;
			}



			print "<TD align=center>".++$n."</TD>";
			printZeroTD($row['Number_Customer']."-".$row['Number_Project']);
			printZeroTD($row['Number_UTR']);
			printZeroTD($row['Customer_Service']);
			printZeroTD($row['Plant_Destination']);
			printZeroTD($row['Name_Project']);
			$man_hour = round($man_hour_smeta + $row['Man_Hour_Sum'], 0);
			print("<TD align=center><b>".$man_hour."</b></TD>");//трудозатраты за период
			
			print("<TD align=center><b>".$man_hour_offPlan."</b></TD>");
			print("<TD align=center><b>".$man_hour_all."</b></TD>");


		
	    //заполняем массив для передачи в эксель
	    $result[$n]['Number_Project'] 		= $row['Number_Customer']."-".$row['Number_Project'];
	    $result[$n]['Number_UTR'] 			= str_replace("&nbsp;", "", $row['Number_UTR']);
	    $result[$n]['Customer_Service'] 	= str_replace("&nbsp;", "", $row['Customer_Service']);
	    $result[$n]['Plant_Destination'] 	= str_replace("&nbsp;", "", $row['Plant_Destination']);
	    $result[$n]['Name_Project'] 		= $row['Name_Project'];
	    $result[$n]['Man_Hour_Sum'] 		= $man_hour;
	    $result[$n]['ManHourSumAll'] 		= $man_hour_all;

	    $ManHourSum = $ManHourSum + $man_hour;
	    $ManHourSumOffPlan = $ManHourSumOffPlan + $man_hour_offPlan;
		$ManHourSumAll = $ManHourSumAll + $man_hour_all; //фактич.трудозатраты всего по объекту

		}
	}

	print "<TR>\n";
	print "<TH colspan='6' align=right>Итого:</TH>";
	printZeroTH($ManHourSum);
	printZeroTH($ManHourSumOffPlan);
	printZeroTH($ManHourSumAll); //фактич.трудозатраты всего по объекту
	print "</Table>\n";

	$s = serialize($result);    //происходит сериализация массива для передачи по скрытому полю ввода
	print "<form action=\"report_excel/saveExcel_UTR.php\" method=\"POST\">\n";
	print "<input type=\"image\" name=\"btnSaveExcel\" src=\"../img/file_xls.png\" width=\"48\" height=\"48\" border=\"0\" alt=\"сохранение в Экселе\" />  ";
	print "<div class=\"hint\"><span>Подсказка! </span>Сохранение в Excel</div>";
	print "<input type=\"hidden\" name=\"dataToExcel\" value=\"".htmlspecialchars($s, ENT_QUOTES)."\" />  ";
	print "</form>";
}
require_once("../end1.php");
