<?php
session_start();     //инициализирум механизм сесссий
//начать буферизацию вывода
ob_start();
include_once("../modules/lib/lib.php");
checkSession(); //проверка на присутствие в сессии идентификатора пользователя
require_once("begin1.php");
?>
<style type="text/css">
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
</style>
<?php
if (isset($_GET['idProject']))
{
	$_SESSION['idProject'] = $_GET['idProject'];
}
print "<H2>Управление комментариями </H2><br />\n";
print "<a href=\"AdminProject.php\">Страница редактирования проектов</a><br />\n";
print "<Hr Width=\"100%\" ALIGN=\"left\"><br />\n";
print "<h4 style='display:inline'>Шифр проекта:</h4>&nbsp;<i style='color:#990099'>".getNameProject($_SESSION['idProject'])."</i>";
print "<p></p>  ";
print "<h4 style='display:inline'>Наименование проекта:</h4>&nbsp;<i style='color:#990099'>".getNameExtProject($_SESSION['idProject'])."</i>";
print "<p></p>  ";
print "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\">\n";
$errors = array(); //объявляется массив ошибок
$Query = "SELECT Comment FROM project WHERE id=".escapeshellarg($_SESSION['idProject'])." LIMIT 1";
if ( !($dbResult = mysql_query($Query, $link)) )
{
	//print "Выборка $QueryMan не удалась!\n".mysql_error();
	processingError("$Query ".mysql_error(), __FILE__, __LINE__, __FUNCTION__);
}
else {
	$res = mysql_num_rows($dbResult);
	if ($res == 0)
	{
		print "<textarea name=\"reader\" rows=\"10\" style='width:500px'></textarea> ";
	}
	else {
		$row = mysql_fetch_array($dbResult, MYSQL_BOTH);
		print "<textarea name=\"reader\" rows=\"10\" style='width:500px'>".$row['Comment']."</textarea> ";
	}
	print "<p></p>";
	print "<input type=\"submit\" name=\"ok\" value=\"update\" class='btnupdate'/>";
}
//вывод на экран ошибок
if ( count($errors) > 0 )
{
	display_errors();
	return;
}
if (empty($_REQUEST['reader']))
{
	$_REQUEST['reader'] = '&nbsp;';
}
if (isset($_REQUEST['ok']))
{
	$Query = " UPDATE project SET ".
	" Comment=".escapeshellarg($_REQUEST['reader']).
	" WHERE id = ".escapeshellarg($_SESSION['idProject']);
	if ( !($dbResult = mysql_query($Query, $link)) )
	{
		processingError("$Query ", __FILE__, __LINE__, __FUNCTION__);
	}
	//после добавления данных перегружаем страницу и данные берутся уже из табл. Plan_Employee
	header("Location:AdminProject_Comment.php");
}
require_once("../end1.php");
//сбросить содержимое буфера
ob_end_flush();
?>