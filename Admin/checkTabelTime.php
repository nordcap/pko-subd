<?php
//начать буферизацию вывода
ob_start();
include_once("../modules/lib/lib.php");
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Автоматизированная система учета трудозатрат</title>
    <meta charset="utf-8">
	<link href="../favicon.ico" rel="icon" type="image/x-icon" />
    <link href="../style.css" rel="stylesheet">
	<script src="../js/jquery.js"></script>
  </head>
  <body>
<?php


if (!empty($_POST))
{
	
	
if ( $_REQUEST['rbCategry'] == 'upd' )
{
	//перенаправляем на страницу редактирования табельного времени	
	header("Location:AdminTabelTime.php?rbCategry=".$_REQUEST['rbCategry']."&year=".$_REQUEST['lstYear']);
	exit;
}

$errors = array(); //объявляется массив ошибок

//проверка на наличие в базе данные по указанному году
if (checkTabelTime($_REQUEST['lstYear'])) {
	$errors[] = '<h3>В базе уже существуют указанные данные!</h3>';	
}
			

if( count($errors) > 0 )
{
	display_errors();
	
?>

<div class="f-message g-3" style="margin:auto;">
	<h5>Выбор действия</h5>
	<p>Перезаписать (удалить) данные: Да,нет?</p>
	<p class="f-message-actions">
		<a class="f-bu f-bu-warning" href="AdminTabelTime.php?rbCategry=<?php print $_REQUEST['rbCategry']?>&year=<?php print $_REQUEST['lstYear']?>">Да</a>
		<a class="f-bu f-bu-success" href="admin.php">Отмена</a>
	</p>
</div><!--f-message -->
<?php		
	} else
	{
		//если нет ошибок перенаправляем на страницу редактирования табельного времени	
		header("Location:AdminTabelTime.php?rbCategry=".$_REQUEST['rbCategry']."&year=".$_REQUEST['lstYear']);
		exit;
	}
}
?>
</body>
</html>

<?php
//сбросить содержимое буфера
ob_end_flush();
