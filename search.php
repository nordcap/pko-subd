<?php
session_start();
//начать буферизацию вывода
ob_start();
include_once("modules/lib/lib.php");
checkSession(); //проверка на присутствие в сессии идентификатора пользователя

$errors = array(); //объявляется массив ошибок
//для утверждающих работы
if ( isset($_REQUEST['rbstatusBVP']) )
{
	switch ($_REQUEST['rbstatusBVP'])
	{
		case "addstatus":
        checkSee(); //проверка на просмотр
		checkApprove();//проверка на возможность утверждения работы в данный период
		checkBVP(); //проверить на утвержденность в предыдущей месяце
		checkEmptyDate();//проверка правильности внесения даты
		checkDateClose();//проверка на дату окончания проекта (запрет утверждения)
        checkStatementBVP();//если работа в архиве то ошибка
		break;
		case "delstatus":
        checkSee(); //проверка на просмотр
		checkBVP(); //проверить на утвержденность в предыдущей месяце
		checkStatementBVP();//если работа в архиве то ошибка
		break;
        case "substatus":
        checkSee(); //проверка на просмотр
        checkBVP(); //проверить на утвержденность в предыдущей месяце
        checkEmptyDate();//проверка правильности внесения даты
        checkDateClose();//проверка на дату окончания проекта (запрет утверждения)
        checkStatementBVP();//если работа в архиве то ошибка
        break;
	}
	if( count($errors) > 0 )
	{
		//если есть ошибки при вводе или обновлении, то заносим их во временные переменные переменные
		$tmpDate = date("d.m.y", mktime());
		$_REQUEST['Date'] = $tmpDate;
	}
}
//для работников Архива
if ( isset($_REQUEST['rbstatusBVP_Archive']) )
{
	switch ($_REQUEST['rbstatusBVP_Archive'])
	{
		case "addstatus":
		checkEmptyDate();//проверка правильности даты
		break;
	}
	if( count($errors) > 0 )
	{
		//если есть ошибки при вводе или обновлении, то заносим их во временные переменные переменные
		$tmpDateArchive = date("d.m.y", mktime());
		$_REQUEST['DateArchive'] = $tmpDateArchive;
	}
}
if ( isset($_REQUEST['Поиск']) )
{
	$arrInterval = set_time_interval($_REQUEST['DateBegin'], $_REQUEST['DateEnd']);
	$_REQUEST['DateBegin'] = $arrInterval[0];
	$_REQUEST['DateEnd'] = $arrInterval[1];
}

//если есть ошибки- выводим их на экран
if ( count($errors) > 0 )
{
	display_errors();
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Автоматизированная система учета трудозатрат</title>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; CHARSET=utf-8">
<link  rel="stylesheet" type="text/css" href="style.css">
<link href="favicon.ico" rel="icon" type="image/x-icon" />
<script src="js/jquery.js"></script>
</head>
<body>
<form action="<?php print $_SERVER['PHP_SELF']; ?>" method="post">
	<div class="f-nav-bar">
			<div class="f-nav-bar-title">
			<img src="img/img_index/Control_Panel.png" alt="АРМ трудозатраты" width="48"  align="absmiddle" style="	display: inline-block;"/>АРМ трудозатраты
			</div>
		<div class="f-nav-bar-body">
			<ul class="f-nav">
				<li><a id="fio_li" href="#"><img src="img/img_search/users_two_48.png" alt="Сотрудники" class="f-img"/>Сотрудники</a></li>
				<li><a id="obj_li"  href="#"><img src="img/img_search/Tools.png" alt="Объекты" class="f-img"/>Объекты</a></li>
				<li><a id="mark_li" href="#"><img src="img/img_search/vectorgfx.png" alt="Марки" class="f-img"/>Марки</a></li>
				<li><a id="date_li" href="#"><img src="img/img_search/clock_48.png" alt="Дата" class="f-img"/>Дата</a></li>
				<li><a id="status_li" href="#"><img src="img/img_search/task.png" alt="Статус" class="f-img"/>Статус</a></li>
				<li><button type="submit" name="Поиск" value="Поиск по базе" class="f-bu f-bu-success">Поиск</button></li>
                <li><a href="index1.php"><img src="img/img_search/arrow_left_green_48.png" alt="На главную страницу" class="f-img"/>Главная...</a></li>
                <li><a href="printSearch.php"><img src="img/img_search/Print.png" alt="Печать" class="f-img"/>Печать</a></li>
			</ul>
		</div>
		<div class="f-nav-right" style="position: absolute; top:5px;right:10px;">
			<ul class="f-nav">
            <?php
            //утверждать проекты имеют право только следующие лица + утверждающие +ГИП
            if (($_SESSION['Name_Post'] == "Администратор") OR
            ($_SESSION['Name_Post'] == "Начальник отдела") OR
            ($_SESSION['Name_Post'] == "Зам.начальника отдела") OR
            ($_SESSION['Name_Post'] == "ГИП") OR
            ($_SESSION['Sanction'] == 'TRUE'))
            {
            ?>
			<li><a id="validation_li" href="#"><img src="img/img_search/Edit_Yes.png" alt="Утверждение" class="f-img"/>Утверждение</a></li>
            <?php }
            //сдавать проекты в архив могут только работники архива и администратор
            if (($_SESSION['Name_Post'] == "Администратор") OR
            ($_SESSION['Name_Post'] == 'Работник архива'))
            {  ?>
			<li><a id="archive_li" href="#"><img src="img/img_search/safe_closed.png" alt="Архив" class="f-img"/>Архив</a></li>
            <?php } ?>
			</ul>
		</div>
			

	</div>



<!--------- код выпадающих окон        -------------------------------------------------------------->
<!--    fio    --------------------------------->
<div  id="fio_block" class="blocks_menu">
	<div class="g-row">
		<div class="g-2">
			<label>ФИО
				<select name="lstEmployee" class="g-2">
                    <option>Выбор ФИО</option>
					<?php getArrayNameEmployee(); ?>
				</select></label>
		</div>
	</div>
    <?php
    //выборки по отделам делать могут только следующие лица + утверждающие
    if (($_SESSION['Name_Post'] == "Экономист") OR
    ($_SESSION['Name_Post'] == "Начальник управления") OR
    ($_SESSION['Name_Post'] == "Начальник отдела") OR
    ($_SESSION['Name_Post'] == "Зам.начальника отдела") OR
    ($_SESSION['Name_Post'] == "Администратор") OR
    ($_SESSION['Name_Post'] == "ГИП") OR
    ($_SESSION['Name_Post'] == "Работник архива") OR
    ($_SESSION['Sanction'] == 'TRUE'))
    { ?>
	<div class="g-row">
		<div class="g-2">
					<label>отдел
					<select name="Department" class="g-2">
						<option>Выбор отдела</option>
                        <?php getArrayNameDepartment(); ?>
					</select></label>
		</div>
	</div>
	<div class="g-row">
		<div class="g-2">
				<label>бюро
				<select name="Office" class="g-2">
					<option>Выбор бюро</option>
                    <?php getArrayNameOffice();  ?>
				</select></label>
		</div>
	</div>
    <?php } ?>



</div>
<!--object----------------------------------------------------->
<div id="obj_block" class="blocks_menu">
	<div class="g-row">
		<div class="g-2">
			<label>объекты
			<select name="lstObject"  class="g-2">
            <option>Выберите объект</option>
                <?php getArrayProject(); ?>
			</select></label>
		</div>
	</div>
</div>
<!--mark----------------------------------------------------->
<div id="mark_block" class="blocks_menu">
	<div class="g-row">
		<div class="g-2">
			<label>марки
			<select class="g-2" name="Name_Mark">
			<option>Выберите марку</option>
                <?php getArrayNameMark();  ?>
			</select></label>
		</div>
	</div>
</div>

<!--date----------------------------------------------------->
<div id="date_block" class="blocks_menu">

	<div class="g-row">
		<div class="g-2">
			<label>месяц</label>
            <?php printLstMonth(); ?>
		</div>
		<div class="g-2">
		<label>год</label>
        <?php printLstYear(); ?>
		</div>
	</div>
	<div class="g-row">
		<div class="g-2">
			<label>начальная дата
			<input type="date"  name="DateBegin" class="g-2"/></label>
		</div>
		<div class="g-2">
		<label>конечная дата
			<input type="date" name="DateEnd" class="g-2"/></label>
		</div>
	</div>
</div>
<!--status ----------------------------------------------------->
<div id="status_block" class="blocks_menu">
	<div class="g-row">
	<input  type="checkbox" name="searchBVP" value="TRUE"/>сдано на проверку<br/>
	<input  type="checkbox" name="statusBVP" value="TRUE"/>утверждено(поиск по дате утверждения)
	</div>
		<fieldset style="border:1px solid #868268;">
		<h4>период отчётности</h4>
		<input type="radio" name="rbPeriod" value="1-31"/>календарный<br/>
		<input type="radio" name="rbPeriod" value="26-25" checked="checked"/>отчётный
		</fieldset>
	
</div>

<!--утверждение ----------------------------------------------------->
<?php
//утверждать проекты имеют право только следующие лица + утверждающие +ГИП
if (($_SESSION['Name_Post'] == "Администратор") OR
($_SESSION['Name_Post'] == "Начальник отдела") OR
($_SESSION['Name_Post'] == "Зам.начальника отдела") OR
($_SESSION['Name_Post'] == "ГИП") OR
($_SESSION['Sanction'] == 'TRUE'))
{
?>
<div id="validation_block" class="blocks_menu">
	<div class="g-row">
		<div class="g-2">
		<label>дата сдачи
		<input type="date" name="Date" value="<?php print $tmpDate; ?>" class="g-2"/></label>
		</div>
	</div>
	<div class="g-row">
			<button type="submit" name="rbstatusBVP" value="addstatus" class="f-bu f-bu-success">утвердить</button>
			<button type="submit" name="rbstatusBVP" value="delstatus" class="f-bu f-bu-warning">вернуть</button>
            <?php
            if($_SESSION['Name_Department'] == "ПСО" OR $_SESSION['Name_Post'] == "Администратор"){ ?>
		   	<button type="submit" name="rbstatusBVP" value="substatus" class="f-bu f-bu-default">вне плана</button>
            <?php } ?>
	</div>

</div>
<?php
 }
//сдавать проекты в архив могут только работники архива и администратор
if (($_SESSION['Name_Post'] == "Администратор") OR
($_SESSION['Name_Post'] == 'Работник архива'))
{
?>

<!--archive ----------------------------------------------------->
<div id="archive_block" class="blocks_menu">
	<div class="g-row">
		<div class="g-2">
			<label>дата сдачи
			<input type="date" name="DateArchive" value="<?php print $tmpDateArchive; ?>" class="g-2"/></label>
		</div>
	</div>
	<div class="g-row">
			<button type="submit" name="rbstatusBVP_Archive" value="addstatus" class="f-bu f-bu-success">подтвердить</button>
			<button type="submit" name="rbstatusBVP_Archive" value="delstatus" class="f-bu f-bu-warning">вернуть</button>
	</div>

</div>
	<!--commit:100 сообщение об выбранных элементах-->
		<div id='message_header' class='f-message f-message-success'>
		<h4>Выбраны:</h4>
		<div id='message_employee'></div>
		<div id='message_department'></div>
		<div id='message_office'></div>
		<div id='message_project'></div>
		<div id='message_mark'></div>
		</div>
		<!--конец инф.блока-->
<?php
}
//отображение таблицы с данными
//отображение данных
if ( count($errors) == 0 )
{
	ViewTableSearchDesign();
}
?>
</form>


<?php
print "</body>\n";
?>

<script type="text/javascript">
$(document).ready(function(){
							
							$('#fio_li').click(function(obj){
														closeAll('#fio_li');
														$('#fio_block').css('left',obj.pageX);
														$('#fio_block').slideToggle(400);
														});
							$('#fio_block').dblclick(function(){
														$('#fio_block').slideUp(400);

															});

							$('#obj_li').click(function(obj){
														closeAll('#obj_li');
														$('#obj_block').css('left',obj.pageX);
														$('#obj_block').slideToggle(400);
														});
							$('#obj_block').dblclick(function(){
														$('#obj_block').slideUp(400);

															});


							$('#mark_li').click(function(obj){
														closeAll('#mark_li');
														$('#mark_block').css('left',obj.pageX);
														$('#mark_block').slideToggle(400);
														});
							$('#mark_block').dblclick(function(){
														$('#mark_block').slideUp(400);

															});

							$('#date_li').click(function(obj){
														closeAll('#date_li');

                                                          $('#date_block').css('left',obj.pageX);
                                                          $('#date_block').slideToggle(400);
                                                          //$('.date_selector').css('left',0);
														});
							$('#date_block').dblclick(function(){
														$('#date_block').slideUp(400);

															});

							$('#status_li').click(function(obj){
														closeAll('#status_li');
														$('#status_block').css('left',obj.pageX);
														$('#status_block').slideToggle(400);

                                                        });
							$('#status_block').dblclick(function(){
														$('#status_block').slideUp(400);

															});

							$('#validation_li').click(function(obj){
														closeAll('#validation_li');
														$('#validation_block').css('left',obj.pageX-200);
														$('#validation_block').slideToggle(400);
                                                        });
							$('#validation_block').dblclick(function(){
														$('#validation_block').slideUp(400);

														});

							$('#archive_li').click(function(obj){
														closeAll('#archive_li');
														$('#archive_block').css('left',obj.pageX-200);
														$('#archive_block').slideToggle(400);
														});
							$('#archive_block').dblclick(function(){
														$('#archive_block').slideUp(400);

														});
														

							});



function closeAll(obj)
{

switch(obj){
	case '#fio_li':
		$('#obj_block').slideUp(200);
		$('#mark_block').slideUp(200);
		$('#date_block').slideUp(200);
		$('#status_block').slideUp(200);
		$('#validation_block').slideUp(200);
		$('#archive_block').slideUp(200);
		break;

	case '#obj_li':
		$('#fio_block').slideUp(200);
		$('#mark_block').slideUp(200);
		$('#date_block').slideUp(200);
		$('#status_block').slideUp(200);
		$('#validation_block').slideUp(200);
		$('#archive_block').slideUp(200);
		break;

	case '#mark_li':
		$('#fio_block').slideUp(200);
		$('#obj_block').slideUp(200);
		$('#date_block').slideUp(200);
		$('#status_block').slideUp(200);
		$('#validation_block').slideUp(200);
		$('#archive_block').slideUp(200);
		break;

		case '#date_li':
		$('#fio_block').slideUp(200);
		$('#obj_block').slideUp(200);
		$('#mark_block').slideUp(200);
		$('#status_block').slideUp(200);
		$('#validation_block').slideUp(200);
		$('#archive_block').slideUp(200);
		break;

	case '#status_li':
		$('#fio_block').slideUp(200);
		$('#obj_block').slideUp(200);
		$('#mark_block').slideUp(200);
		$('#date_block').slideUp(200);
		$('#validation_block').slideUp(200);
		$('#archive_block').slideUp(200);
		break;

	case '#validation_li':
		$('#fio_block').slideUp(200);
		$('#obj_block').slideUp(200);
		$('#mark_block').slideUp(200);
		$('#date_block').slideUp(200);
		$('#status_block').slideUp(200);
		$('#archive_block').slideUp(200);
		break;

	case '#archive_li':
		$('#fio_block').slideUp(200);
		$('#obj_block').slideUp(200);
		$('#mark_block').slideUp(200);
		$('#date_block').slideUp(200);
		$('#status_block').slideUp(200);
		$('#validation_block').slideUp(200);
		break;

}

}

</script>


<!--вызов скрипта обрабатывающего выбор элементов в поиске-->
<script>
$(document).ready(function(){
	$('#message_header').hide(); /*commit:100*/
	
	$('select[name=lstEmployee]').change(getEmployee); /*commit:100*/
	$('select[name=Department]').change(getDepartment); /*commit:100*/
	$('select[name=Office]').change(getOffice); /*commit:100*/
	$('select[name=lstObject]').change(getProject); /*commit:100*/	
	$('select[name=Name_Mark]').change(getMark); /*commit:100*/	
});	



/*обработчик выбора списка сотрудников*/
/*commit:100*/
function getEmployee(obj)
{
	var id_employee = $(obj.target).val();
	$.post('modules/post/get_employee.php', 
	{data : id_employee}, 
	function(returnData)
	{
		$('#message_employee').html('<p><mark>' + returnData + '</mark></p>');
		$('#message_department').html('');
		$('#message_office').html('');

		//$('#message_content:last-child').append('<p>'+ returnData+'</p>');
	},
	'html');
	$('#message_header').show();
															
}

/*обработчик выбора списка отделов*/
/*commit:100*/
function getDepartment(obj)
{
	var id_department = $(obj.target).val();
	$.post('modules/post/get_department.php', 
	{data: id_department},
	function(returnData)
	{
		$('#message_department').html('<p>Отдел: <b>' + returnData + '</b></p>');

	},
	'html');
	$('#message_header').show();
}


/*обработчик выбора списка бюро*/
/*commit:100*/
function getOffice(obj)
{
	var id_office = $(obj.target).val();
	$.post('modules/post/get_office.php', 
	{data: id_office},
	function(returnData)
	{
		$('#message_office').html('<p>Бюро: <b>' + returnData + '</b></p>');
	},
	'html');
	$('#message_header').show();
}

/*обработчик выбора списка проектов*/
/*commit:100*/
function getProject(obj)
{
	var id_project = $(obj.target).val();
	$.post('modules/post/get_project.php', 
	{data: id_project},
	function(returnData)
	{
		$('#message_project').html('<p>Название проекта: <b><mark>' + returnData + '</mark></b></p>');
	},
	'html');
	$('#message_header').show();
}


/*обработчик выбора списка марок*/
/*commit:100*/
function getMark(obj)
{
	var id_mark = $(obj.target).val();
	$.post('modules/post/get_mark.php', 
	{data: id_mark},
	function(returnData)
	{
		$('#message_mark').html('<p>Марка: <b>' + returnData + '</b></p>');
	},
	'html');
	$('#message_header').show();
}

</script>


</script>




<?php
print "</html>";//сбросить содержимое буфера
ob_end_flush();