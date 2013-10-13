<?php
session_start();
//начать буферизацию вывода
ob_start();
include_once("../modules/lib/lib.php");
checkSession(); //проверка на присутствие в сессии идентификатора пользователя
require_once("begin1.php");
if ($_SESSION['Name_Post'] == "Администратор")
{

$errors = array(); //объявляется массив ошибок
//если нажата кнопка проверки табеля то вызываетс модуль CheckTabel.php
if (isset($_REQUEST['btncheckTabel']))
{
	header("Location: ../CheckTabel.php");
	exit();
}
if (!empty($_POST))
{
	//проверяем какие кнопки были нажаты
	if (isset($_REQUEST['btnReport']))
	{
		$_SESSION['lstMonth'] = $_REQUEST['lstMonth'];
		$_SESSION['lstYear'] = $_REQUEST['lstYear'];
		$_SESSION['lstPerformerReport'] = $_REQUEST['lstPerformerReport'];
		header("Location: ReportMonth.php");
		exit();
	}
	//проверка ошибок
	switch ( $_REQUEST['rbselect'] )
	{
		case "insert":
		checkEmptyDate();//проверка правильности даты
		checkEmptySapr();//проверка на заполнение полей
		break;
		case "update":
		checkEmptyDate();//проверка правильности даты
		checkEmptySapr(); //проверка на заполнение полей
		break;
		case "copy":
		//если выбрана опция копирование данных
		$Comma_separated = implode(",", $_REQUEST['design']);
		$Query = "SELECT DISTINCTROW sapr.Id, sapr.Date, sapr.Name_Work, ".
		"sapr.check_Work, sapr.Time, sapr.id_Employee, sapr.id_EmployeeCustomer, sapr.Comment ".
		"FROM sapr ".
		"WHERE ".
		"sapr.Id IN ($Comma_separated) LIMIT 1";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			print "Выборка $Query не удалась!\n";
		}
		else   {
			while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
			{
				$_REQUEST['Date'] = date("d.m.y", $row['Date']);
				$_REQUEST['Name_Work'] = $row['Name_Work'];
				$_REQUEST['Time'] = $row['Time'];
				$_REQUEST['Comment'] = $row['Comment'];
				//	      	$_REQUEST['lstEmployee'] = $row['id_EmployeeCustomer'];  //заказчик
				//	      	$_REQUEST['lstPerformer'] = $row['id_Employee']; //исполнитель
				$tmplstEmployee = $row['id_EmployeeCustomer']; //заказчик
				$tmplstPerformer = $row['id_Employee']; //исполнитель
			}
		}
		break;
	}
}
if ( count($errors) > 0 )
{
	$tmplstEmployee = getIdEmployee($_REQUEST['lstEmployee']); //заказчик
	$tmplstPerformer = getIdEmployee($_REQUEST['lstPerformer']); //исполнитель
	display_errors();
}
?>
	<div class="f-nav-bar">
			<div class="f-nav-bar-title">
			<img src="../img/img_index/Control_Panel.png" alt="АРМ трудозатраты" width="48"  align="absmiddle" style="	display: inline-block;"/>АРМ трудозатраты
			</div>
		<div class="f-nav-bar-body">
			<ul class="f-nav">
                <li><a href="../index1.php"><img src="../img/img_search/arrow_left_green_48.png" alt="На главную страницу" class="f-img"/>Главная...</a></li>
				<li><a href="#" id="report_sapr"><img src="../img/img_index/Performance.png" alt="Отчеты" class="f-img"/>Отчеты</a></li>
			</ul> 		
		</div>
		<div class="f-nav-right" style="position: absolute; top:5px;right:10px;">
        <?php
            print $_SESSION['Family_short'];
        ?>
		</div>
		<div class="f-nav-right" style="position: absolute; top:25px;right:10px;">
			Сегодня  <?php print date("d.m.Y"); ?>
		</div>
		<div class="f-nav-right" style="position: absolute; top:45px;right:10px;">
			<?php on_line(); ?>
		</div>		


	</div>



<!----------------------------------------------------------------------------------------------------------------------------->


<form action="<?php print $_SERVER['PHP_SELF']; ?>" method="post">

<!--отчеты-->
<div id="report_blocks" class="blocks_menu g-2" style="margin:10px auto;">
	<div class="g-row">
	<select name="lstPerformerReport" class="g-2">
		<option>бюро САПР</option>
        <?php
        //заполнение списка сотрудников-исполнителей
        getArrayNameEmployeeSAPR();
        ?>
	</select>
	</div>

	<div class="g-row">
        <?php
        	//печатаем список месяцев
        	printLstMonth();
        ?>
			</div>
	<div class="g-row">
        <?php
        	//печатаем список лет
        	printLstYear();
        ?>

	</div>
	<div class="g-row">
	<button type="submit" name="btnReport" value="Отчет" class="f-bu f-bu-success" style="margin-left:20px; width:100px;">Отчет</button>
	</div>
	<div class="g-row">
	<button type="submit" name="btncheckTabel"  value="Табель" class="f-bu f-bu-default" style="margin-left:20px; width:100px;">Табель</button>
	</div>
</div>

<!---------------------------------------------------------------->
<div  id="panel_blocks" class="g-4" style="background-color:#e2d1b4; padding:10px; border-radius:10px; border:2px solid #868268; margin:10px auto;">
<div class="g-row">
	<div class="g-2">
		<div class="g-row">
			<label>Дата
			<input  type="text" name="Date" value="<?php print $_REQUEST['Date']; ?>" class="date_input g-2"/>
			</label>
		</div>
		<div class="g-row">
			<label>Причина заявки
			<textarea name="Name_Work" class="g-2" style="height:100px;"><?php print($_REQUEST['Name_Work']);  ?></textarea>
			</label>
		</div>
	</div>
	<div class="g-2">
		<div class="g-row">
			<label>Заказчик
			<select name="lstEmployee" class="g-2">
				<option><?php print getNameEmployee($tmplstEmployee); ?></option>
                <?php
                //заполнение списка сотрудников
                getArrayNameEmployee();
                ?>
			</select>
			</label>
		</div>
		<div class="g-row">
			<label>Исполнитель
			<select name="lstPerformer" class="g-2">
				<option><?php print getNameEmployee($tmplstPerformer); ?></option>
                <?php
                //заполнение списка сотрудников-исполнителей
                getArrayNameEmployeeSAPR();
                ?>
			</select>
			</label>
		</div>
		<div class="g-row">
			<label>Время выполнения
			<input type="text" name="Time" value="<?php print $_REQUEST['Time']; ?>" class="g-2"/>
			</label>
		</div>
	</div>
</div>
<div class="g-row">
    <label>Комментарий
    <textarea name="Comment" class="g-4"><?php print($_REQUEST['Comment']);  ?></textarea></label>
</div>
<div class="g-row">
	<button type="submit" name="rbselect" value="insert" class="f-bu f-bu-success" style="margin-right:40px; margin-left:30px; width:100px;">Вставка</button>
	<button type="submit" name="rbselect" value="update" class="f-bu f-bu-default" style="margin-right:20px; width:100px;">Изменить</button>
	<button type="submit" name="rbselect" value="delete" class="f-bu f-bu-warning" style="margin-right:40px; margin-left:30px; width:100px;">Удалить</button>
	<button type="submit" name="rbselect" value="copy" class="f-bu f-bu-default" style="margin-right:20px; width:100px;">Копировать</button>
</div>
</div>			


<?php
//отображение данных
if ( count($errors) == 0 )
{
	ViewTableSAPR();
}
print "</form>";
}
else {
	print "<a href=\"../index1.php\">Назад</a><br />\n";
	print "<div class=\"validation\">";
	print "<H3>Отсутствуют права на просмотр информации!</H3>\n";
	print "</div>";
	print "<div class=\"info\">";
	print "<H4>Перейдите по ссылке на главную страницу</H4>\n";
	print "</div>";
	exit;
}
?>

</body>

<script type="text/javascript">
$(document).ready(function(){
							$('.f-nav.f-nav-tabs>li>a').click(activateLI);
                            $('.f-table-zebra tr>td:nth-child(4)').css('text-align', 'left');



							$('#report_sapr').click(function(){
														$('#panel_blocks').slideToggle(400)
														$('#report_blocks').slideToggle(400);
														});
							$('#report_blocks').dblclick(function(){
															$('#report_blocks').slideUp(400);
															$('#panel_blocks').slideDown(400)
															});

							});




function activateLI(eventObj)
{

	if ($(eventObj.target).parent().hasClass('active'))
	{
		return true;
	} else
	{
		$(eventObj.target).parent().siblings().removeClass('active');
		$(eventObj.target).parent().addClass('active');
		return true;
	}


}
</script>
</html>
<?php
//если есть данные и нет ошибок, перегружаем страницу
if (!empty($_POST) AND
$_REQUEST['rbselect']!="copy" AND
count($errors) == 0)
{
	header("Location: $_SERVER[PHP_SELF]");
	exit();
}
//сбросить содержимое буфера
ob_end_flush();

