<?php
if(isset($_POST['dataToExcel']))
{

	include_once("../../modules/lib/PHPExcel/IOFactory.php");
	include_once("../../modules/lib/lib.php");
	$objPHPExcel = PHPExcel_IOFactory::load("../../modules/template/template_project.xls");
	$objPHPExcel ->setActiveSheetIndex(0);
	$aSheet = $objPHPExcel->getActiveSheet();
	$cell = 3; //текущая активная ячейка
	$n = 1;//номер позиции
  foreach(unserialize($_POST['dataToExcel']) as $row)
  {
		//объединяем ячейки если максимальное кол-во строк по маркам или отделам больше 1
		if($row['count'] > 1)
		{	
			$cell_merge = $cell + $row['count'] - 1;//номер последней объединяемой ячейки
			$aSheet ->mergeCells('A'.$cell.':'.'A'.$cell_merge);
			$aSheet ->mergeCells('B'.$cell.':'.'B'.$cell_merge);
			$aSheet ->mergeCells('C'.$cell.':'.'C'.$cell_merge);
			$aSheet ->mergeCells('D'.$cell.':'.'D'.$cell_merge);
			$aSheet ->mergeCells('E'.$cell.':'.'E'.$cell_merge);
			$aSheet ->mergeCells('F'.$cell.':'.'F'.$cell_merge);
			$aSheet ->mergeCells('G'.$cell.':'.'G'.$cell_merge);
			$aSheet ->mergeCells('H'.$cell.':'.'H'.$cell_merge);
			$aSheet ->mergeCells('I'.$cell.':'.'I'.$cell_merge);
			$aSheet ->mergeCells('R'.$cell.':'.'R'.$cell_merge);
			$aSheet ->mergeCells('S'.$cell.':'.'S'.$cell_merge);
			$aSheet ->mergeCells('T'.$cell.':'.'T'.$cell_merge);
			$aSheet ->mergeCells('U'.$cell.':'.'U'.$cell_merge);
			$aSheet ->mergeCells('V'.$cell.':'.'V'.$cell_merge);
		} else
		{
			$cell_merge = $cell; //если ячейки не объединяются то приравниваем к номеру текущей ячейке
		}

        $aSheet ->setCellValue('A'.$cell,$n); //номер
		$aSheet ->setCellValue('B'.$cell,$row['Number_Project']);
		$aSheet ->setCellValue('C'.$cell,$row['Name_Project']);
		$aSheet ->setCellValue('D'.$cell,$row['Number_UTR']);
		$aSheet ->setCellValue('E'.$cell,$row['FIO']);
		$aSheet ->setCellValue('F'.$cell,$row['DateOpenProject']);
		$aSheet ->setCellValue('G'.$cell,$row['DateCloseProject']);
		$aSheet ->setCellValue('H'.$cell,$row['listDep']);
		$aSheet ->setCellValue('I'.$cell,$row['Dep']);
		
		//если есть задания между отделами		
		if(is_array($row['arrayTask']) AND $row['count'] > 1)
		{
			
			foreach($row['arrayTask'] as $key => $value)
			{
				$cur_cell = $cell;//номер ячейки для марок и отделов
				switch($key){
					case 'From':
						foreach($value as $v)
						{
							$aSheet ->setCellValue('J'.$cur_cell++,$v);	
						}
						break;
					case 'To':
						foreach($value as $v)
						{
							$aSheet ->setCellValue('K'.$cur_cell++,$v);	
										
						}
						break;				
					case 'Comment':
						foreach($value as $v)
						{
							$aSheet ->setCellValue('L'.$cur_cell++,$v);
											
						}
						break;	
					case 'Date':
						foreach($value as $v)
						{
							$aSheet ->setCellValue('M'.$cur_cell++,$v);
											
						}
						break;	
				}
			}

		} 

		if(is_array($row['arrayMark']) AND $row['count'] > 1)
		{
			foreach($row['arrayMark'] as $key => $value)
			{
				$cur_cell = $cell;//номер ячейки для марок и отделов
				switch($key){
					case 'Mark':
						foreach($value as $k => $v)
						{
							$aSheet ->setCellValue('N'.$cur_cell++,$v.$row['arrayMark']['NumberMark'][$k]);
						}
						break;
					case 'Comment':
						foreach($value as $v)
						{
							$aSheet ->setCellValue('O'.$cur_cell++,$v);				
						}
						break;				
					case 'DateBVP':
						foreach($value as $v)
						{
							$aSheet ->setCellValue('P'.$cur_cell++,$v);	
						}
						break;	
					case 'DateCustomer':
						foreach($value as $v)
						{
							$aSheet ->setCellValue('Q'.$cur_cell++,$v);			
						}
						break;	
				}
			}
		}
		
	
		$aSheet ->setCellValue('R'.$cell,$row['Report']);
		$aSheet ->setCellValue('S'.$cell,$row['Report_ManHour']);
		$aSheet ->setCellValue('T'.$cell,$row['Customer_Service']);
		$aSheet ->setCellValue('U'.$cell,$row['Plant_Destination']);
		$aSheet ->setCellValue('V'.$cell,$row['Man_Hour']);



		//форматирование ячеек таблицы		
		setBorderStyle($aSheet, 'A', $cell, $row['count']);//форматирование ячейки - обрамление границами
		setBorderStyle($aSheet, 'B', $cell, $row['count']);
		setBorderStyle($aSheet, 'C', $cell, $row['count']);
		setBorderStyle($aSheet, 'D', $cell, $row['count']);
		setBorderStyle($aSheet, 'E', $cell, $row['count']);
		setBorderStyle($aSheet, 'F', $cell, $row['count']);
		setBorderStyle($aSheet, 'G', $cell, $row['count']);
		setBorderStyle($aSheet, 'H', $cell, $row['count']);
		setBorderStyle($aSheet, 'I', $cell, $row['count']);
		setBorderStyle($aSheet, 'J', $cell, $row['count']);
		setBorderStyle($aSheet, 'K', $cell, $row['count']);
		setBorderStyle($aSheet, 'L', $cell, $row['count']);
		setBorderStyle($aSheet, 'M', $cell, $row['count']);
		setBorderStyle($aSheet, 'N', $cell, $row['count']);
		setBorderStyle($aSheet, 'O', $cell, $row['count']);
		setBorderStyle($aSheet, 'P', $cell, $row['count']);
		setBorderStyle($aSheet, 'Q', $cell, $row['count']);
		setBorderStyle($aSheet, 'R', $cell, $row['count']);
		setBorderStyle($aSheet, 'S', $cell, $row['count']);
		setBorderStyle($aSheet, 'T', $cell, $row['count']);
		setBorderStyle($aSheet, 'U', $cell, $row['count']);
		setBorderStyle($aSheet, 'V', $cell, $row['count']);



        $cell = $cell_merge + 1;
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
header('Content-Disposition: attachment;filename="saveExcelProject.xls"');
header('Cache-Control: max-age=0');
//выводим в браузер таблицу с бланком
$objWriter->save('php://output');
}
