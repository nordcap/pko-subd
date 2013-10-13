<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является страницей отражающий график загрузки подразделений
в годовой промежуток времени
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
session_start();
include_once("../modules/lib/lib.php");
//include_once("../modules / lib / Tabel_Time.php");
//include_once("../js / jscript.js");
require_once("begin1.php");
//в зависимости с какого отчета был перенаправлен пользователь, генерируем ссылку обратно
switch ( $_GET['Type'] ) {
    case "timeReport":
    print "<a href=\"../Report/timeReport.php\">Назад</a><br />\n";
    break;
    case "hourReport":
    print "<a href=\"../Report/hourReport.php\">Назад</a><br />\n";
    break;
    case "Project_Output_Object":
    print "<a href=\"../Report/object.php\">Назад</a><br />\n";
    break;
    case "ReportA1":
    print "<a href=\"../Report/ReportA1.php\">Назад</a><br />\n";
    break;
    case "ReportA4":
    print "<a href=\"../Report/ReportA4.php\">Назад</a><br />\n";
    break;
    case "ProjectOutput":
    print "<a href=\"../Report/ProjectOutput.php\">Назад</a><br />\n";
    break;
    case "ProjectOutput_Archive":
    print "<a href=\"../Report/ProjectOutput_Archive.php\">Назад</a><br />\n";
    break;
    case "ProjectOutputA1":
    print "<a href=\"../Report/ProjectOutputA1.php\">Назад</a><br />\n";
    break;
    case "ProjectOutputA4":
    print "<a href=\"../Report/ProjectOutputA4.php\">Назад</a><br />\n";
    break;
}
//print " < H2 > График загрузки отделов по месяцам</H2><br />\n";
$graphData = $_SESSION['graphData'];
$planTime  = $_SESSION['planTime'];  //плановое время для отдела

//нарисовать график из полученных данных
?>
<script language="JavaScript" type="text/javascript">
    var chart;
    $(document).ready(function() {
            chart = new Highcharts.Chart({
                    chart: {
                        renderTo: 'container',
                        type: 'column'
                    },
                    title: {
                        text: 'график загрузки подразделений'
                    },
                    subtitle: {
                        <?php

                        switch ($_SESSION['rbReport'])
                        {
                            case "time":
                            ?>
                            text: 'по затраченному времени'
                            <?php
                            break;

                            case "man_hour":
                            ?>
                            text: 'по общим трудозатратам'
                            <?php
                            break;

                            case "sheet_a1":
                            ?>
                            text: 'по затратам листов формата А1'
                            <?php
                            break;

                            case "sheet_a4":
                            ?>
                            text: 'по затратам листов формата А4'
                            <?php
                            break;


                            case "project_output":
                            ?>
                            text: 'по утверждённым трудозатратам'
                            <?php
                            break;

                            case "project_outputA1":
                            ?>
                            text: 'по листажу А1'
                            <?php
                            break;

                            case "project_outputA4":
                            ?>
                            text: 'по листажу А4'
                            <?php
                            break;

                            case "project_output_archive":
                            ?>
                            text: 'по архивным трудозатратам'
                            <?php
                            break;

                        }
                        ?>

                    },
                    xAxis: {
						<?php if($_GET['Type']=="Project_Output_Object"){?>
						 categories: ['ТО','КИПиА','СТРО','ГП','ПСО','ВКиОВ','КО','ЭО']
						<?php } else {?>
                        categories: ['январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь']
						<?php }?>
                    },
                    yAxis: {
                        title: {
                            <?php
                            switch ($_SESSION['rbReport'])
                            {
                                case "time":
                                ?>
                                text: 'часы'
                                <?php
                                break;

                                case "man_hour":
                                ?>
                                text: 'чел./ч.'
                                <?php
                                break;

                                case "sheet_a1":
                                ?>
                                text: 'листы'
                                <?php
                                break;

                                case "sheet_a4":
                                ?>
                                text: 'листы'
                                <?php
                                break;


                                case "project_output":
                                ?>
                                text: 'чел./ч.'
                                <?php
                                break;

                                case "project_outputA1":
                                ?>
                                text: 'листы'
                                <?php
                                break;

                                case "project_outputA4":
                                ?>
                                text: 'листы'
                                <?php
                                break;

                                case "project_output_archive":
                                ?>
                                text: 'чел./ч.'
                                <?php
                                break;
                            }
                            ?>

                        }
                    },
                    tooltip: {
                        enabled: false,
                        formatter: function() {
                            return '<b>'+ this.series.name +'</b><br/>'+
                            this.x +': '+ this.y +'°C';
                        }
                    },
                    plotOptions: {
                        column: {
                            dataLabels: {
                                enabled: true
                            },
                            enableMouseTracking: false
                        }
                    },
                    series: [{
                            name: 'факт',
                            data: [
                                <?php foreach($graphData as $key=>$value)
                                {
                                    print $value;
                                    print ",";
                                }
                                ?>
                            ]
                        }
                        <?php if($_GET['Type']=="ProjectOutput"){
                            ?>
                            ,
                            {
                                name: 'план',
                                data:[
                                    <?php foreach($planTime as $key=>$value)
                                    {
                                        print $value;
                                        print ",";
                                    }
                                    ?>
                                ]
                            }
                            <?php
                        }?>
                    ]




                });
        });
</script>

<script language="JavaScript" src="../js/Highcharts-2.2.5/js/highcharts.js" type="text/javascript">
</script>
<script language="JavaScript" src="../js/Highcharts-2.2.5/js/modules/exporting.js" type="text/javascript">
</script>
<div id="container" style="min-width: 400px; height: 500px; margin: 0 auto">
</div>
<?php
require_once("../end1.php");
?>
