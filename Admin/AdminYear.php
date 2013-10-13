<?php
session_start();
include_once("../modules/lib/lib.php");
checkSession(); //проверка на присутствие в сессии идентификатора пользователя
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Автоматизированная система учета трудозатрат</title>
    <meta charset="utf-8">
	<link href="../favicon.ico" rel="icon" type="image/x-icon" />
    <link href="../style.css" rel="stylesheet" media="screen">
  </head>
  <body>
	<?php 
		if (($_SESSION['Name_Post'] == "Администратор") or
			($_SESSION['Name_Post'] == "Экономист"))
		{
		?>
		<h2>Модуль редактирования лет</h2>
		<a href="Admin.php">Страница администрирования</a><br />
		<hr>
		
		<?php
		//если нажата кнопка Запрос,то
		if ( isset($_REQUEST['rbselect']) )
		{
			$errors = array(); //объявляется массив ошибок
			switch ( $_REQUEST['rbselect'] )
			{
				case "insert":

				break;
				
				case "update":

				break;
				
				case "copy":
				//если выбрана опция копирование данных
				$Comma_separated = implode(",", $_REQUEST['id_year']);
				$Query = "SELECT * from year ".
				"WHERE ".
				"id IN ($Comma_separated) ".
				"LIMIT 1";   //выборка из таблицы
				if ( !($dbResult = mysql_query($Query, $link)) )
				{
					print "Выборка не удалась!\n";
				}
				else  {
					while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
					{
						$_REQUEST['Year'] = $row['Year'];
					}

				}
				break;
			}
			if( count($errors) > 0 )
			{
				display_errors();
				//если есть ошибки при вводе или обновлении, то заносим их во временные переменные переменные
			}
		}

		?>
		<!--form-->
		<form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
			<div class="f-row">
				<label>год</label>
				<div class="f-input">
					<input type="text" name="Year" value="<?php echo $_REQUEST['Year']?>"/>
				</div>
			</div>
			<div class="f-action">
		        <button type="submit" name="rbselect" value="select" class="f-bu">Общая</button>
		        <button type="submit" name="rbselect" value="insert" class="f-bu f-bu-success">Вставка</button>
		        <button type="submit" name="rbselect" value="update" class="f-bu f-bu-default">Изменить</button>
		        <button type="submit" name="rbselect" value="delete" class="f-bu f-bu-warning">Удалить</button>
		        <button type="submit" name="rbselect" value="copy" class="f-bu f-bu-default">Копировать</button>
			</div>
		<?php 
			//вызов отображения таблицы
			if ( count($errors) == 0 )
			{
				ViewTableYear();
			}
		?>
		</form>
		<?php
		}
		else {
			?>
			<a href="admin.php">Назад</a><br />
			<div class="validation">
			<h3>Отсутствуют права на просмотр информации!</h3>
			</div>
			<div class="info">
			<h4>Перейдите по ссылке на страницу администрирования</h4>
			</div>
			<?php
			exit;
		}

	?>

  </body>
</html>
<?php 
//если есть данные и нет ошибок и сообщений, перегружаем страницу
if (!empty($_POST) AND
$_REQUEST['rbselect']!="copy" AND count($errors) == 0 )
{
	header("Location: $_SERVER[PHP_SELF]");
	exit();
}