<?php
if(isset($_POST['dataToExcel']))
{

	include_once("../../modules/lib/PHPExcel/IOFactory.php");
	include_once("../../modules/lib/lib.php");
	$objPHPExcel = PHPExcel_IOFactory::load("../../modules/template/template_utr.xls");
	$objPHPExcel->setActiveSheetIndex(0);
	$aSheet = $objPHPExcel->getActiveSheet();
	$cell = 3;
	$n = 1;
  foreach(unserialize($_POST['dataToExcel']) as $key=>$row)
  {
        $aSheet->setCellValue('A'.$cell,$n); //номер
		$aSheet->setCellValue('B'.$cell,$row['Number_Project']);
		$aSheet->setCellValue('C'.$cell,$row['Number_UTR']);
		$aSheet->setCellValue('D'.$cell,$row['Customer_Service']);
		$aSheet->setCellValue('E'.$cell,$row['Plant_Destination']);
		$aSheet->setCellValue('F'.$cell,$row['Name_Project']);
		$aSheet->setCellValue('G'.$cell,$row['Man_Hour_Sum']);
		$aSheet->setCellValue('H'.$cell,$row['ManHourSumAll']);
		
		//форматирование ячеек
		setBorderStyle($aSheet, 'A', $cell);
		setBorderStyle($aSheet, 'B', $cell);
		setBorderStyle($aSheet, 'C', $cell);
		setBorderStyle($aSheet, 'D', $cell);
		setBorderStyle($aSheet, 'E', $cell);
		setBorderStyle($aSheet, 'F', $cell);
		setBorderStyle($aSheet, 'G', $cell);
		setBorderStyle($aSheet, 'H', $cell);		

        $cell++;
		$n++;
  }
//создаем объект класса-писателя
include_once("../../modules/lib/PHPExcel/Writer/Excel5.php");
$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
//для сохранения на сервере можно использовать...
//$objWriter->save("c:\\CommDocs\\saveExcelUTR.xls");


//для сохранения на локальном диске или просмотра в Экселе можно использовать...
//выводим заголовки
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="saveExcelUTR.xls"');
header('Cache-Control: max-age=0');
//выводим в браузер таблицу с бланком
$objWriter->save('php://output');
}
