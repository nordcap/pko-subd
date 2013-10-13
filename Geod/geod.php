<?php
session_start();
//начать буферизацию вывода
ob_start();
include_once("../modules/lib/lib.php");
checkSession(); //проверка на присутствие в сессии идентификатора пользователя
include_once("begin1.php");
//id=15 - Строкань
if (($_SESSION['Name_Post'] == "Администратор") OR
($_SESSION['Name_Post'] == "Экономист") OR
($_SESSION['Name_Department'] == "ГД") OR
($_SESSION['Id_Employee'] == 15))
{
	$errors = array(); //объявляется массив ошибок
	if (!empty($_POST))
	{
		//проверяем какие кнопки были нажаты
		if (isset($_REQUEST['btnReport']))
		{
			switch ($_REQUEST['rbselectReport'])
			{
				case "rbMonth":
				$_SESSION['lstMonth'] = $_REQUEST['lstMonth'];
				$_SESSION['lstYear'] = $_REQUEST['lstYear'];
				$_SESSION['k_addDate'] = $_REQUEST['k_addDate'];
				$_SESSION['rbPeriod'] = $_REQUEST['rbPeriod'];
				header("Location: ReportMonth.php");
				exit();
				break;
				case "rbYear":
				//определим массив с значениями месяцев в сек заданного года
				if ( $_REQUEST['lstYear'] == "Выберите год" )
				{
					$date_time_array = getdate();
					$lstYear = $date_time_array['year'];
				}
				else {
					$lstYear = $_REQUEST['lstYear'];
				}
				//определяем массив масяцев выбранного года
				$_SESSION['arrayMonth'] = getArrayTimeYear($lstYear, $_REQUEST['rbPeriod']);
				header("Location: ReportYear.php");
				exit();
				break;
				case "rbUTR":
				$_SESSION['lstMonth'] = $_REQUEST['lstMonth'];
				$_SESSION['lstYear'] = $_REQUEST['lstYear'];
				$_SESSION['rbPeriod'] = $_REQUEST['rbPeriod'];
				header("Location: ReportMonthUTR.php");
				exit();
				break;
			}
		}
		//проверка ошибок
		switch ( $_REQUEST['rbselect'] )
		{
			case "insert":
			checkEmptyDate();//проверка правильности даты
			checkEmptyGeod();
			checkFullNumber();//проверка на внесение проектов только с номером
//			checkCloseProject(); //проверка на внесение работы с законченным проектом
			break;
			case "update":
			checkEmptyDate();//проверка правильности даты
			checkEmptyGeod();
			checkFullNumber();//проверка на внесение проектов только с номером
//			checkCloseProject(); //проверка на внесение работы с законченным проектом
			break;
			case "copy":
			//если выбрана опция копирование данных
			$Comma_separated = implode(",", $_REQUEST['design']);
			$Query = "SELECT Id, Date, Name_Work, Time, id_Project, id_Tender, ".
			"Price, Number, Comment, Category, Unit_Measurement ".
			"FROM geod ".
			"WHERE ".
			"geod.Id IN ($Comma_separated) LIMIT 1";
			if ( !($dbResult = mysql_query($Query, $link)) )
			{
				print "Выборка $Query не удалась!\n";
			}
			else   {
				while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
				{
					$_REQUEST['Date'] = date("d.m.y", $row['Date']);
					$_REQUEST['Time'] = $row['Time'];
					$_REQUEST['lstProject'] = $row['id_Project'];
					$_REQUEST['lstTender'] = $row['id_Tender'];
					$_REQUEST['Name_Work'] = $row['Name_Work'];
					$_REQUEST['Price'] = $row['Price'];
					$_REQUEST['Number'] = $row['Number'];
					$_REQUEST['Comment'] = $row['Comment'];
					$_REQUEST['Category'] = $row['Category'];
					$_REQUEST['Unit_Measurement'] = $row['Unit_Measurement'];
				}
				$tmpNumberProject = $_REQUEST['lstProject'];
				$tmpNumberTender = $_REQUEST['lstTender'];
			}
			break;
		}
	}
	if ( count($errors) > 0 )
	{
		display_errors();
		$_REQUEST['Date'] = date("d.m.y", mktime());
		$tmpNumberProject = $_REQUEST['lstProject'];
		$tmpNumberTender = $_REQUEST['lstTender'];
	}
?>





	<div class="f-nav-bar">
			<div class="f-nav-bar-title">
			<img src="../img/img_index/Control_Panel.png" alt="АРМ трудозатраты" width="48"  align="absmiddle" style="	display: inline-block;"/>АРМ трудозатраты
			</div>
		<div class="f-nav-bar-body">
			<ul class="f-nav">
                <li><a href="../index1.php"><img src="../img/img_search/arrow_left_green_48.png" alt="На главную страницу" class="f-img"/>Главная...</a></li>
               	<li><a href="AdminGeod.php"><img src="../img/img_index/Security2.png" alt="Админинистрирование" class="f-img"/>Админинистрирование</a></li>
				<li><a href="#" id="report_geod"><img src="../img/img_index/Performance.png" alt="Отчеты" class="f-img"/>Отчеты</a></li>
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
	<input type="radio" name="rbselectReport" value="rbMonth" checked/>за месяц<br/>
	<input type="radio" name="rbselectReport" value="rbYear"/>за год<br/>
	<input type="radio" name="rbselectReport" value="rbUTR"/>УТР<br/>
	<input type="checkbox" name="k_addDate" value="TRUE"/>расширенный<br/>
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
	<div class="g-row"> <!--1 уровень-->
		<div class="g-3">
			<div class="g-row">
				<div class="g-3">
					<label>Дата
					<input type="text" name="Date" value="<?php print $_REQUEST['Date']; ?>" class="date_input g-3"/></label>
				</div>
			</div>
			<div class="g-row">
				<div class="g-3">
					<label>Номер проекта
					<select  name="lstProject" class="g-3">
					<option><?php print getNameProject($tmpNumberProject); ?></option>
                    <?php getArrayProject(); ?>
					</select></label>
				</div>
			</div>
			<div class="g-row">
				<div class="g-3">
					<label>№ заявки
					<select  name="lstTender" class="g-3">
					<option><?php print getNameTenderGeod($tmpNumberTender); ?></option>
                     <?php getArrayTender(); ?>
					</select></label>
				</div>
			</div>


		</div>
		<div class="g-3">
			<label>Наименование работ
			<textarea name="Name_Work" class="g-3" style="height:160px;"><?php print $_REQUEST['Name_Work']; ?></textarea></label>
		</div>
	</div>	
	<div class="g-row"> <!--1 уровень-->
		<div class="g-3">
			<label>Категория сложности
			<select  name="Category" class="g-3">
			<option><?php print $_REQUEST['Category']; ?></option>
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			</select></label>
		</div>
		<div class="g-3">
			<label>Единица изм.
			<select  name="Unit_Measurement" class="g-3">
			<option><?php print $_REQUEST['Unit_Measurement']; ?></option>
            <?php 	getArrayNameMeasure(); ?>
			</select></label>
		</div>
	</div>
	<div class="g-row"> <!--1 уровень-->
		<div class="g-3">
			<label>Трудозатраты
			<input type="text" name="Time" value="<?php print $_REQUEST['Time']; ?>" class="g-3"/></label>
		</div>
		<div class="g-3">
			<label>Цена за 1 ед.
			<input type="text" name="Price" value="<?php print $_REQUEST['Price']; ?>" class="g-3"/></label>
		</div>
	</div>
	<div class="g-row"> <!--1 уровень-->
		<div class="g-3">
			<label>Исполнители
			<input type="text" name="Comment" class="g-3" value="<?php print $_REQUEST['Comment']; ?>"/></label>
		</div>
		<div class="g-3">
			<label>Количество
			<input type="text" name="Number" value="<?php print $_REQUEST['Number']; ?>" class="g-3"/></label>
		</div>
	</div>
	<div class="g-row">
		<ul class="f-nav">
			<li style="padding-top:20px;color:#d50000; font-size:110%; font-weight: bold;">Коэффициенты</li>
			<li><a href="#" id="pol_li"><img src="../img/img_geod/CAT Helmet.png" alt="полевые"  class="f-img"/>полевые</a></li>
			<li><a href="#" id="kam_li"><img src="../img/img_search/vectorgfx.png" alt="камеральные"  class="f-img"/>камеральные</a></li>
			<li><a href="#" id="sum_li"><img src="../img/img_geod/kedit.png" alt="итоговые"  class="f-img"/>итоговые</a></li>
		</ul>
	</div>

	
	<!--вывод окон с коэффициентами-------------------------------------------------------->

<div id="k_pol" class="blocks_menu">
	<input type="checkbox" name="k_field" value="0.85"/>полевые работы <i>(K=0.85)</i><br/>
	<input type="checkbox" name="k_light" value="1.15"/>работа при искусственном освещении<i>(K=1.15)</i><br/>
	<input type="checkbox" name="k_vibration" value="1.25"/>работа в помещениях с вибрацией<i>(K=1.25)</i><br/>
	<input type="checkbox" name="k_season" value="1.3"/>сезонность<i>( K=1.3 c 10 окт по 10 мая)</i><br/>
	<input type="checkbox" name="k_bridge" value="1.3"/>работа с подмостей(K=1.3)<br/>
	<input type="checkbox" name="k_regime" value="1.25"/>режимный<i>(K=1.25)</i>
</div>


<div id="k_kam" class="blocks_menu">
	<input type="checkbox" name="k_profil" value="1.75"/>составление планов продольных профилей<i>(K=1.75)</i><br/>
	<input type="checkbox" name="k_plane" value="1.1"/>составление планов в цвете<i>(K=1.10)</i><br/>
	<input type="checkbox" name="k_it" value="1.2"/>камеральные работы с применением ИТ<i>(K=1.2)</i><br/>
	<input type="checkbox" name="k_kalka" value="0.4"/>копирование исп съемок на кальку<i>(K=0.4)</i><br/>
	<input type="checkbox" name="k_regime_kam" value="1.25"/>режимный<i>(K=1.25)</i>
</div>

	
<div id="k_sum" class="blocks_menu">
	<div>
			<input type="text" name="k_smeta" value="<?php print $_REQUEST['k_smeta']; ?>" class="g-1" style="margin-right:10px;"><label>к-т к итогу сметной стоимости</label>
	</div>
	<div class="k_row">
			<input type="text" name="k_index" value="<?php print $_REQUEST['k_index']; ?>" class="g-1" style="margin-right:10px;"><label>к-т индексации</label>
	</div>
	<div class="k_row">
			<input type="text" name="k_correction" value="<?php print $_REQUEST['k_correction']; ?>" class="g-1" style="margin-right:10px;"><label>корректировочный к-т</label>
	</div>
</div>







	<!--конец вывода окон с коэффициентами---------------------------------------------->



	<div class="g-row" style="margin-top:0px;"> <!--1 уровень-->
		<div class="g-6">
			<button type="submit" name="rbselect" value="select" class="f-bu">Общая</button>
			<button type="submit" name="rbselect" value="insert" class="f-bu f-bu-success">Вставка</button>
			<button type="submit" name="rbselect" value="update" class="f-bu f-bu-default">Изменить</button>
			<button type="submit" name="rbselect" value="delete" class="f-bu f-bu-warning">Удалить</button>
			<button type="submit" name="rbselect" value="copy" class="f-bu f-bu-default">Копировать</button>

		</div>
	</div>

</div>


<!------------------------------------------------------------------------------------------------------------------------>


<?php
	//отображение данных
	if ( count($errors) == 0 )
	{
		ViewTableGeod();
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
                            $('.f-table-zebra tr>td:nth-child(5)').css('text-align', 'left');
                            $('.f-table-zebra tr>td:nth-child(11)').css('text-align', 'left');
							$('.f-nav.f-nav-tabs>li>a').click(activateLI);
							$('#pol_li').click(function(){
														close_k('#pol_li');
														$('#k_pol').slideToggle(400);
														});
							$('#k_pol').dblclick(function(){
															$('#k_pol').slideUp(400);
															});


							$('#kam_li').click(function(){
														close_k('#kam_li');
														$('#k_kam').slideToggle(400);
														});
							$('#k_kam').dblclick(function(){
															$('#k_kam').slideUp(400);
															});


							$('#sum_li').click(function(){
														close_k('#sum_li');
														$('#k_sum').slideToggle(400);
														});
							$('#k_sum').dblclick(function(){
															$('#k_sum').slideUp(400);
															});


							$('#report_geod').click(function(){
														$('#panel_blocks').slideToggle(400)
														$('#report_blocks').slideToggle(400);
														});
							$('#report_blocks').dblclick(function(){
															$('#report_blocks').slideUp(400);
															$('#panel_blocks').slideDown(400)
															});

							});



function close_k(obj)
{
switch(obj){
	case '#pol_li':
		$('#k_kam').slideUp(400);
		$('#k_sum').slideUp(400);
		break;

	case '#kam_li':
		$('#k_pol').slideUp(400);
		$('#k_sum').slideUp(400);
		break;		

	case '#sum_li':
		$('#k_pol').slideUp(400);
		$('#k_kam').slideUp(400);
		break;
		}
}


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
if (!empty($_POST) AND $_REQUEST['rbselect']!="copy" AND count($errors) == 0)
{
	header("Location: $_SERVER[PHP_SELF]");
	exit();
}
//сбросить содержимое буфера
ob_end_flush();
?>