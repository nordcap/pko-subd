<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей отчета трудоемкости работ по методике ДКС
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
include_once("../modules/lib/lib.php");
require_once("begin1.php");
include_once("../js/jscript.js");
print "<a href=\"object.php\">Назад</a><br />\n";
print "<H2>Сводные данные по методике ДКС</H2><br />\n";
print "<br /><b>Отдел:</b>&nbsp";
if ( $_SESSION['Department'] != "Выбор отдела" )
{
	print getNameDepartment($_SESSION['Department']);
}
//запрос отдела
$QueryDepartment = selectDepartment();
//в зависимости от выбранной категории определяем критерий поиска дат-по всем работам или по утвержденным
switch ($_SESSION['rbCategry'])
{
	case "time":
	$QueryDate = getIntervalDate(
	$_SESSION['lstMonth'],
	$_SESSION['lstYear'],
	"design",
	"Date",
	$_SESSION['rbPeriod']);
	break;
	case "man_hour":
	$QueryDate = getIntervalDate(
	$_SESSION['lstMonth'],
	$_SESSION['lstYear'],
	"design",
	"statusBVP",
	$_SESSION['rbPeriod']);
	break;
	case "time_DKS":
	$QueryDate = getIntervalDate(
	$_SESSION['lstMonth'],
	$_SESSION['lstYear'],
	"design",
	"statusBVP",
	$_SESSION['rbPeriod']);  //оставляем аналогично трудозатратам
	$ManHourDepartment = getManHourSumBVP($QueryDate); //трудозатраты отдела за месяц
	$TabelTimeDepartment = getTabelTime(); //фактическое табельное время отдела за месяц
	$KoefTime = $TabelTimeDepartment/$ManHourDepartment; //получаем коэф-т приведения трудозатрат к табельному времени
	break;
}
//составляем объединение результатов работы нескольких команд SELECT в один набор результатов.
$Query1= "SELECT  ".
"design.num_cher, design.Man_Hour1, design.Man_Hour4, design.Man_Hour3, ".
"design.Time1, design.Time3, design.Time4, SUM(design.Time) AS Time,".
"employee.Family, employee.Name, employee.Patronymic, employee.Tabel_Number, ".
"project.Number_Project, ".
"department.Name_Department, office.Name_Office, ".
"mark.Name_Mark, work.Name_Work, work.Type_Work, ".
"SUM(design.Sheet_A1) AS Sheet_A1, SUM(design.Sheet_A4+2*design.Sheet_A3) AS Sheet_A4, ".
"SUM(design.Man_Hour_Sum) AS Man_Hour_Sum ".
"FROM design, employee, project, mark, department, office, work ".
"WHERE ".
"(design.id_Mark = mark.Id) AND ".
"(design.id_Project = project.Id) AND ".
"(design.id_Employee = employee.Id) AND ".
"(design.id_Work = work.Id) AND ".
"(employee.id_Department = department.Id) AND ".
"(employee.id_Office = office.id) AND department.id <>19 ";
//объединение запроса в 1 запрос
$Query = $Query1.$QueryDate.$QueryDepartment.
" GROUP BY employee.id, mark.Name_Mark, project.id, work.Type_Work ".
" ORDER BY BINARY employee.Family, mark.Name_Mark, project.Number_Project";
//file_put_contents("query.txt",$Query);
if ( !($dbResult = mysql_query($Query, $link)) )
{
	//print"Выборка не удалась!\n";
	processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
}
else {
	//таблица HTML
	print "<TABLE  Border=\"1\" CellSpacing=\"0\" CellPadding=\"2\">\n";
	print " <Caption><H4>Информация о выполнении проектных работ ПУ ОАО \"Азот\" за ".
	$Month[$_SESSION['lstMonth']]." ".$Year[$_SESSION['lstYear']]." </Caption>\n";
	//заголовок таблицы
	print "<TR bgcolor=#E8E8E8 align=center>\n";
	print "<TH CLASS= \"thsmall\" rowspan=3>№ п/п</TH>\n";
	print "<TH CLASS= \"thsmall\" rowspan=3>Наименование работ</TH>\n";
	print "<TH CLASS= \"thsmall\" rowspan=3>Номер проекта</TH>\n";
	print "<TH CLASS= \"thsmall\" rowspan=3>Марка</TH>\n";
	print "<TH CLASS= \"thsmall\" rowspan=3>Табельный №</TH>\n";
	print "<TH CLASS= \"thsmall\" rowspan=3>Исполнитель</TH>\n";
	print "<TH CLASS= \"thsmall\" colspan = 3>Кол-во листов в приведенном формате</TH>\n";
	switch ($_SESSION['rbCategry'])
	{
		case "time":
		print "<TH CLASS= \"thsmall\" colspan = 3>Кол-во отработанного по табелю рабочего времени (чел.час)</TH>\n";
		break;
		case "man_hour":
		print "<TH CLASS= \"thsmall\" colspan = 3>Кол-во трудозатрат (чел.час)</TH>\n";
		break;
		case "time_DKS":
		print "<TH CLASS= \"thsmall\" colspan = 3>Кол-во отработанного по табелю рабочего времени (чел.час)</TH>\n";
		break;
	}
	print "<TH CLASS= \"thsmall\" colspan = 2>Норматив трудозатрат по Методике на 1 лист в  приведенном формате</TH>\n";
	print "<TH CLASS= \"thsmall\" colspan = 3>Объем работ в пересчете на трудозатраты по Методике</TH>\n";
	print "<TH CLASS= \"thsmall\" colspan = 4>Отклонение трудозатрат по Методике</TH>\n";
	print "</TR>\n";
	print "<TR bgcolor=#E8E8E8 align=center>\n";
	print "<TH CLASS= \"thsmall\" rowspan=2>Итого</TH>\n";
	print "<TH CLASS= \"thsmall\" rowspan=2>Графическая часть А1</TH>\n";
	print "<TH CLASS= \"thsmall\" rowspan=2>Текстовая часть А4</TH>\n";
	print "<TH CLASS= \"thsmall\" rowspan=2>Итого</TH>\n";
	print "<TH CLASS= \"thsmall\" rowspan=2>Графическая часть А1</TH>\n";
	print "<TH CLASS= \"thsmall\" rowspan=2>Текстовая часть А4</TH>\n";
	print "<TH CLASS= \"thsmall\" rowspan=2>Графическая часть А1</TH>\n";
	print "<TH CLASS= \"thsmall\" rowspan=2>Текстовая часть А4</TH>\n";
	print "<TH CLASS= \"thsmall\" rowspan=2>Итого</TH>\n";
	print "<TH CLASS= \"thsmall\" rowspan=2>Графическая часть А1</TH>\n";
	print "<TH CLASS= \"thsmall\" rowspan=2>Текстовая часть А4</TH>\n";
	print "<TH CLASS= \"thsmall\" colspan=2>Абсолютное (факт-метод)</TH>\n";
	print "<TH CLASS= \"thsmall\" colspan=2>Процент (факт-метод)/метод</TH>\n";
	print "</TR>\n";
	print "<TR bgcolor=#E8E8E8 align=center>\n";
	print "<TH CLASS= \"thsmall\">Графическая часть(гр.11-гр.16)</TH>\n";
	print "<TH CLASS= \"thsmall\">Текстовая часть(гр.12-гр.17)</TH>\n";
	print "<TH CLASS= \"thsmall\">Графическая часть(гр.18/гр.16)</TH>\n";
	print "<TH CLASS= \"thsmall\">Текстовая часть(гр.19/гр.17)</TH>\n";
	print "</TR>\n";
	print "<TR bgcolor=#E8E8E8 align=center>\n";
	print "<TH CLASS= \"thsmall\">1</TH>\n";
	print "<TH CLASS= \"thsmall\">2</TH>\n";
	for ($i=3; $i<22; $i++)
	{
		print "<TH CLASS= \"thsmall\">$i</TH>\n";
	}
	print "</TR>\n";
	//конец заголовка таблицы
	//Кол-во листов в приведенном формате
	$Sheet_A1 = 0;
	$Sheet_A4 = 0;
	$TotalSheet = 0;
	//Кол-во отработанного рабочего времени
	$ManHourSum_A4 = 0;
	$ManHourSum_A1 = 0;
	$TotalManHourSum = 0;
	//Кол-во отработанного рабочего времени(по табелю)
		$Time_A4 = 0;
	$Time_A1 = 0;
	$TotalTime = 0;
	//Объем работ в пересчете на трудозатраты по методике ДКС
	$ManHourSum_A4_DKS = 0;
	$ManHourSum_A1_DKS = 0;
	$TotalManHourSum_DKS = 0;
	//Абсолютные отклонения
	$A1 = 0;
	$A4 = 0;
	//порядковый номер
	$n = 0;
	while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
	{
		if (fmod($n,2) != 0)
		{
			print "<TR align=center BGCOLOR=#D8FFC0>\n"; //зеленый
		}
		else {
			print "<TR align=center>\n";
		}
		/*1*/	  	print "<TD>".++$n."</TD>";
		/*2*/    printZeroTD(strtok($row['Type_Work'],"("));
		/*4*/    printZeroTD($row['Number_Project']);
		/*5*/    printZeroTD($row['Name_Mark']."-".$row['Name_Work']."-".strtok($row['Type_Work'],"("));
		printZeroTD($row['Tabel_Number']);
		/*6*/    print("<TD nowrap align=left>{$row['Family']} ".
		mb_substr($row['Name'],0,1,'utf8').".".
		mb_substr($row['Patronymic'],0,1,'utf8').".</TD>");
		$temp8 = $_SESSION['K_Graph']*$row['Sheet_A1'];
		$temp9 = $_SESSION['K_Text']*$row['Sheet_A4'];
		$temp7 = $temp8 + $temp9;
		/*7*/    printZeroTD(round($temp7,1));
		/*8*/    printZeroTD(round($temp8,1));
		/*9*/    printZeroTD(round($temp9,1));
		//сумма листов в приведенном формате
		$Sheet_A1 = $Sheet_A1 + $temp8;
		$Sheet_A4 = $Sheet_A4 + $temp9;
		$TotalSheet = $TotalSheet + $temp7;
		switch ($_SESSION['rbCategry'])
		{
			//------------------------------------------------------
			case "time":
			$temp10 = $row['Time'];  //общее количество времени потраченное на работу
			/*10*/  printZeroTD(round($temp10,1));
			//в графу 11 записываем значение, если трудозатраты текстовой части равны нулю
			/*11*/  if ($row['Time3'] == 0 AND $row['Time4'] == 0)
			{
				$temp11 = $temp10;
				printZeroTD(round($temp11,1));
				$Time_A1 = $Time_A1 + $temp11;
			}
			else {
				print "<TD>&nbsp</TD>\n";
			}
			//в графу 12 записываем значение, если трудозатраты графической части равны нулю
			/*12*/  if (($row['Time4'] != 0 OR
			$row['Time3'] != 0) AND
			$row['Time1'] == 0)
			{
				$temp12 = $temp10;
				printZeroTD(round($temp12,1));
				$Time_A4 = $Time_A4 + $temp12;
			}
			else {
				print "<TD>&nbsp</TD>\n";
			}
			$TotalTime = $TotalTime + $temp10;
			break;
			//------------------------------------------------------
			case "man_hour":
			$temp10 = $row['Man_Hour_Sum'];  //общее количество трудозатрат потраченное на работу
			/*10*/  printZeroTD(round($temp10,1));
			//в графу 11 записываем значение, если трудозатраты текстовой части равны нулю
			/*11*/  if ($row['Man_Hour3'] == 0 AND $row['Man_Hour4'] == 0)
			{
				$temp11 = $temp10;
				printZeroTD(round($temp11,1));
				$ManHourSum_A1 = $ManHourSum_A1 + $temp11;
			}
			else {
				print "<TD>&nbsp</TD>\n";
			}
			//в графу 12 записываем значение, если трудозатраты графической части равны нулю
			/*12*/if (($row['Man_Hour4'] != 0 OR $row['Man_Hour3'] != 0) AND
			$row['Man_Hour1'] == 0)
			{
				$temp12 = $temp10;
				printZeroTD(round($temp12,1));
				$ManHourSum_A4 = $ManHourSum_A4 + $temp12;
			}
			else {
				print "<TD>&nbsp</TD>\n";
			}
			$TotalManHourSum = $TotalManHourSum + $temp10;
			break;
			//------------------------------------------------------
			case "time_DKS":
			$temp10 = $row['Man_Hour_Sum'] * $KoefTime;  //общее количество трудозатрат потраченное на работу
			/*10*/  printZeroTD(round($temp10,1));
			//в графу 11 записываем значение, если трудозатраты текстовой части равны нулю
			/*11*/  if ($row['Man_Hour3'] == 0 AND $row['Man_Hour4'] == 0)
			{
				$temp11 = $temp10;
				printZeroTD(round($temp11,1));
				$ManHourSum_A1 = $ManHourSum_A1 + $temp11;
			}
			else {
				print "<TD>&nbsp</TD>\n";
			}
			//в графу 12 записываем значение, если трудозатраты графической части равны нулю
			/*12*/  if (($row['Man_Hour4'] != 0 OR $row['Man_Hour3'] != 0) AND
			$row['Man_Hour1'] == 0)
			{
				$temp12 = $temp10;
				printZeroTD(round($temp12,1));
				$ManHourSum_A4 = $ManHourSum_A4 + $temp12;
			}
			else {
				print "<TD>&nbsp</TD>\n";
			}
			$TotalManHourSum = $TotalManHourSum + $temp10;
			break;
		}
		if ($row['Man_Hour1'] != 0)
		{
			/*13*/   	printZeroTD($row['Man_Hour1']);
		}
		else {
			print "<TD>&nbsp;</TD>\n";
		}
		if ($row['Man_Hour3'] == 0 AND
		$row['Man_Hour4'] != 0)
		{
			$temp14 = $row['Man_Hour4'];
		}
		elseif ($row['Man_Hour4'] == 0 AND
		$row['Man_Hour3'] != 0)
		{
			$temp14 = $row['Man_Hour3']/2;
		}
		elseif ($row['Man_Hour3'] != 0 AND $row['Man_Hour4'] !=0)
		{
			$temp14 = $row['Man_Hour4'] + $row['Man_Hour3']/2;
		}
		else {
			$temp14 = 0;
		}
		/*14*/   printZeroTD(round($temp14,1));
		$temp16 = $_SESSION['K_Graph']*$row['Sheet_A1'] * $row['Man_Hour1'];
		$temp17 = $_SESSION['K_Text']*$row['Sheet_A4'] * $temp14;
		if ($row['Man_Hour1'] != 0)
		{
			/*15*/   	    printZeroTD(round($temp16,1));
		}
		elseif ($row['Man_Hour3'] != 0 OR $row['Man_Hour4'] != 0 )
		{
			/*15*/	        printZeroTD(round($temp17,1));
		}
		else {
			print "<TD>&nbsp;</TD>\n";
		}
		/*16*/   printZeroTD(round($temp16,1));
		/*17*/   printZeroTD(round($temp17,1));
		//Сумма работ в пересчете на трудозатраты по методике ДКС
		$ManHourSum_A1_DKS = $ManHourSum_A1_DKS + $temp16;
		$ManHourSum_A4_DKS = $ManHourSum_A4_DKS + $temp17;
		$TotalManHourSum_DKS = $TotalManHourSum_DKS + $temp16 + $temp17;
		$temp18 = $temp11 - $temp16;
		$temp19 = $temp12 - $temp17;
		if ($temp18 < 0)
		{
			printZeroTD(round($temp18,1), "red");
		}
		else {
			/*18*/   	printZeroTD(round($temp18,1));
		}
		if ($temp19 < 0)
		{
			printZeroTD(round($temp19,1), "red");
		}
		else {
			/*19*/   	printZeroTD(round($temp19,1));
		}
		//Абсолютные отклонения
		$A1 = $A1 + $temp18;
		$A4 = $A4 + $temp19;
		if ($temp16 != 0)
		{
			$temp20 = 100*$temp18/$temp16;
			if ($temp20 < 0)
			{
				/*20*/			    printZeroTD(round($temp20,1),"red","%");
			}
			else {
				/*20*/	   	        printZeroTD(round($temp20,1),"black","%");
			}
		}
		else {
			print "<TD>&nbsp;</TD>\n";
		}
		if ($temp17 != 0)
		{
			$temp21 = 100*$temp19/$temp17;
			if ($temp21 < 0)
			{
				/*21*/			printZeroTD(round($temp21,1),"red","%");
			}
			else {
				/*21*/	   	printZeroTD(round($temp21,1),"black","%");
			}
		}
		else {
			print "<TD>&nbsp;</TD>\n";
		}
		//обнуляем временные переменные
		$temp7 = 0;
		$temp8 = 0;
		$temp9 = 0;
		$temp10 = 0;
		$temp11 = 0;
		$temp12 = 0;
		$temp14 = 0;
		$temp16 = 0;
		$temp17 = 0;
		$temp18 = 0;
		$temp19 = 0;
	}
	//итоговая строка
	print "<TR>\n";
	print "<TH colspan=6 align=right>Итого:</TH>";
	printZeroTH(round($TotalSheet,1));      //7
	printZeroTH(round($Sheet_A1,1));        //8
	printZeroTH(round($Sheet_A4,1));        //9
	switch ($_SESSION['rbCategry'])
	{
		case "time":
		printZeroTH(round($TotalTime,1)); //10
		printZeroTH(round($Time_A1,1));   //11
		printZeroTH(round($Time_A4,1));   //12
		break;
		case "man_hour":
		printZeroTH(round($TotalManHourSum,1)); //10
		printZeroTH(round($ManHourSum_A1,1));   //11
		printZeroTH(round($ManHourSum_A4,1));   //12
		break;
		case "time_DKS":
		printZeroTH(round($TotalManHourSum,1)); //10
		printZeroTH(round($ManHourSum_A1,1));   //11
		printZeroTH(round($ManHourSum_A4,1));   //12
		break;
	}
	print "<TH>&nbsp;</TH>\n";     //13
	print "<TH>&nbsp;</TH>\n";     //14
	printZeroTH(round($TotalManHourSum_DKS,1)); //15
	printZeroTH(round($ManHourSum_A1_DKS,1));   //16
	printZeroTH(round($ManHourSum_A4_DKS,1));   //17
	if ($A1 < 0)
	{
		printZeroTH(round($A1,1),"red");      //18
	}
	else {
		printZeroTH(round($A1,1));      //18
	}
	if ($A4 < 0)
	{
		printZeroTH(round($A4,1),"red");      //19
	}
	else {
		printZeroTH(round($A4,1));      //19
	}
	if ($ManHourSum_A1_DKS != 0)
	{
		$temp20 = round(100*$A1/$ManHourSum_A1_DKS,1);
		if ($temp20 < 0)
		{
			printZeroTH($temp20,"red","%");    //20
		}
		else {
			printZeroTH($temp20, "black", "%");  //20
		}
	}
	else {
		print("<TH>&nbsp</TH>\n");
	}
	if ($ManHourSum_A4_DKS != 0)
	{
		$temp21 = round(100*$A4/$ManHourSum_A4_DKS,1);
		if ($temp21 < 0)
		{
			printZeroTH($temp21,"red","%");                //21
		}
		else {
			printZeroTH($temp21, "black", "%");            //21
		}
	}
	else {
		print("<TH>&nbsp</TH>\n");
	}
	print "</Table>\n";
}
require_once("../end1.php");
?>