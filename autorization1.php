<?php

/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей авторизации сотрудника
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
//session_destroy();
session_start();
//инициализирум механизм сесссий
//начать буферизацию вывода
ob_start();
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Автоматизированная система учета трудозатрат</title>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; CHARSET=utf-8">
<link  rel="stylesheet" type="text/css" href="style.css">
<link href="favicon.ico" rel="icon" type="image/x-icon" />
<style type="text/css">
body {
    background: url(img/background.jpg) no-repeat;
    background-size: 100%;
}
</style>
</head>
<body>
<?php
if (stristr($_SERVER['HTTP_USER_AGENT'],'MSIE 6.0') OR stristr($_SERVER['HTTP_USER_AGENT'],'MSIE 7.0') OR stristr($_SERVER['HTTP_USER_AGENT'],'MSIE 8.0')) {
            print "<div class=\"g error\" style=\"margin:auto; position:relative; top:150px; border-radius:10px;\">\n";
            print "<H3>Ваш браузер Internet Explorer. Для работы с приложением используйте браузеры Chrome, Firefox, Opera</H3>\n";
            print "</div>\n";
            exit;
}

?>
<div id="auth" class="g-6">
    <form action="<?php  print $_SERVER['PHP_SELF'];?>" method="post">
        <div class="g-5">
            <div class="f-row">
                <label>Логин</label>
                    <div class="f-input">
                        <input Type="text" Name="Login">
                    </div>
            </div>
            <div class="f-row">
                <label>Пароль</label>
                    <div class="f-input">
                        <input Type="password" Name="Password">
                    </div>
            </div>
            <div class="f-row">
                    <button type="submit" name="Вход"  value="ОК" class="f-bu" style="margin-left: 220px; width: 100px;">OK</button>
            </div>
        </div>
    </form>
    <div id="countdown"></div>
    <script language="JavaScript" src="js/countdown.js" type="text/javascript"></script>
</div>

<!--<div id="countdown"></div>
<script language="JavaScript" src="js/countdown.js" type="text/javascript"></script>-->
<?php
//--------------------
//сделать проверка на корректность ввода`
if (!empty ($_REQUEST['Вход']))
{
  include_once ("modules/lib/lib.php");
  include_once ("modules/lib/Tabel_Time.php");
  $Query = "SELECT Employee.Id, Employee.Family, Employee.Name,
	Employee.Patronymic, Employee.Status, " . "Department.Name_Department, Office.Name_Office, Post.Name_Post,
	Employee.Sanction, Employee.Login, Employee.Password " . "FROM Department, Office, Post, Employee " . "WHERE " . "(Post.id = Employee.id_Post) AND " . "(Office.id = Employee.id_Office) AND " . "(Department.id = Employee.id_Department) AND " . "(Employee.Status = " . escapeshellarg('TRUE') . ") AND " . "(Employee.Login = " . escapeshellarg(trim(strtolower($_REQUEST['Login']))) . ") AND " . "(Employee.Password = " . escapeshellarg(cryptParol(trim(strtolower($_REQUEST['Password'])))) . ")";
  if (!($dbResult = mysql_query($Query, $link)))
  {
    print "Запрос не выполнен!<br />\n";
    exit;
  }
  else
  {
  //смотрим количество строк в последнем запросе
    if (mysql_num_rows($dbResult) == 0)
    {
      print "<div class=\"g error\" style=\"margin:auto; position:relative; top:150px; border-radius:10px;\">\n";
      print "<H3>Не верные имя или пароль</H3>\n";
      print "</div>\n";
    }
    else
    {
      $row = mysql_fetch_array($dbResult, MYSQL_BOTH);
      //заносим необходимые данные в переменные сессии
      $_SESSION['Id_Employee'] = $row['Id'];
      //небходимо для ViewTableDesign
      $_SESSION['Family'] = $row['Family'];
      $_SESSION['Name'] = $row['Name'];
      $_SESSION['Patronymic'] = $row['Patronymic'];
      $_SESSION['Family_short'] = getNameEmployee_short($row['Id']);




      $_SESSION['Sanction'] = $row['Sanction'];
      //возможность утверждения работ
      $_SESSION['Login'] = $_REQUEST['Login'];
      $_SESSION['Password'] = cryptParol(trim(strtolower($_REQUEST['Password'])));
      $_SESSION['Name_Department'] = $row['Name_Department'];
      $_SESSION['Name_Office'] = $row['Name_Office'];
      $_SESSION['Name_Post'] = $row['Name_Post'];
      $_SESSION['LimitDate'] = TRUE;
      //определяет  наличие блокировки проверки даты
      //TRUE - проверка есть, FALSE - проверки нет
      $CurrentDate = getdate();
      //определяем текущую дату
      $_SESSION['CurrentDay'] = $CurrentDate['mday'];
      //определяем текущий день
      $_SESSION['CurrentMonth'] = $CurrentDate['mon'];
      //определяем текущий месяц
      $_SESSION['CurrentYear'] = $CurrentDate['year'];
      //определяем текущий год
      $_SESSION['pageMonth'] = $_SESSION['CurrentMonth'];
      //определяем текущую страницу
      $_SESSION['pageYear'] = $_SESSION['CurrentYear'];
      require_once ('block.php');

      if (defined("BLOCK"))    //если определена константа
      {
        if (constant("BLOCK") == 0)   //не заблокировано
        {
        //для руководства (ГИПов) всегда доступ открыт
          if ($_SESSION['Name_Department'] == "Руководство")
          {
            header("Location:Admin/AdminProject.php");
          }
          else
          {
            header("Location:index1.php");
          }
        }
        elseif (constant("BLOCK") == 1)    //заблокировано
        {
          //для руководства (ГИПов) всегда доступ открыт
          if ($_SESSION['Name_Department'] == "Руководство")
          {
            header("Location:Admin/AdminProject.php");
            exit;
          }
          if (($_SESSION['Name_Post'] == "Экономист") OR ($_SESSION['Name_Post'] == "Администратор") OR ($_SESSION['Name_Post'] == "Работник архива"))
          {
          //переходим на главную страницу
            header("Location:index1.php");
          }
          else
          {
            exit;
          }
        }
      }
/*      //для руководства (ГИПов) всегда доступ открыт
      if ($_SESSION['Name_Department'] == "Руководство")
      {
        header("Location:Admin/AdminProject.php");
      }
      else
      {
        header("Location:index1.php");
        //ВСЕМ ЗАПРЕЩЕН ДОСТУП К ТРУДОЗАТРАТАМИ, КРОМЕ ИЗБРАННЫХ

        /*			if ( ($_SESSION['Name_Post'] == "Экономист") OR
        ($_SESSION['Name_Post'] == "Администратор") OR
        ($_SESSION['Name_Post'] == "Работник архива"))
        {
        //переходим на главную страницу
        header("Location:index1.php");
        }
        else {
        exit;
        }
      }*/
    }
  }
}
//print(phpinfo(32));
require_once ("end1.php");
//сбросить содержимое буфера
ob_end_flush();
?>