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
	public $subHeader = array();

	public $tipo = 'Planilla de evaluacion';
	public $evaluation_parameters = array();
	public $maxPeriod = 0;
	public $infoGroupAndAsig = array();
	public $institution = array();
	public $DC = array();
	public $DP = array();
	public $DS = array();

	private $_with_C_S = 78; //Ancho de la celda del nombre del estudiante
	private $_with_C_N_E_P = 8; //Ancho de la celda novedad (NOV) y estatus (EST) y Periodo (P)
	private $_with_C_H = 50; //Ancho de la celda donde estan los header (Desempeños)
	private $_with_A_E = 10; //Ancho para la celda de AE Cambio

	private $fontSizeHeader = 9;

	private $_with_title = 120;
	private $_with_DR = 120;
	private $_with_gr = 75;
	private $_with_cellSpace = 25;

	function Header(){
		
		if($this->institution['logo_byte'] != NULL)
		{
			$pic = 'data:image/png;base64,'.base64_encode($this->institution["logo_byte"]);
			$info = getimagesize($pic);

		    // Logo
		    $this->Image($pic, 12, 14, 15, 15, 'png');
		}
		    
	    // Marca de agua

	    //Marco
	    $this->Cell(0, 24, '', 1,0);
	    $this->Ln(0);

	    // PRIMERA LINEA
	    $this->SetFont('Arial','B',12);
	    // Movernos a la dereca
	    $this->Cell(90, 6, '', 0,0);
	    
	    if($this->DefOrientation == 'P')
	    {	
	    	// $this->_with_C_S = 100;
	    	$this->_with_title = 10;
	    	$this->_with_gr = 54;
	    	$this->_with_doc = 65;
	    	$this->_with_C_H = 48;
	    	$this->fontSizeHeader = 7;
	    	$this->_with_DR = 90;
			$this->_with_cellSpace = 17;
	    }
	    // Título
	    $this->Cell($this->_with_title, 6, $this->institution['nombre_inst'], 0, 0, 'C');
	    

	    // Movernos a la derecha
	    $this->Cell(0, 6, '', 0,0);
	    // Salto de línea
	    $this->Ln(6);

	    // SEGUNDA LINEA
	    $this->SetFont('Arial','B',$this->fontSizeHeader);
	    $this->Cell(90, 4, '', 0,0);
	    // Título
	    $this->Cell($this->_with_title,4, strtoupper($this->infoGroupAndAsig['sede']), 0, 0, 'C');
	    // Movernos a la derecha
	    $this->Cell(0, 4, '', 0,0);
	    // Salto de línea
	    $this->Ln(4);

	    // TERCERA LINEA
	    $this->Cell(90, 4, '', 0,0);
	    // Título
	    $this->Cell($this->_with_title, 4, strtoupper($this->tipo), 0, 0, 'C');
	    // Movernos a la derecha
	    $this->Cell(0, 4, '', 0,0);
	    // Salto de línea
	    $this->Ln(4);

	    // CUARTA LINEA
	    $this->SetFont('Arial','',$this->fontSizeHeader);
	    // Movernos a la derecha
	    $this->Cell($this->_with_cellSpace, 4, '', 0,0);
	    $this->Cell($this->_with_gr, 4, 'GRUPO: '.$this->infoGroupAndAsig['nombre_grupo'], 0, 0, 'L');
	    // Título
	    $this->Cell($this->_with_DR,4, 'DIRECTOR DE GRUPO: '.
					$this->infoGroupAndAsig['dir_primer_nomb']." ".
	    			$this->infoGroupAndAsig['dir_segundo_nomb']." ".
	    			$this->infoGroupAndAsig['dir_primer_ape']." ".
	    			$this->infoGroupAndAsig['dir_segundo_ape'], 0, 0, 'L');
	    // Movernos a la derecha
	    $this->Cell(0, 4, 'FECHA: ___________', 0,0,'L');
	    // Salto de línea
	    $this->Ln(4);

	    // QUINTA LINEA
	    // Movernos a la derecha
	    $this->Cell($this->_with_cellSpace, 4, '', 0,0);
	    $this->Cell($this->_with_gr, 4, substr('ASIGNATURA: '.$this->infoGroupAndAsig['asignatura'], 0,35), 0,0);
	    // $this->Cell(65, 4, 'ASIGNATURA: '.$this->infoGroupAndAsig['asignatura'], 0, 0, 'L');
	    // Título
	    $this->Cell($this->_with_DR,4, 'DOCENTE: '.
					$this->infoGroupAndAsig['doc_primer_nomb']." ".
	    			$this->infoGroupAndAsig['doc_segundo_nomb']." ".
	    			$this->infoGroupAndAsig['doc_primer_ape']." ".
	    			$this->infoGroupAndAsig['doc_segundo_ape'], 0, 0, 'L');
		
	    // Movernos a la derecha
	    $this->Cell(0, 4, utf8_decode('AÑO LECTIVO ').date('Y'), 0,0,'L');
	    // Salto de línea
	    $this->Ln(8);

	    $this->_configMargin();
	    $this->_header();
	}

	private function _configMargin()
	{
		switch ($this->maxPeriod) {
			case 1:
				$this->_with_C_S = 85;
				// $this->_with_C_H = 44;
				break;
			case 2:
				$this->_with_C_S = 78;
				// $this->_with_C_H = 60;
				break;
			case 3:
				$this->_with_C_S = 78;
				// $this->_with_C_H = 60;
				break;
			case 4:
				$this->_with_C_S = 78;
				// $this->_with_C_H = 60;
				break;
		}

		if($this->DefOrientation == 'P')
	    {	
	    	$this->_with_C_S = 80;
	    }
	}
	private function _header()
	{
		// 
		$this->SetFillColor(135, 206, 235);
		// 
		$this->SetFont('Arial','B',9);

		// 
		$this->Cell( ( ($this->_with_C_N_E_P * 2 ) + ($this->_with_C_N_E_P * $this->maxPeriod) + $this->_with_C_S) , 4, '', 1,0, 'C', true);

		// Recorremos los parametros de evaluacion
		foreach ($this->evaluation_parameters as $key => $value) {

            if($value['parametro'] == 'AEE')
            {
            $this->Cell($this->_with_A_E, 4, (ucwords($value['parametro'])), 1,0, 'C', true);
            }
            else{

            	$this->Cell($this->_with_C_H, 4, (ucwords($value['parametro'])), 1,0, 'C', true);
            }
        }

		$this->Cell(0, 8, 'Val', 1,0, 'C', true); //Cambio alto 8

		$this->Ln(4);

		$this->_subHeader();
	}

	private function _subHeader(){

		$this->Cell($this->_with_C_S, 4, 'APELLIDOS Y NOMBRES DE ESTUDIANTE', 1,0, 'C', true);
		$this->Cell($this->_with_C_N_E_P, 4, 'NOV', 1, 0, 'C', true);
		$this->Cell(8, 4, 'EST', 1, 0, 'C', true);

		for ($i=0; $i < $this->maxPeriod; $i++) { 
			$this->Cell($this->_with_C_N_E_P, 4, 'P'.($i+1), 1, 0, 'C', true);			
		}

		foreach ($this->evaluation_parameters as $key => $value) {
			
			if(count($value['indicadores']) == 0)
			{
				for ($i=0; $i < 5; $i++) { 
					$this->Cell( $this->_with_C_H / 5 , 4, '', 1,0, 'C', true);
				}
			}
			foreach ($value['indicadores'] as $keyInd => $valueInd) {

				 if($value['parametro'] == 'AEE')
            	{
            		$this->Cell( $this->_with_A_E , 4, $valueInd['abreviacion'], 1,0, 'C', true);	
            	}else
            	{
            		$this->Cell( $this->_with_C_H / count($value['indicadores']) , 4, $valueInd['abreviacion'], 1,0, 'C', true); //Cambio $this->_with_C_H / 5	
            	}
			}
		}

		$this->Ln(4);
	}

	// 
	public function showData($data=array()){

		$this->SetFont('Arial','',9);

		// Recorremos cada estududiante
		foreach ($data as $key => $value) {
			// Preguntamos si el estudiante es diferente de nulo
			if($value['alu_primer_nom'] != NULL){

				// Se imprime el nombre
				if($key < 9)
				{
					$this->Cell($this->_with_C_S, 4, '0'.($key+1).' '.
						$value['alu_primer_ape'].' '.
						$value['alu_segundo_ape'].' '.
						$value['alu_primer_nom'].' '.
						$value['alu_segundo_nom'], 1,0, 'L', false);
				}
				else
				{
					$this->Cell($this->_with_C_S, 4, ($key+1).' '.
						$value['alu_primer_ape'].' '.
						$value['alu_segundo_ape'].' '.
						$value['alu_primer_nom'].' '.
						$value['alu_segundo_nom'], 1,0, 'L', false);
				}
				
				// Se imprime la celda de la novedad
				$this->Cell($this->_with_C_N_E_P, 4, '', 1, 0, 'C', false);
				// Se imprime el status
				
				if($value['estatus'] != 'C'){
					$this->Cell($this->_with_C_N_E_P, 4, $value['estatus'], 1, 0, 'C', false);
				}else{
					$this->Cell($this->_with_C_N_E_P, 4, '', 1, 0, 'C', false);
				}
				// Recorremos los periodos del estudiante
				for ($i=0; $i < $this->maxPeriod; $i++) {

					// Preguntamos si la nota es iguala 0
					if($value['periodo'.($i+1)] == NULL || $value['periodo'.($i+1)] == 0)	$this->Cell($this->_with_C_N_E_P, 4, '', 1, 0, 'C', false);					 
					else
						$this->Cell($this->_with_C_N_E_P, 4, $value['periodo'.($i+1)], 1, 0, 'C', false);					
				}

				foreach ($this->evaluation_parameters as $key => $value) {
			
					if(count($value['indicadores']) == 0)
					{
						for ($i=0; $i < 5; $i++) { 
							$this->Cell( $this->_with_C_H / 5 , 4, '', 1,0, 'C', false);
						}
					}
					foreach ($value['indicadores'] as $keyInd => $valueInd) {
						if($value['id_parametro'] == 4)
		            	{
		            		$this->Cell( $this->_with_A_E , 4, '', 1,0, 'C', false);	
		            	}else
		            	{
		            		$this->Cell( $this->_with_C_H / count($value['indicadores']) , 4, '', 1,0, 'C', false);	
		            	}
						
					}
				}
				$this->Cell(0,4,'',1,0);

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
	    $this->Cell(0,10,utf8_decode('Ágora - Página ').$this->PageNo(),0,0,'C');
	}
}
?>