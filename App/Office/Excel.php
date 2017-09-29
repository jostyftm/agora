<?php

namespace App\Office;

use PHPExcel as PHPExcel;
/**
* 
*/
class Excel
{

	private $_xls;

	// 
	private $fields = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N"];

	function __construct()
	{
		$this->_xls = new PHPExcel();
	}
	
	private function setProperties()
	{
		// Set document properties
		$this->_xls->getProperties()->setCreator("Maarten Balliauw")
					 ->setLastModifiedBy("Maarten Balliauw")
					 ->setTitle("Office 2007 XLSX Test Document")
					 ->setSubject("Office 2007 XLSX Test Document")
					 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
					 ->setKeywords("office 2007 openxml php")
					 ->setCategory("Test result file");
	}

	private function getHeaders()
	{
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="01simple.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
	}

	public function downloadExcel()
	{	
		
		// HOJA 1
		$this->_xls->createSheet(0);
		// Add some data
		$this->_xls->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'Hello')
		            ->setCellValue('B2', 'world!')
		            ->setCellValue('C1', 'Hello')
		            ->setCellValue('D2', 'world!');

		// Miscellaneous glyphs, UTF-8
		$this->_xls->setActiveSheetIndex(0)
		            ->setCellValue('A4', 'Miscellaneous glyphs')
		            ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

		// Rename worksheet
		$this->_xls->getActiveSheet()->setTitle('Hoja1');


		// HOJA 2
		$this->_xls->createSheet(1);
		// Add some data
		$this->_xls->setActiveSheetIndex(1)
		            ->setCellValue('A1', 'Hello')
		            ->setCellValue('B2', 'world!')
		            ->setCellValue('C1', 'Hello')
		            ->setCellValue('D2', 'world!');

		// Miscellaneous glyphs, UTF-8
		$this->_xls->setActiveSheetIndex(1)
		            ->setCellValue('A4', 'Miscellaneous glyphs')
		            ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

		// Rename worksheet
		$this->_xls->getActiveSheet()->setTitle('Hoja2');


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$this->_xls->setActiveSheetIndex(0);

		$this->getHeaders();

		$objWriter = \PHPExcel_IOFactory::createWriter($this->_xls, 'Excel5');
		$objWriter->save('php://output');
	}

	public function setData($sheet=0, $headers=array(), $data=array())
	{
		// Añador propiedades
		$this->setProperties();

		// Crear lña hoja de trabajo
		$this->_xls->createSheet($sheet);

		// Obtenemosm la hoja de trabajo
		$this->_xls->setActiveSheetIndex($sheet);

		// Asignamos un nombre a la hoja de trabajo
		$this->_xls->getActiveSheet()->setTitle(utf8_encode($data[0]['nombre_grupo']));

		// Insertar los header de la tabla
		foreach($headers as $key => $field):
			$this->_xls->getActiveSheet()->setCellValue($this->fields[$key+1]."2", $field);
		endforeach;

		// Insertar los datos de la tabla
		foreach($data as $key => $info):
			$this->_xls->getActiveSheet()->setCellValue("B".($key+3), $info['idstudents']);
			$this->_xls->getActiveSheet()->setCellValue("C".($key+3), utf8_encode($info['primer_ape_alu']." ".$info['segundo_ape_alu']));
			$this->_xls->getActiveSheet()->setCellValue("D".($key+3), utf8_encode($info['primer_nom_alu']." ".$info['segundo_nom_alu']));
			$this->_xls->getActiveSheet()->setCellValue("E".($key+3), utf8_encode($info['nombre_grupo']));
			$this->_xls->getActiveSheet()->setCellValue("F".($key+3), utf8_encode($info['jornada']));
		endforeach;

	}

	public function donwload()
	{
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$this->_xls->setActiveSheetIndex(0);

		// Obtenemos los headers
		$this->getHeaders();

		$objWriter = \PHPExcel_IOFactory::createWriter($this->_xls, 'Excel5');
		$objWriter->save('php://output');
	}
}
?>