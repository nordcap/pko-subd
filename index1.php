<?php
session_start();
//начать буферизацию вывода
ob_start();
//подключение библиотек и модулей
include_once("modules/lib/lib.php");
checkSession(); //проверка на присутствие в сессии идентификатора пользователя
require_once("begin1.php");

//в зависимости от должности ставим коэффициент
switch ($_SESSION['Name_Post'])
{
	case "Инженер-проектировщик":
	$tmpCf_Hour = 1.15;
	break;
	case "Инженер-проектировщик 2 кат.":
	$tmpCf_Hour = 1.1;
	break;
	case "Ведущий инженер":
	$tmpCf_Hour = 0.95;
	break;
	case "Сметчик":
	$tmpCf_Hour = 1.15;
	break;
	case "Сметчик 2 кат.":
	$tmpCf_Hour = 1.1;
	break;
	case "Ведущий сметчик":
	$tmpCf_Hour = 0.95;
	break;
	case "Начальник отдела":
	$tmpCf_Hour = 0.95;
	break;
	case "Зам.начальника отдела":
	$tmpCf_Hour = 0.95;
	break;
	case "Начальник бюро":
	$tmpCf_Hour = 0.95;
	break;
	case "Старший специалист":
	$tmpCf_Hour = 0.95;
	break;
	default:
	$tmpCf_Hour = 1;
}
//по умолчанию коэф. заполнения листа =1
$tmpCf_Sheet = 1;

	$errors = array(); //объявляется массив ошибок
	switch ( $_REQUEST['rbselect'] )
	{
		case "insert":
        checkSee(); //проверка на просмотр  для блокировки занесения информации в период с 20 по 25 числа отчетного месяца
		//проверка на ошибки
		checkEmpty();   //заполнение недостающих полей нулями
		checkEmptyDate();//проверка правильности внесения даты
		checkFullNumber();//проверка на внесение проектов только с номером
		checkCloseProject(); //проверка на внесение работы с законченным проектом
		if ( count($errors) > 0 )
		{
			display_errors();
		}
		break;
		case "update":
        checkSee(); //проверка на просмотр
        //проверка на ошибки
		checkEmpty();  //заполнение недостающих полей нулями
		checkEmptyDate();  //проверка правильности внесения даты
		checkStatementBVP(); //если работа уже утверждена то ошибка
		checkFullNumber();//проверка на внесение проектов только с номером
		checkCloseProject(); //проверка на внесение работы с законченным проектом
		if ( count($errors) > 0 )
		{
			display_errors(); //вывод предупреждений
		}
		break;
		case "delete":
        checkSee(); //проверка на просмотр
        //нельзя  удалить утвержденную работу
		checkStatementBVP(); //если работа уже утверждена то ошибка
		if ( count($errors) > 0 )
		{
			display_errors(); //вывод предупреждений
		}
		break;

    case "hand_all":
        checkSee(); //проверка на просмотр
		if ( count($errors) > 0 )
		{
			display_errors(); //вывод предупреждений
		}
		break;
		case "copy":
		//если выбрана опция копирование данных
		//checkEmpty();  //заполнение недостающих полей нулями
		$Comma_separated = implode(",", $_REQUEST['design']);
		$Query = "SELECT design.Id, design.Date, ".
		"employee.Family, employee.Name, employee.Patronymic, ".
		"project.Number_Project, project.Name_Project, ".
		"mark.Name_Mark, mark.Comment_Mark, work.Name_Work,work.Type_Work, work.Comment, ".
		"design.Time1, design.Sheet_A1, design.k_Sheet, ".
		"design.Time3, design.Sheet_A3, design.num_cher, ". //Добавил design.num_cher Ден
		"design.Time4, design.Sheet_A4, design.prov, ".//Добавил design.prov, Ден
		"design.Time_Collection, design.Time_Agreement, design.Time, design.Man_Hour_Sum, design.checkBVP, design.statusBVP ".
		"FROM design, work, employee, project, mark, man_hour ".
		"WHERE ".
		"((design.id_Mark = mark.id) AND ".
		"(man_hour.id_Mark= mark.id) AND ".
		"(man_hour.id_Work= work.id) AND ".
		"(design.id_Project = project.id) AND ".
		"(design.id_Work = work.id) AND ".
		"(design.id_Employee = employee.id)) AND (employee.id=".escapeshellarg($_SESSION['Id_Employee'])." AND ".
		"(design.Id IN ($Comma_separated))) ".
		"LIMIT 1";
		if ( !($dbResult = mysql_query($Query, $link)) )
		{
			print "Выборка не удалась!\n";
		}
		else   {
			while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
			{
				if ( $row['Comment'] == "формат А1" )
				{
					$_REQUEST['Sheet_A1'] = $row['Sheet_A1'];
					$_REQUEST['Time1'] = $row['Time1'];
					$tmpSheet = $_REQUEST['Sheet_A1'];
					$tmpTime = $_REQUEST['Time1'];
				}
				elseif ( $row['Comment'] == "формат А3" )
				{
					$_REQUEST['Sheet_A3'] = $row['Sheet_A3'];
					$_REQUEST['Time3'] = $row['Time3'];
					$tmpSheet = $_REQUEST['Sheet_A3'];
					$tmpTime = $_REQUEST['Time3'];
				}
				elseif ( $row['Comment'] == "формат А4")
				{
					$_REQUEST['Sheet_A4'] = $row['Sheet_A4'];
					$_REQUEST['Time4'] = $row['Time4'];
					$tmpSheet = $_REQUEST['Sheet_A4'];
					$tmpTime = $_REQUEST['Time4'];
				}
				else {
					$_REQUEST['Time4'] = $row['Time4'];
					$tmpTime = $_REQUEST['Time4'];
				}
				$_REQUEST['Time_Collection'] = $row['Time_Collection'];
				$_REQUEST['Time_Agreement'] = $row['Time_Agreement'];
				$_REQUEST['Date'] = $row['Date'];
				$_REQUEST['Number_Project'] = $row['Number_Project'];
				$_REQUEST['Сf_Sheet'] = $row['k_Sheet'];
				$_REQUEST['Name_Project'] = $row['Name_Project'];
				$_REQUEST['Name_Work'] = $row['Name_Work']." - ".$row['Type_Work'];
				$_REQUEST['Name_Mark'] = $row['Name_Mark']." - ".$row['Comment_Mark'];
				$_REQUEST['Comment'] = $row['Comment'];
				$_REQUEST['num_cher'] = $row['num_cher'];
				$_REQUEST['prov'] = $row['prov'];
				$_REQUEST['checkBVP'] = $row['checkBVP'];
			}
			//заносим значения во временные переменные
			$tmpTime_Collection = $_REQUEST['Time_Collection'];
			$tmpTime_Agreement = $_REQUEST['Time_Agreement'];
			$tmpDate = date("d.m.y", $_REQUEST['Date']);
			$tmpNumberProject = getIdProject($_REQUEST['Number_Project']);
			$tmpNameProject = $_REQUEST['Name_Project'];
			$tmpNameWork = getIdWork($_REQUEST['Name_Work']);
			$tmpNameMark = getIdMark($_REQUEST['Name_Mark']);
			$tmpFormat = "Документ имеет ".$_REQUEST['Comment'];
			$tmpNumCher = $_REQUEST['num_cher'];
			$tmpProv = $_REQUEST['prov'];
			$tmpCheckBVP = $_REQUEST['checkBVP'];
			$tmpCf_Sheet = $_REQUEST['Сf_Sheet'];
		}
		break;
	}
	if( count($errors) > 0 )
	{
		//если есть ошибки при вводе или обновлении, то заносим их во временные переменные переменные
		$tmpSheet = $_REQUEST['Sheet_A'];
		$tmpTime = $_REQUEST['Time'];
		$tmpCf_Hour = $_REQUEST['Сf_Man_Hour'];
		$tmpTime_Collection = $_REQUEST['Time_Collection'];
		$tmpTime_Agreement = $_REQUEST['Time_Agreement'];
		$tmpDate = date("d.m.y", mktime());
		$tmpNumberProject = $_REQUEST['Number_Project'];
		$tmpNameWork = $_REQUEST['Name_Work'];
		$tmpNameMark = $_REQUEST['Name_Mark'];
		$tmpFormat = "Документ имеет ".$_REQUEST['Comment'];
		$tmpNumCher = $_REQUEST['num_cher'];
		$tmpProv = $_REQUEST['prov'];
		$tmpCheckBVP = $_REQUEST['checkBVP'];
		$tmpFormat = $row['Comment'];
		$tmpCf_Sheet = $_REQUEST['Сf_Sheet'];
	}

?>
	<div class="f-nav-bar">
			<div class="f-nav-bar-title">
			<img src="img/img_index/Control_Panel.png" alt="АРМ трудозатраты" width="48"  align="absmiddle" style="	display: inline-block;"/>АРМ трудозатраты
			</div>
		<div class="f-nav-bar-body">
			<ul class="f-nav">
				<li><a href="changeParol.php"><img src="img/img_index/Key.png" alt="Сменить пароль" class="f-img"/>Сменить пароль</a></li>
				<li><a href="admin/admin.php"><img src="img/img_index/Security2.png" alt="Админинистрирование" class="f-img"/>Админинистрирование</a></li>
				<li><a href="search.php"><img src="img/img_index/Find.png" alt="Поиск" class="f-img"/>Поиск</a></li>
				<li><a href="report/object.php"><img src="img/img_index/Performance.png" alt="Отчеты" class="f-img"/>Отчеты</a></li>
				<li><a href="geod/geod.php"><img src="img/img_index/Teodolit.png" alt="Геодезия" class="f-img"/>Геодезия</a></li>
				<li><a href="sapr/sapr.php"><img src="img/img_index/Printer_Landscape.png" alt="Бюро САПР" class="f-img"/>Бюро САПР</a></li>
				<li><a href="archive/archive.php"><img src="img/img_index/Floppy.png" alt="Архив" class="f-img"/>Архив</a></li>
				<li><a href="checkTabel.php"><img src="img/img_index/Calendar.png" alt="Проверить табель" class="f-img"/>Проверить табель</a></li>
				<li><a href="ИНСТРУКЦИЯ по Трудозатраты ПУ.doc"><img src="img/img_index/Info.png" alt="Справка" class="f-img"/>Справка</a></li>
				
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
<div class="g-9" style="background-color:#e2d1b4; padding:10px; border-radius:10px; border:2px solid #868268; margin:10px auto;">
	<div class="g-row"> <!--1 уровень-->
		<div class="g-3">
			<label>Дата
			<input type="text" name="Date" value="<?php print $tmpDate;?>"  class="date_input g-3"/></label>
		</div>
		<div class="g-6">
			<label>Наименование проекта
                <div id="id_Name_Project">
			        <textarea name="Name_Project" readonly class="g-6" style="height:26px;"><?php print $tmpNameProject; ?></textarea>
                </div>
            </label>
		</div>
	</div>	
	<div class="g-row"> <!--1 уровень-->
		<div class="g-2">
			<label>Номер проекта
			<select name="Number_Project" id="id_Number_Project" class="g-2">
			<option><?php print getNameProject($tmpNumberProject); ?></option>
            <?php
                //заполнение списка вида работ
                getArrayProject();
            ?>
			</select></label>
		</div>
		<div class="g-2">
			<label>ГИП
				<div id="id_GIP">
					<input type="text" class="g-2" readonly>
				</div>
			</label>
		</div>
		<div class="g-5">
			<label>Марка
			<select  name="Name_Mark" id="id_name_mark" class="g-5">
			<option><?php print getNameMark($tmpNameMark); ?></option>
            <?php
                //заполнение списка марок работ
                getArrayNameMark();
            ?>
			</select></label>
		</div>
	</div>
	<div class="g-row"> <!--1 уровень-->
		<div class="g-3">
			<label>Номер чертежа
			<input type="text" name="num_cher" value="<?php print $tmpNumCher; ?>" class="g-3" placeholder="необязательное поле"/></label>
		</div>
		<div class="g-6">
			<label>Наименование работ
                <div id="id_work">
        			<select  name="Name_Work" id="id_name_work" class="g-6" onchange="javascript:format();">
        			<option><?php print getNameWork($tmpNameWork); ?></option>
                    <?php
                        //заполнение списка вида работ
                        getArrayWorkName($tmpNameMark);
                    ?>
        			</select>
                </div>
            </label>
            <div id="id_format">
    			<p class="f-input-help"><?php print $tmpFormat; ?></p>
            </div>
		</div>	
	</div>
	<div class="g-row"> <!--1 уровень-->
		<div class="g-2">
			<label>Листы
			<input type="text" name="Sheet_A" value="<?php print $tmpSheet; ?>" class="g-2" /></label>
		</div>
		<div class="g-2">
			<label>Сбор данных
			<input type="text" name="Time_Collection" value="<?php print $tmpTime_Collection; ?>" class="g-2" /></label>
		</div>			
		<div class="g-2">
			<label>Коэф.квалификации
			<input type="text" name="Сf_Man_Hour" value="<?php print $tmpCf_Hour; ?>" class="g-2 help"
            title="Нормы разработаны на среднюю квалификацию исполнителя, <br>т.е. на инж.-проектировщик 1 кат.
                   При выполнении док-ии <br>исполнителями другой категории вводить коэ-т на квалификацию:<br>
                   <b>0.95</b> - ведущий инженер<br><b>1</b> - 1 категория<br><b>1.1</b> - 2 категория<br>
                   <b>1.15</b> - без категории\" readonly /></label>
		</div>			
		<div class="g-2">
			<label class="f-check">

                <?php  if ( $tmpCheckBVP == "TRUE" ) {  ?>
                	<input type="checkbox" name="checkBVP" value="TRUE" style="margin-top:20px;" checked/>сдано на проверку
                <?php } else { ?>
                     <input type="checkbox" name="checkBVP" value="TRUE" style="margin-top:20px;"/>сдано на проверку
                    <?php  }  ?>
			</label>
		</div>
	</div>
	<div class="g-row"> <!--1 уровень-->
		<div class="g-2">
			<label>Факт.время
			<input type="text" name="Time" value="<?php print $tmpTime; ?>" class="g-2" /></label>
		</div>	
		<div class="g-2">
			<label>Согласование
			<input type="text" name="Time_Agreement" value="<?php print $tmpTime_Agreement; ?>" class="g-2"  /></label>
		</div>
		<div class="g-2">
			<label>К-т.заполн. листа
			<input type="text"  name="Сf_Sheet" value="<?php print $tmpCf_Sheet; ?>" class="g-2 help" title = "Коэффициент заполнения варьируется от 0 до 1" /></label>
		</div>
		<div class="g-2">
        <?php
        //определенные группы лиц занимаются и проверкой чертежей
        if ( ($_SESSION['Name_Post'] == "Администратор") or
        ($_SESSION['Name_Post'] == "Начальник управления") or
        ($_SESSION['Name_Post'] == "Начальник отдела") or
        ($_SESSION['Name_Post'] == "Начальник бюро") or
        ($_SESSION['Name_Post'] == "Старший специалист") or
        ($_SESSION['Name_Post'] == "Старший механик") or
        ($_SESSION['Name_Post'] == "Старший технолог") )
        {
        ?>
			<label>Проверка чертежей
			<input type="text"  name="prov" value="<?php print $tmpProv; ?>" class="g-2" placeholder="Заполняет нач.бюро"/></label>
        <?php } ?>
		</div>
		
	</div>
	<div class="g-row"> <!--1 уровень-->
		<div class="g-12">
			<button type="submit" name="rbselect" value="select" class="f-bu">Общая</button>
			<button type="submit" name="rbselect" value="insert" class="f-bu f-bu-success">Вставка</button>
			<button type="submit" name="rbselect" value="update" class="f-bu f-bu-default">Изменить</button>
			<button type="submit" name="rbselect" value="delete" class="f-bu f-bu-warning">Удалить</button>
			<button type="submit" name="rbselect" value="copy" class="f-bu f-bu-default">Копировать</button>
			<button type="submit" name="rbselect" value="select_all" class="f-bu f-bu-default">Выделить всё</button>
			<button type="submit" name="rbselect" value="hand_all" class="f-bu f-bu-success">На проверку</button>
		</div>
	</div>	
	
</div>


 <?php
//отображение данных
if ( count($errors) == 0 )
{
	ViewTableDesign();
}
 ?>
 </form>


<?php
print "</body>\n";
include_once("js/jscript.js");
?>
<script  type="text/javascript">
$(document).ready(function(){
     $('.f-table-zebra tr>td:nth-child(4)').css('text-align', 'left');
  });
</script>
<?php
print "</html>\n";//сбросить содержимое буфера
ob_end_flush();
?>