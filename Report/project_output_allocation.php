<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей отчета "свод выработки по подразделениям"
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
error_reporting(E_ERROR & ~E_NOTICE );
session_start();
include_once("../modules/lib/lib.php");
require_once("begin1.php");
include_once("../js/jscript.js");
print "<a href=\"object.php\">Назад</a><br />\n";
print "<H3>Расчет коэффициента распределения пропорционально доле проектных работ по заказчикам</H3><br />\n";
$errors = array(); //объявляется массив ошибок
//выбираем значения месяца и года
if ( $_SESSION['lstMonth'] == "Выберите месяц" OR
$_SESSION['lstYear'] == "Выберите год" )
{
	$errors[] = "<H3>Необходимо указать месяц и год</H3>\n";
}
if ( count($errors) > 0 )
{
	display_errors();
	return;
}
print "<Table Border=\"1\" CellSpacing=\"0\" CellPadding=\"2\">\n";
print "<Tr>\n";
print "<Td>";
print "Объект: ";
if( $_SESSION['lstObject'] == "Выбор объекта" )
{
	print "Все объекты";
}
else {
	print getNameProject($_SESSION['lstObject']);
}
print "<Td>";
print "Заказчик: ";
if ( $_SESSION['lstCustomer'] == "Выбор заказчика" )
{
	print "Все заказчики";
}
else {
	print getNameCustomer($_SESSION['lstCustomer']);
}
print "<Td>";
print "Текущая дата ".date("d.m.Y");
print "<Td>";
print "Год ".$_SESSION['lstYear'];
if ( $_SESSION['lstObject'] == "Выбор объекта" )
{
	print " ";
}
else {
	print "<Tr>\n";
	print "<Td colspan=4>";
	print "<U>".getNameExtProject($_SESSION['lstObject'])."</U>";
}
print "</Table>\n";
print "<p></p>";
// -----------------------------------------------------------------------------------------------------------------
//подготовительные запросы
if ( $_SESSION['lstCustomer'] != "Выбор заказчика" )
{
	$QueryCustomer = "(project.Id_Customer=".escapeshellarg($_SESSION['lstCustomer']).") AND ";
}
else {
	$QueryCustomer = "";
}
//таблица HTML
print "<TABLE Width=\"50%\" Border=\"2\" CellSpacing=\"0\" CellPadding=\"2\">\n";
print "<TR>\n";
print "<TH rowspan=2>Месяц</TH>".
"<TH colspan=2>Всего по ПУ</TH>".
"<TH colspan=5>".getNameCustomer($_SESSION['lstCustomer'])."</TH>".
"<TR>\n".
"<TH>План</TH>".
"<TH>Выдача БВП</TH>".
"<TH>факт.время</TH>".
"<TH>трудозатраты</TH>".
"<TH>выдача БВП</TH>".
"<TH>Коэф-т</TH>".
"<TR>\n".
"<TH>1</TH>".
"<TH>2</TH>".
"<TH>3</TH>".
"<TH>4</TH>".
"<TH>5</TH>".
"<TH>6</TH>".
"<TH>7</TH>";
foreach($_SESSION['Month'] as $index=>$NameMonth)
{
	print "<TR align=center>\n";
	printZeroTD($NameMonth); //1
	printZeroTD( round(getPlanMonth($index),0) );//2
	$PlanBVP = round(getDataMonthBVP($index,""),0); //3
	printZeroTD( $PlanBVP );//3
	$row = getDataMonth($index,$QueryCustomer);
	printZeroTD( round($row[0],0) );//4
	printZeroTD( round($row[1],0) );//5
	$answerBVP = round(getDataMonthBVP($index,$QueryCustomer),0); //6
	printZeroTD( $answerBVP );//6
	$k = @round($answerBVP/$PlanBVP,3);//7
	printZeroTD( $k );//7
}
print "</Table>\n";
require_once("../end1.php");
?>