<?php
session_start();
//начать буферизацию вывода
ob_start();
include_once("../modules/lib/lib.php");
checkSession(); //проверка на присутствие в сессии идентификатора пользователя
require_once("begin1.php");
if (($_SESSION['Name_Post'] == "Администратор") OR
($_SESSION['Name_Post'] == "Экономист") OR
($_SESSION['Name_Post'] == "Работник архива"))
{
	$errors = array(); //объявляется массив ошибок
	if (!empty($_POST))
	{
		//проверяем какие кнопки были нажаты
		if (isset($_REQUEST['btnReport']))
		{
			$_SESSION['lstMonth'] = $_REQUEST['lstMonth'];
			$_SESSION['lstYear'] = $_REQUEST['lstYear'];
			$_SESSION['NumberProjectReport'] = $_REQUEST['Number_Project_Report'];
			$_SESSION['NameMarkReport'] = $_REQUEST['Name_Mark_Report'];
			$_SESSION['lstPerformerReport'] = $_REQUEST['lstPerformerReport'];
			$_SESSION['rbPeriod'] = $_REQUEST['rbPeriod'];
			header("Location: ReportArchive.php");
			exit();
		}
		//проверка ошибок
		switch ( $_REQUEST['rbselect'] )
		{
			case "insert":
			checkEmptyArchive();//проверка на заполнение полей
			checkEmptyDate();//проверка правильности даты
			checkFullNumber();//проверка на внесение проектов только с номером
			break;
			case "update":
			checkEmptyArchive();//проверка на заполнение полей
			checkEmptyDate();//проверка правильности даты
			checkFullNumber();//проверка на внесение проектов только с номером
			break;
			case "copy":
			//если выбрана опция копирование данных
			$Comma_separated = implode(",", $_REQUEST['design']);
			$Query = "SELECT archive.id, Date, id_Work, id_Mark, id_Project, id_Employee, Sheet_A1, Sheet_A4, NumberDraw, archive.Comment,
			mark.Name_Mark, mark.Comment_Mark, work.Name_Work,work.Type_Work, (work.Comment) AS WorkCom, project.Number_Project, project.Name_Project ".
			" FROM archive, employee, project, mark, work ".
			" WHERE ".
			"(archive.id_Mark = mark.id) AND ".
			"(archive.id_Project = project.id) AND ".
			"(archive.id_Work = work.id) AND ".
			"(archive.id_Employee = employee.id) AND ".
			"archive.id IN ($Comma_separated)";
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				print "Выборка $Query не удалась!\n".mysql_error();
			}
			else   {
				$row = mysql_fetch_array($dbResult, MYSQL_BOTH);
				$_REQUEST['Date'] = date("d.m.y", $row['Date']);  //дата
				$_REQUEST['Number_Project'] = $row['Number_Project']; //номер объекта
				$_REQUEST['Name_Project'] = $row['Name_Project']; //номер объекта
				$_REQUEST['NumberDraw'] = $row['NumberDraw'];  //номер чертежа
				$_REQUEST['Name_Work'] = $row['Name_Work']." - ".$row['Type_Work'];  //работа
				$_REQUEST['Name_Mark'] = $row['Name_Mark']." - ".$row['Comment_Mark'];//марка
				$_REQUEST['Comment'] = $row['Comment']; //комментарий
				//по комментарию к работе определяем формат листа
				if ( $row['WorkCom'] == "формат А1" )
				{
					$_REQUEST['Sheet_A1'] = $row['Sheet_A1'];
					$tmpSheet = $_REQUEST['Sheet_A1'];
				}
				elseif ( $row['WorkCom'] == "формат А3" )
				{
					$_REQUEST['Sheet_A4'] = $row['Sheet_A4'];
					$tmpSheet = $_REQUEST['Sheet_A4'];
				}
				elseif ( $row['WorkCom'] == "формат А4")
				{
					$_REQUEST['Sheet_A4'] = $row['Sheet_A4'];
					$tmpSheet = $_REQUEST['Sheet_A4'];
				}
				$tmpNumberDraw = $_REQUEST['NumberDraw'];
				$tmpNumberProject = getIdProject($_REQUEST['Number_Project']);
				$tmpNameProject = $_REQUEST['Name_Project'];
				$tmpNameWork = getIdWork($_REQUEST['Name_Work']);
				$tmpNameMark = getIdMark($_REQUEST['Name_Mark']);
				$tmpDate = date("d.m.y", mktime());
				$tmplstEmployee = $row['id_Employee']; //исполнитель
				$tmpComment = $_REQUEST['Comment'];
			}
			break;
		}
	}
	if ( count($errors) > 0 )
	{
		$tmpNameProject = $_REQUEST['Name_Project'];
		$tmplstEmployee = getIdEmployee($_REQUEST['lstEmployee']); //заказчик
		$tmpDate = date("d.m.y", mktime());
		$tmpSheet = $_REQUEST['Sheet_A'];
		$tmpNumberDraw = $_REQUEST['NumberDraw'];
		$tmpNumberProject = $_REQUEST['Number_Project'];
		$tmpNameWork = $_REQUEST['Name_Work'];
		$tmpNameMark = $_REQUEST['Name_Mark'];
		$tmpComment = $_REQUEST['Comment'];
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
				<li><a href="#" id="report_archive"><img src="../img/img_index/Performance.png" alt="Отчеты" class="f-img"/>Отчеты</a></li>
			</ul>
		</div>
		<div class="f-nav-right" style="position: absolute; top:5px;right:10px;">
        <?php
            print $_SESSION['Family_short'];
        ?>
		</div>
		<div class="f-nav-right" style="position: absolute; top:25px;right:10px;">
			Сегодня <?php print date("d.m.Y"); ?>
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
	<label>по сотруднику
	<select name="lstPerformerReport" class="g-2">
		<option>Выберите сотрудника</option>
        <?php
        	//заполнение списка сотрудников-исполнителей
        	getArrayNameEmployee();
        ?>
	</select></label>
	</div>
	<div class="g-row">
	<label>по проекту
	<select name="Number_Project_Report" class="g-2">
		<option>Выберите номер</option>
        <?php
        	//заполнение списка вида работ
        	getArrayProject();
        ?>
	</select></label>
	</div>
	<div class="g-row">
	<label>по марке
	<select name="Name_Mark_Report" class="g-2">
		<option>Выберите марку</option>
        <?php
        	//заполнение списка марок работ
        	getArrayNameMark();
        ?>
	</select></label>
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
	<input type="radio" name="rbPeriod" value="1-31"/>календарный<br/>
	<input type="radio" name="rbPeriod" value="26-25" checked/>отчетный
	</div>
	<div class="g-row">
	<button type="submit" name="btnReport" value="Отчет" class="f-bu" style="margin-left:30px;">&nbsp;ОК&nbsp;</button>
	</div>
</div>


<div  id="panel_blocks" class="g-6" style="background-color:#e2d1b4; padding:10px; border-radius:10px; border:2px solid #868268; margin:10px auto;">
	<div class="g-row">
		<div class="g-3">
			<label>Дата
			<input  type="text" name="Date"  value="<?php print $tmpDate; ?>" class="date_input g-3"/></label>
		</div>
		<div class="g-3">
			<label>Исполнитель
			<select name="lstEmployee" class="g-3">
				<option><?php print getNameEmployee($tmplstEmployee); ?></option>
                <?php
                	//заполнение списка сотрудников
                	getArrayNameEmployee();
                ?>
			</select></label>
		</div>
	</div>
	<div class="g-row">
		<div class="g-3">
			<label>№ проекта
			<select name="Number_Project" id="id_Number_Project" class="g-3">
				<option><?php print getNameProject($tmpNumberProject); ?></option>
                <?php
                	//заполнение списка вида работ
                	getArrayProject();
                ?>
			</select></label>
		</div>
		<div class="g-3">
			<label>листы
			<input type="text" name="Sheet_A" value="<?php print $tmpSheet; ?>" class="g-3"/></label>
		</div>
	</div>
	<div class="g-row">
		<label>Наименование проекта
        <div id="id_Name_Project">
    		<textarea name="Name_Project" readonly class="g-6" style="height:40px;">
            <?php print $tmpNameProject;  ?>
            </textarea>
        </div></label>
	</div>
	<div class="g-row">
		<label>Номер чертежа
		<input type="text" name="NumberDraw" value="<?php print $tmpNumberDraw; ?>" class="g-6"/></label>
	</div>
	<div class="g-row">
		<label>Марка
			<select name="Name_Mark" id="id_name_mark" class="g-6">
				<option><?php print getNameMark($tmpNameMark); ?></option>
                <?php
                	//заполнение списка марок работ
                	getArrayNameMark();
                ?>
    		</select></label>
	</div>
	<div class="g-row">
		<label>Наименование работ
            <div id="id_work">
    			<select name="Name_Work" id="id_name_work" class="g-6">
    				<option><?php print getNameWork($tmpNameWork); ?></option>
                    <?php
                    	//заполнение списка вида работ
                    	getArrayWorkName($tmpNameMark);
                    ?>
    			</select>
            </div></label>
	</div>
	<div class="g-row">
		<label>Комментарий
		<input type="text" name="Comment" value="<?php print $tmpComment; ?>" class="g-6"/></label>
	</div>



	<div class="g-row"> <!--1 уровень-->
		<div class="g-6">
			<button type="submit" name="rbselect" value="select" class="f-bu">Выборка</button>
			<button type="submit" name="rbselect" value="insert" class="f-bu f-bu-success">Вставка</button>
			<button type="submit" name="rbselect" value="update" class="f-bu f-bu-default">Изменить</button>
			<button type="submit" name="rbselect" value="delete" class="f-bu f-bu-warning">Удалить</button>
			<button type="submit" name="rbselect" value="copy" class="f-bu f-bu-default">Копировать</button>

		</div>
	</div>

</div>



<?php
	//отображение данных
	if ( count($errors) == 0 )
	{
		ViewTableArchive();
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
                            $('#id_Number_Project').change(select_number_project);
                            $('#id_name_mark').change(select_work);
                            $('.f-table-zebra tr>td:nth-child(6)').css('text-align', 'left');
                            $('.f-table-zebra tr>td:nth-child(3)').css('text-align', 'left');
                            $('.f-table-zebra tr>td:nth-child(9)').css('text-align', 'left');
                            $('.f-table-zebra tr>td:nth-child(10)').css('text-align', 'left');

                        	$('.f-nav.f-nav-tabs>li>a').click(activateLI);



							$('#report_archive').click(function(){
														$('#panel_blocks').slideToggle(400)
														$('#report_blocks').slideToggle(400);
														});
							$('#report_blocks').dblclick(function(){
															$('#report_blocks').slideUp(400);
															$('#panel_blocks').slideDown(400)
															});

							});


function select_number_project(eventObj)
{
  $.post('select_project.php',
    {'data': $(eventObj.target).val()},
    success,
    'html');
  function success(returnData)
  {
    $('#id_Name_Project').html(returnData);
  }
};


function select_work(eventObj)
{
  $.post('select_work.php',
    {'data': $(eventObj.target).val()},
    success,
    'html');


  function success(returnData)
  {
    $('#id_work').html(returnData);
  }

};

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

