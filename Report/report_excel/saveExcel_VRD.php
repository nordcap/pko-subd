<?php
session_start();     //инициализирум механизм сесссий
$row = $_SESSION['vrd'];
include_once("../../modules/lib/lib.php");

	$str = "Кемеровское ОАО 'Азот'\n";
	//запрос на выборку данных из табл. mark
	$arrayMark = getArrayFromMark($row['id']);
	//сливаем вместе марки и номера
	foreach($arrayMark['Mark'] as $key=>$value)
	{
		if ($arrayMark['NumberMark'][$key] == "")
		{
			$resultMark[][$arrayMark['id'][$key]] =  $arrayMark['Mark'][$key];
		}
		else {
			$resultMark[][$arrayMark['id'][$key]] =  $arrayMark['Mark'][$key].".".$arrayMark['NumberMark'][$key];
		}
		if ($arrayMark['NumberChange'][$key] == "")
		{
			$change[] =  "";
		}
		else
		{
			$change[] = $arrayMark['NumberChange'][$key];
		}
	}





	include_once("../../modules/lib/PHPExcel/IOFactory.php");
	$objPHPExcel = PHPExcel_IOFactory::load("../../modules/template/template_format.xls");
	$objPHPExcel->setActiveSheetIndex(0);
	$aSheet = $objPHPExcel->getActiveSheet();
	$cell = 5;
	$n = 1;
	foreach($resultMark as $arr)
	{
		foreach($arr as $id=>$NameMark)
		{
			$aSheet->setCellValue('B'.$cell,$n); //номер
			//обозначение
			$aSheet->setCellValue('D'.$cell,$row['Number_Project']."-".$NameMark);
			//наименование
			$aSheet->setCellValue('N'.$cell,getMark($id,"Comment_Mark"));
			//номер изменения
			if ($change[$n - 1] == "")
			{
				$aSheet->setCellValue('AI'.$cell,$change[$n - 1]);
			}
			else
			{
				$aSheet->setCellValue('AI'.$cell,"Изм. ".$change[$n - 1]);
			}
			$cell = $cell + 2;
			$n++;
		}
	}

	//заполнение основной надписи
	$aSheet->setCellValue('R50',$row['Number_Project']);
	$aSheet->setCellValue('AC50',$row['Number_Project']);
	$aSheet->setCellValue('N55',$row['Name_Project']);
	$aSheet->setCellValue('N52',$str.$row['Name_Customer']);


//создаем объект класса-писателя
include_once("../../modules/lib/PHPExcel/Writer/Excel5.php");
$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);

//для сохранения на локальном диске или просмотра в Экселе можно использовать...
//выводим заголовки
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="saveExcelVRD.xls"');
header('Cache-Control: max-age=0');
//выводим в браузер таблицу с бланком
$objWriter->save('php://output');

