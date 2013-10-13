<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является инициализатором времени и производств.табеля
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
/*$Month = array(
1=>"январь","февраль","март","апрель",
"май","июнь","июль","август","сентябрь",
"октябрь","ноябрь","декабрь");
//массив представляющий собой список лет-диапазон возможных дат проектов
$Year = array(
2000=>"2000","2001","2002","2003",
"2004","2005","2006","2007","2008",
"2009","2010","2011","2012","2013",
"2014","2015");*/
//инициализация массива для разбивки проектов по номерам
$_SESSION['arrayProject'] = array(  '^[[:alpha:]]'=>'xxxx',
 									 '^1'=>'1xx-xx',
									 '^2'=>'2xx-xx',
									 '^3'=>'3xx-xx',
									 '^4'=>'4xx-xx',
									 '^5'=>'5xx-xx',
									 '^6'=>'6xx-xx',
									 '^7'=>'7xx-xx',
									 '^8'=>'8xx-xx',
									 '^9'=>'9xx-xx');
//текущий элемент массива будет страницей по умолчанию
$_SESSION['pageProject'] = current(array_keys($_SESSION['arrayProject']));
//инициализируем список отделов
$_SESSION['arrayDepartment'] = getArrayDepartment();
$QueryDay = "SELECT Day FROM Day";
$QueryMonth = "SELECT Month FROM Month";
$QueryYear = "SELECT Year FROM Year";
if ( !($dbResult = mysql_query($QueryDay, $link)) )
{
	print "запрос $QueryDay не выполнен\n";
}
else {
	$i = 1;
	while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
	{
		$Day[$i++] = $row['Day'];
	}
	$_SESSION['Day']=$Day;
}
if ( !($dbResult = mysql_query($QueryMonth, $link)) )
{
	print "запрос $QueryMonth не выполнен\n";
}
else {
	$i = 1;
	while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
	{
		$Month[$i++] = $row['Month'];
	}
	$_SESSION['Month']=$Month;
}
if ( !($dbResult = mysql_query($QueryYear, $link)) )
{
	print "запрос $QueryYear не выполнен\n";
}
else {
	$i = 2008;
	while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
	{
		$Year[$i++] = $row['Year'];
	}
	$_SESSION['Year']=$Year;
}
unset($QueryDay);
unset($QueryMonth);
unset($QueryYear);
?>
