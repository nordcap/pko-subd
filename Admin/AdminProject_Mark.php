<?php
session_start();     //инициализирум механизм сесссий
//начать буферизацию вывода
ob_start();
include_once("../modules/lib/lib.php");
checkSession(); //проверка на присутствие в сессии идентификатора пользователя
require_once("begin1.php");
include_once("../js/jscript.js");
?>
<style type="text/css">
input.btncopy {
	background: url(../img/copy.png);
	/*display:block;  */
	width: 83px;
	height: 24px;
	background-color: transparent;
	color: transparent;
	border: none;
	margin-left: 30px;
}
input.btncopy:hover {
	background-position: left bottom;
}
input.btndel {
	background: url(../img/delete.png);
	/*display:block;  */
	width: 88px;
	height: 24px;
	background-color: transparent;
	color: transparent;
	border: none;
	margin-left: 30px;
}
input.btndel:hover {
	background-position: left bottom;
}
input.btnupdate {
	background: url(../img/update.png);
	/*display:block;  */
	width: 74px;
	height: 24px;
	background-color: transparent;
	color: transparent;
	border: none;
	margin-left: 30px;
}
input.btnupdate:hover {
	background-position: left bottom;
}
input.btnadd {
	background: url(../img/add.png);
	/*display:block;  */
	width: 68px;
	height: 24px;
	background-color: transparent;
	color: transparent;
	border: none;
	margin-left: 30px;
}
input.btnadd:hover {
	background-position: left bottom;
}
</style>
<?php
if (isset($_GET['idProject']))
{
	$_SESSION['idProject'] = $_GET['idProject'];
}
print "<H2>Управление марками, выданными в БВП </H2><br />\n";
print "<a href=\"AdminProject.php\">Страница редактирования проектов</a><br />\n";
print "<Hr Width=\"100%\" ALIGN=\"left\"><br />\n";
print "<h4 style='display:inline'>Шифр проекта:</h4>&nbsp;<i style='color:#990099'>".getNameProject($_SESSION['idProject'])."</i>";
print "<p></p>  ";
print "<h4 style='display:inline'>Наименование проекта:</h4>&nbsp;<i style='color:#990099'>".getNameExtProject($_SESSION['idProject'])."</i>";
print "<p></p>  ";
print "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\">\n";
$errors = array(); //объявляется массив ошибок
// отображем таблицу заданий, а также формируем массив значений
$ArrayMark = ViewTableProjectMark();
//валидация
$ArrayMark = checkEmptyMarkBVP($ArrayMark);
//вывод на экран ошибок
if ( count($errors) > 0 )
{
	display_errors();
	return;
}
if (isset($_REQUEST['btnadd']))
{
	$Query = " INSERT INTO markbvp SET ".
	" id_Project = ".escapeshellarg($_SESSION['idProject']).
	", id_Mark = ".escapeshellarg(68);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		processingError("$Query ", __FILE__, __LINE__, __FUNCTION__);
	}
	//после добавления данных перегружаем страницу и данные берутся уже из табл. Plan_Employee
	header("Location:AdminProject_Mark.php");
}
//нажата кнопка копирования
if (isset($_REQUEST['btncopy']))
{
	//вычислим схождение массивов
	$arr_id = array_intersect($ArrayMark['id'], $_REQUEST['active_str']);
	foreach($arr_id as $key=>$id)
	{
		$Query = " INSERT INTO markbvp SET ".
		" id_Project = ".escapeshellarg($_SESSION['idProject']).
		", id_Mark = ".escapeshellarg($ArrayMark['mark'][$key]).
		", NumberMark = ".escapeshellarg($ArrayMark['num_mark'][$key]).
		", NumberChange = ".escapeshellarg($ArrayMark['num_change'][$key]).
		", DateExtraditionBVP = ".escapeshellarg($ArrayMark['date_extradition_BVP'][$key]).
		", DateCustomer = ".escapeshellarg($ArrayMark['date_customer'][$key]).
		", Comment = ".escapeshellarg($ArrayMark['comment'][$key]);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			processingError("$Query ", __FILE__, __LINE__, __FUNCTION__);
		}
	}
	//после добавления данных перегружаем страницу и данные берутся уже из табл. Plan_Employee
	header("Location:AdminProject_Mark.php");
}
if (isset($_REQUEST['btndel']))
{
	//вычислим схождение массивов
	$Comma_separated = implode(",", $_REQUEST['active_str']);
	$Query = " DELETE FROM markbvp WHERE id IN ($Comma_separated)";
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
	}
	//после добавления данных перегружаем страницу и данные берутся уже из табл. Plan_Employee
	header("Location:AdminProject_Mark.php");
}
switch ($_REQUEST['ok'])
{
	case "updateZero":
	$Query = " INSERT INTO markbvp SET ".
	" id_Project = ".escapeshellarg($_SESSION['idProject']).
	", id_Mark = ".escapeshellarg($ArrayMark['mark'][0]).
	", NumberMark = ".escapeshellarg($ArrayMark['num_mark'][0]).
	", NumberChange = ".escapeshellarg($ArrayMark['num_change'][0]).
	", DateExtraditionBVP = ".escapeshellarg($ArrayMark['date_extradition_BVP'][0]).
	", DateCustomer = ".escapeshellarg($ArrayMark['date_customer'][0]).
	", Comment = ".escapeshellarg($ArrayMark['comment'][0]);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		processingError("$Query ", __FILE__, __LINE__, __FUNCTION__);
	}
	//после добавления данных перегружаем страницу и данные берутся уже из табл. Plan_Employee
	header("Location:AdminProject_Mark.php");
	break;
	case "update":
	foreach($ArrayMark['id'] as $key=>$value_id)
	{
		$Query = " UPDATE markbvp SET ".
		" id_Mark = ".escapeshellarg($ArrayMark['mark'][$key]).
		", NumberMark = ".escapeshellarg($ArrayMark['num_mark'][$key]).
		", NumberChange = ".escapeshellarg($ArrayMark['num_change'][$key]).
		", DateExtraditionBVP = ".escapeshellarg($ArrayMark['date_extradition_BVP'][$key]).
		", DateCustomer = ".escapeshellarg($ArrayMark['date_customer'][$key]).
		", Comment = ".escapeshellarg($ArrayMark['comment'][$key]).
		" WHERE id = ".escapeshellarg($value_id);
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			//processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
		}
	}
	//после обновления данных перегружаем страницу и данные берутся уже из табл. Plan_Employee
	header("Location:AdminProject_Mark.php");
	break;
}
require_once("../end1.php");
//сбросить содержимое буфера
ob_end_flush();
?>