<?php

namespace App\ModelPDF;

use Lib\merge\FPDF as FPDF;
/**
* 
*/
class EvaluationSheetPDF extends FPDF
{	
	private $_prefixHeader = "_header";
	private $_prefixSubHeader = '_subHeader';

	private $_prefixShowData = '_showData';
	public $headerInfo= array(
			'desempeño cognitivo' => array(
				1 =>	' ',
				2 =>	' ',
				3 =>	' ',
				4 =>	' ',
				5 =>	' '
			),
			'desempeño social' => array(
				1 =>	'ReD',
				2 =>	'Lid',
				3 =>	'Per',
				4 =>	'Com',
				5 =>	'Cco'
			),
			'desempeño personal' => array(
				1 =>	'Par',
				2 =>	'Asis',
				3 =>	'Ppe',
				4 =>	'ReN',
				5 =>	'PCT'
			)
		);
	public $subHeader = array();

	public $tipo = 'Planilla de evaluacion';
	public $model = '';
	public $maxPeriod = 0;
	public $infoGroupAndAsig = array();
	public $institution = array();

	private $_with_C_S = 80; //Ancho de la celda del nombre del estudiante
	private $_with_C_N_E_P = 8; //Ancho de la celda novedad (NOV) y estatus (EST) y Periodo (P)
	private $_with_C_H = 50; //Ancho de la celda donde estan los header (Desempeños)

	function Header(){
		$pic = 'data:image/png;base64,'.base64_encode($this->institution["logo_byte"]);
		$info = getimagesize($pic);

	    // Logo
	    $this->Image($pic, 6, 4, 20, 20, 'png');
		    
	    // Marca de agua

	    //Marco
	    $this->Cell(0, 24, '', 1,0);
	    $this->Ln(0);

	    // PRIMERA LINEA
	    $this->SetFont('Arial','B',12);
	    // Movernos a la dereca
	    $this->Cell(90, 6, '', 0,0);
	    // Título
	    $this->Cell(120, 6, $this->institution['nombre_inst'], 0, 0, 'C');
	    // Movernos a la derecha
	    $this->Cell(0, 6, '', 0,0);
	    // Salto de línea
	    $this->Ln(6);

	    // SEGUNDA LINEA
	    $this->SetFont('Arial','B',9);
	    $this->Cell(90, 4, '', 0,0);
	    // Título
	    $this->Cell(120,4, strtoupper($this->infoGroupAndAsig['sede']), 0, 0, 'C');
	    // Movernos a la derecha
	    $this->Cell(0, 4, '', 0,0);
	    // Salto de línea
	    $this->Ln(4);

	    // TERCERA LINEA
	    $this->Cell(90, 4, '', 0,0);
	    // Título
	    $this->Cell(120, 4, strtoupper($this->tipo), 0, 0, 'C');
	    // Movernos a la derecha
	    $this->Cell(0, 4, '', 0,0);
	    // Salto de línea
	    $this->Ln(4);

	    // CUARTA LINEA
	    $this->SetFont('Arial','',8);
	    // Movernos a la derecha
	    $this->Cell(25, 4, '', 0,0);
	    $this->Cell(65, 4, 'GRUPO: '.$this->infoGroupAndAsig['nombre_grupo'], 0, 0, 'L');
	    // Título
	    $this->Cell(120,4, ' DIRECTOR DE GRUPO: '.$this->infoGroupAndAsig['director_grupo'], 0, 0, 'C');
	    // Movernos a la derecha
	    $this->Cell(0, 4, 'MES_____________________', 0,0);
	    // Salto de línea
	    $this->Ln(4);

	    // QUINTA LINEA
	    // Movernos a la derecha
	    $this->Cell(25, 4, '', 0,0);
	    $this->Cell(65, 4, 'ASIGNATURA: '.$this->infoGroupAndAsig['asignatura'], 0, 0, 'L');
	    // Título
	    $this->Cell(120,4, 'DOCENTE: '.$this->infoGroupAndAsig['docente'], 0, 0, 'C');
	    // Movernos a la derecha
	    $this->Cell(0, 4, utf8_decode('AÑO LECTIVO ').date('Y'), 0,0);
	    // Salto de línea
	    $this->Ln(8);

	    
	    if(method_exists($this, $this->_prefixHeader.$this->model))
	    	call_user_method($this->_prefixHeader.$this->model, $this);
	}

	private function _headerm5(){
		// 
		$this->SetFillColor(135, 206, 235);
		// 
		$this->SetFont('Arial','B',9);

		// 
		$this->Cell( ( ($this->_with_C_N_E_P * 2 ) + ($this->_with_C_N_E_P * $this->maxPeriod) + $this->_with_C_S) , 4, '', 1,0, 'C', true);

		foreach($this->headerInfo as $key => $value){
			$this->Cell($this->_with_C_H, 4, utf8_decode(ucwords($key)), 1,0, 'C', true);
		}

		$this->Ln(4);

		if(method_exists($this, $this->_prefixSubHeader.$this->model))
			call_user_func(array($this, $this->_prefixSubHeader.$this->model));

	}

	private function _subHeaderm5(){

		$this->Cell($this->_with_C_S, 4, 'APELLIDOS Y NOMBRES DE ESTUDIANTE', 1,0, 'C', true);
		$this->Cell($this->_with_C_N_E_P, 4, 'NOV', 1, 0, 'C', true);
		$this->Cell(8, 4, 'EST', 1, 0, 'C', true);

		for ($i=0; $i < $this->maxPeriod; $i++) { 
			$this->Cell($this->_with_C_N_E_P, 4, 'P '.($i+1), 1, 0, 'C', true);			
		}

		foreach($this->headerInfo as $key => $value){
			foreach($value as $key2 => $value2)
				$this->Cell( $this->_with_C_H / count($value) , 4, utf8_decode(ucwords($value2)), 1,0, 'C', true);
		}

		$this->Ln(4);
	}

	private function _headerm6(){
		// $this->Cell(0, 4, 'el metodo m6', 0,0);	
	}

	// 
	public function showData($data=array()){

		$this->SetFont('Arial','',9);

		if(method_exists($this, $this->_prefixShowData.$this->model))
			call_user_func([$this,$this->_prefixShowData.$this->model], $data);
	}

	private function _showDatam5($data=array()){

		foreach ($data as $key => $value) {
			if($value['estudiante'] != NULL){
				$this->Cell($this->_with_C_S, 4, ($key+1).' '.$value['estudiante'], 1,0, 'L', false);
				$this->Cell($this->_with_C_N_E_P, 4, '', 1, 0, 'C', false);
				$this->Cell($this->_with_C_N_E_P, 4, $value['estatus'], 1, 0, 'C', false);

				for ($i=0; $i < $this->maxPeriod; $i++) {
					if($value['periodo'.($i+1)] == NULL || $value['periodo'.($i+1)] == 0)	$this->Cell($this->_with_C_N_E_P, 4, '0.0', 1, 0, 'C', false);					 
					else
						$this->Cell($this->_with_C_N_E_P, 4, $value['periodo'.($i+1)], 1, 0, 'C', false);					
				}

				foreach($this->headerInfo as $key => $value){
					foreach($value as $key2 => $value2)
						$this->Cell( $this->_with_C_H / count($value) , 4, ' ', 1,0, 'C', false);
				}
				$this->Ln(4);
			}
		}
	}

	// Pie de página
	function Footer(){
	    // Posición: a 1,5 cm del final
	    $this->SetY(-15);
	    // Arial italic 8
	    $this->SetFont('Arial','I',8);
	    // Número de página
	    $this->Cell(0,10,utf8_decode('página ').$this->PageNo().'/{nb}',0,0,'C');
	}
}
?>